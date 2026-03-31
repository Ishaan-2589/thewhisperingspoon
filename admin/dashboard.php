<?php
session_start();
require_once "../includes/db.php";
require_once "../includes/admin-auth.php";

// 1. Get Core Dashboard Statistics
$totalOrders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM orders"))['count'];
$totalUsers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users"))['count'];
$totalMenuItems = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM menu_items"))['count'];
$totalRevenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_amount) as total FROM orders WHERE status != 'Cancelled'"))['total'] ?? 0;

// 2. Get Recent Orders
$recentOrdersQuery = "SELECT o.id, o.total_amount, o.status, o.created_at, u.name as user_name
                     FROM orders o
                     JOIN users u ON o.user_id = u.id
                     ORDER BY o.created_at DESC LIMIT 5";
$recentOrders = mysqli_query($conn, $recentOrdersQuery);

// 3. Get Order Status Counts
$statusCounts = [];
$statusQuery = "SELECT status, COUNT(*) as count FROM orders GROUP BY status";
$statusResult = mysqli_query($conn, $statusQuery);
while ($row = mysqli_fetch_assoc($statusResult)) {
    $statusCounts[$row['status']] = $row['count'];
}

// 4. Generate Data for Chart.js (Last 7 Days Revenue)
$dates = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $dates[$date] = 0; // Initialize with 0 so the chart doesn't break if a day had no sales
}

$chartQuery = "SELECT DATE(created_at) as order_date, SUM(total_amount) as daily_revenue 
               FROM orders 
               WHERE status != 'Cancelled' AND created_at >= DATE(NOW()) - INTERVAL 7 DAY
               GROUP BY DATE(created_at)";
$chartResult = mysqli_query($conn, $chartQuery);
if ($chartResult) {
    while($row = mysqli_fetch_assoc($chartResult)) {
        $dates[$row['order_date']] = (float)$row['daily_revenue'];
    }
}

// Format data for JavaScript
$chartLabels = json_encode(array_map(function($d) { return date('M d', strtotime($d)); }, array_keys($dates)));
$chartData = json_encode(array_values($dates));

// Define theme colors for status badges
$statusColors = [
    'Pending'   => '#ffcc00', 
    'Preparing' => '#00c3ff', 
    'Ready'     => '#00ff88', 
    'Delivered' => '#888888',
    'Cancelled' => '#ff4444'
];
?>

<?php include "../includes/header.php"; ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="dashboard-container admin-body">
    <header style="background: transparent; border: none; padding: 20px 0; text-align: center;">
        <h1 class="logo-font" style="font-size: 50px;">Admin Central</h1>
        <p style="color: #888; letter-spacing: 2px;">MANAGING THE WHISPERING SPOON</p>
    </header>

    <div class="dashboard-links" style="margin-bottom: 40px;">
        <a href="orders.php" class="dashboard-link">
            <i class="fas fa-receipt" style="display:block; font-size: 28px; margin-bottom: 8px;"></i> 
            Manage Orders
        </a>
        <a href="menu.php" class="dashboard-link">
            <i class="fas fa-utensils" style="display:block; font-size: 28px; margin-bottom: 8px;"></i> 
            Manage Menu
        </a>
        <a href="users.php" class="dashboard-link">
            <i class="fas fa-users" style="display:block; font-size: 28px; margin-bottom: 8px;"></i> 
            Customer Base
        </a>
        <a href="bookings.php" class="dashboard-link">
            <i class="fas fa-chair" style="display:block; font-size: 28px; margin-bottom: 8px;"></i> 
            Reservations
        </a>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number"><?php echo number_format($totalOrders); ?></div>
            <div class="stat-label">Total Orders</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo number_format($totalUsers); ?></div>
            <div class="stat-label">Registered Users</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $totalMenuItems; ?></div>
            <div class="stat-label">Dishes on Menu</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" style="color: #00ff88 !important;">₹<?php echo number_format($totalRevenue, 2); ?></div>
            <div class="stat-label">Net Revenue</div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px; margin-top: 30px;">
        
        <div class="status-overview">
            <h3 class="heading-font" style="font-size: 28px; margin-bottom: 20px; color: gold;">Live Operations</h3>
            <div style="display: flex; flex-direction: column; gap: 15px;">
                <?php foreach ($statusColors as $status => $color): 
                    $count = $statusCounts[$status] ?? 0;
                ?>
                    <div style="background: #111; padding: 15px; border-radius: 8px; border-left: 5px solid <?php echo $color; ?>; display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: bold; color: #ccc;"><?php echo strtoupper($status); ?></span>
                        <span style="font-size: 20px; color: <?php echo $color; ?>; font-weight: bold;"><?php echo $count; ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="revenue-chart-container" style="background: #111; padding: 20px; border-radius: 8px; border: 1px solid #333;">
            <h3 class="heading-font" style="font-size: 28px; margin-bottom: 20px; color: gold;">7-Day Revenue Trend</h3>
            <div style="position: relative; height: 250px; width: 100%;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    <div class="recent-orders" style="margin-top: 30px;">
        <h3 class="heading-font" style="font-size: 28px; margin-bottom: 20px; color: gold;">Recent Activity</h3>
        <div class="orders-table">
            <table style="width: 100%;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($recentOrders) > 0): ?>
                        <?php while ($order = mysqli_fetch_assoc($recentOrders)): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td style="color: gold;"><i class="fas fa-user-circle" style="margin-right: 5px; color: #555;"></i> <?php echo htmlspecialchars($order['user_name']); ?></td>
                                <td>₹<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo strtolower($order['status']); ?>" style="border: 1px solid <?php echo $statusColors[$order['status']] ?? '#fff'; ?>; color: <?php echo $statusColors[$order['status']] ?? '#fff'; ?>; background: transparent;">
                                        <?php echo $order['status']; ?>
                                    </span>
                                </td>
                                <td style="font-size: 13px; color: #aaa;">
                                    <?php echo date('H:i', strtotime($order['created_at'])); ?> 
                                    <span style="color: #666; margin-left: 5px;"><?php echo date('d M', strtotime($order['created_at'])); ?></span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 30px; color: #666;">No recent orders found. The kitchen is quiet!</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    
    // Create a golden gradient for the chart area
    let gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(255, 215, 0, 0.4)'); // Gold transparent at top
    gradient.addColorStop(1, 'rgba(255, 215, 0, 0.0)'); // Fades to transparent at bottom

    const revenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo $chartLabels; ?>,
            datasets: [{
                label: 'Daily Revenue (₹)',
                data: <?php echo $chartData; ?>,
                borderColor: '#ffd700', // Solid gold line
                backgroundColor: gradient,
                borderWidth: 3,
                pointBackgroundColor: '#111',
                pointBorderColor: '#ffd700',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true,
                tension: 0.4 // Makes the line smooth/curved
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false // Hide the legend to keep it clean
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: 'gold',
                    bodyColor: '#fff',
                    borderColor: 'gold',
                    borderWidth: 1,
                    callbacks: {
                        label: function(context) {
                            return ' ₹' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(255, 255, 255, 0.05)',
                        drawBorder: false,
                    },
                    ticks: {
                        color: '#888',
                        callback: function(value) {
                            return '₹' + value;
                        }
                    }
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false,
                    },
                    ticks: {
                        color: '#888'
                    }
                }
            }
        }
    });
});
</script>

<?php include "../includes/footer.php"; ?>