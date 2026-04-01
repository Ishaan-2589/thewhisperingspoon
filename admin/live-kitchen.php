<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Kitchen - The Whispering Spoon</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { margin: 0; padding: 0; background: #000; color: #fff; font-family: 'Roboto', sans-serif; overflow-x: hidden; }
        .kds-header { background: #111; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid gold; }
        .kds-header h1 { margin: 0; color: gold; font-size: 24px; text-transform: uppercase; letter-spacing: 2px; }
        .live-indicator { display: flex; align-items: center; gap: 10px; color: #00ff88; font-weight: bold; }
        .pulse { width: 12px; height: 12px; background: #00ff88; border-radius: 50%; animation: blink 1.5s infinite; }
        @keyframes blink { 0% { opacity: 1; } 50% { opacity: 0.2; } 100% { opacity: 1; } }
        .board { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; padding: 20px; height: calc(100vh - 80px); }
        .column { background: #0a0a0a; border: 1px solid #333; border-radius: 12px; display: flex; flex-direction: column; overflow: hidden; }
        .col-header { padding: 15px; text-align: center; font-size: 20px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .col-pending .col-header { background: rgba(255, 204, 0, 0.1); color: #ffcc00; border-bottom: 2px solid #ffcc00; }
        .col-preparing .col-header { background: rgba(0, 195, 255, 0.1); color: #00c3ff; border-bottom: 2px solid #00c3ff; }
        .col-ready .col-header { background: rgba(0, 255, 136, 0.1); color: #00ff88; border-bottom: 2px solid #00ff88; }
        .ticket-container { padding: 15px; overflow-y: auto; flex-grow: 1; }
        .ticket { background: #1a1a1a; border-left: 5px solid #fff; padding: 15px; border-radius: 8px; margin-bottom: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.5); }
        .ticket-header { display: flex; justify-content: space-between; margin-bottom: 10px; border-bottom: 1px dashed #444; padding-bottom: 10px; }
        .ticket-id { font-size: 20px; font-weight: bold; color: #fff; }
        .ticket-time { font-size: 16px; font-weight: bold; padding: 4px 8px; border-radius: 4px; }
        .time-safe { background: #222; color: #00ff88; }
        .time-warn { background: rgba(255, 204, 0, 0.2); color: #ffcc00; }
        .time-danger { background: rgba(255, 68, 68, 0.2); color: #ff4444; }
        .customer-name { color: #aaa; font-size: 14px; margin-bottom: 10px; text-transform: uppercase; }
        .ticket-items { list-style: none; padding: 0; margin: 0 0 15px 0; font-size: 18px; }
        .ticket-items li { margin-bottom: 8px; display: flex; gap: 10px; }
        .qty { color: gold; font-weight: bold; }
        .btn-action { width: 100%; padding: 12px; border: none; border-radius: 6px; font-size: 16px; font-weight: bold; cursor: pointer; text-transform: uppercase; }
        .btn-start { background: #00c3ff; color: #000; }
        .btn-finish { background: #00ff88; color: #000; }
        .btn-deliver { background: #333; color: #fff; border: 1px solid #555; }
    </style>
</head>
<body>

<div class="kds-header">
    <h1>Live Kitchen Display</h1>
    <div>
        <a href="orders.php" style="color: #aaa; text-decoration: none; margin-right: 20px;">← Back to Orders</a>
        <div class="live-indicator" style="display: inline-flex;">
            <div class="pulse"></div> Live Sync
        </div>
    </div>
</div>

<div class="board">
    <div class="column col-pending">
        <div class="col-header">New Orders (<span id="count-pending">0</span>)</div>
        <div class="ticket-container" id="col-pending-items"></div>
    </div>
    <div class="column col-preparing">
        <div class="col-header">Cooking (<span id="count-preparing">0</span>)</div>
        <div class="ticket-container" id="col-preparing-items"></div>
    </div>
    <div class="column col-ready">
        <div class="col-header">Ready (<span id="count-ready">0</span>)</div>
        <div class="ticket-container" id="col-ready-items"></div>
    </div>
</div>

<script>
function fetchLiveOrders() {
    fetch('kitchen-api.php?action=fetch')
        .then(response => response.json())
        .then(data => {
            if(data && data.status === 'success') {
                renderBoard(data.orders);
            }
        })
        .catch(err => console.error("KDS Sync Error:", err));
}

function renderBoard(orders) {
    document.getElementById('col-pending-items').innerHTML = '';
    document.getElementById('col-preparing-items').innerHTML = '';
    document.getElementById('col-ready-items').innerHTML = '';
    
    let counts = { 'Pending': 0, 'Preparing': 0, 'Ready': 0 };

    orders.forEach(order => {
        if (!counts.hasOwnProperty(order.status)) return; // Skip delivered/cancelled
        
        counts[order.status]++;
        
        let mins = order.minutes_waiting < 0 ? 0 : order.minutes_waiting;
        let timeClass = 'time-safe';
        if (mins > 15) timeClass = 'time-warn';
        if (mins > 30) timeClass = 'time-danger';

        let itemsHtml = order.items.map(item => `<li><span class="qty">${item.quantity}x</span> ${item.name}</li>`).join('');

        // --- NEW CODE START ---
        // This is where we create the red warning box if the user typed something in checkout
        let specialRequestHtml = '';
        if (order.special_requests && order.special_requests.trim() !== '') {
            specialRequestHtml = `
                <div style="background: rgba(255, 68, 68, 0.15); border: 1px solid #ff4444; color: #ffbbbb; padding: 10px; border-radius: 6px; margin-bottom: 15px; font-size: 14px; font-weight: bold;">
                    <i class="fas fa-exclamation-triangle" style="color: #ff4444; margin-right: 5px;"></i> 
                    ${order.special_requests}
                </div>
            `;
        }
        // --- NEW CODE END ---

        let actionBtn = '';
        if (order.status === 'Pending') {
            actionBtn = `<button class="btn-action btn-start" onclick="updateStatus(${order.id}, 'Preparing')">Start Cooking</button>`;
        } else if (order.status === 'Preparing') {
            actionBtn = `<button class="btn-action btn-finish" onclick="updateStatus(${order.id}, 'Ready')">Mark Ready</button>`;
        } else if (order.status === 'Ready') {
            actionBtn = `<button class="btn-action btn-deliver" onclick="updateStatus(${order.id}, 'Delivered')">Clear (Delivered)</button>`;
        }

        let borderColor = order.status === 'Pending' ? '#ffcc00' : (order.status === 'Preparing' ? '#00c3ff' : '#00ff88');

        // Notice that ${specialRequestHtml} is now injected right above the ul list
        let ticketHtml = `
            <div class="ticket" style="border-left-color: ${borderColor}">
                <div class="ticket-header">
                    <span class="ticket-id">#${String(order.id).padStart(5, '0')}</span>
                    <span class="ticket-time ${timeClass}">${mins}m</span>
                </div>
                <div class="customer-name">${order.customer_name || 'Guest'}</div>
                
                ${specialRequestHtml} 
                
                <ul class="ticket-items">
                    ${itemsHtml}
                </ul>
                ${actionBtn}
            </div>
        `;

        if (order.status === 'Pending') document.getElementById('col-pending-items').innerHTML += ticketHtml;
        if (order.status === 'Preparing') document.getElementById('col-preparing-items').innerHTML += ticketHtml;
        if (order.status === 'Ready') document.getElementById('col-ready-items').innerHTML += ticketHtml;
    });

    document.getElementById('count-pending').innerText = counts['Pending'];
    document.getElementById('count-preparing').innerText = counts['Preparing'];
    document.getElementById('count-ready').innerText = counts['Ready'];
}

function updateStatus(orderId, newStatus) {
    fetch('kitchen-api.php?action=update', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ order_id: orderId, status: newStatus })
    })
    .then(response => response.json())
    .then(data => {
        if(data && data.status === 'success') {
            fetchLiveOrders();
        }
    });
}

// Fetch immediately, then loop every 5 seconds
fetchLiveOrders();
setInterval(fetchLiveOrders, 5000);
</script>

</body>
</html>