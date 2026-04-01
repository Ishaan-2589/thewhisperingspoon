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
    $dates[$date] = 0; 
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

$chartLabels = json_encode(array_map(function($d) { return date('M d', strtotime($d)); }, array_keys($dates)));
$chartData = json_encode(array_values($dates));

$statusColors = [
    'Pending'   => '#ffcc00', 
    'Preparing' => '#00c3ff', 
    'Ready'     => '#00ff88', 
    'Delivered' => '#888888',
    'Cancelled' => '#ff4444'
];
?>

<?php include "../includes/admin-header.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
/* Fix for the Dashboard Quick Links */
.dashboard-links {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.dashboard-link {
    background: #111;
    border: 1px solid #333;
    border-radius: 12px;
    padding: 25px 20px;
    text-align: center;
    color: #ccc;
    text-decoration: none;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    font-family: 'Playfair Display', serif;
    font-size: 18px;
    letter-spacing: 1px;
}

.dashboard-link:hover {
    border-color: gold;
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(255, 215, 0, 0.05);
    color: #fff;
}

.dashboard-link i {
    color: gold;
    font-size: 32px;
    margin-bottom: 12px;
    transition: transform 0.3s ease;
}

.dashboard-link:hover i {
    transform: scale(1.1);
}

/* Base Dashboard Styles */
.admin-body { max-width: 1200px; margin: 0 auto; padding: 40px 20px; }
.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 40px; }
.stat-card { background: linear-gradient(145deg, #1a1a1a, #111); border: 1px solid #333; border-radius: 12px; padding: 25px; text-align: center; border-top: 3px solid gold; box-shadow: 0 10px 30px rgba(0,0,0,0.5);}
.stat-number { font-size: 36px; color: gold; font-family: 'Playfair Display', serif; font-weight: bold; margin-bottom: 5px; }
.stat-label { color: #888; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; }
</style>

<div class="admin-body">

    <div class="dashboard-links">
        <a href="orders.php" class="dashboard-link">
            <i class="fas fa-receipt"></i> 
            Manage Orders
        </a>
        <a href="menu.php" class="dashboard-link">
            <i class="fas fa-utensils"></i> 
            Manage Menu
        </a>
        <a href="users.php" class="dashboard-link">
            <i class="fas fa-users"></i> 
            Customer Base
        </a>
        <a href="bookings.php" class="dashboard-link">
            <i class="fas fa-chair"></i> 
            Table Reservations
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
        <div class="stat-card" style="border-top-color: #00ff88;">
            <div class="stat-number" style="color: #00ff88;">₹<?php echo number_format($totalRevenue, 2); ?></div>
            <div class="stat-label">Net Revenue</div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px; margin-top: 30px;">
        
        <div style="background: #111; padding: 25px; border-radius: 12px; border: 1px solid #222;">
            <h3 style="font-family: 'Playfair Display', serif; font-size: 24px; margin-top: 0; margin-bottom: 20px; color: gold; border-bottom: 1px solid #333; padding-bottom: 10px;">Live Operations</h3>
            <div style="display: flex; flex-direction: column; gap: 15px;">
                <?php foreach ($statusColors as $status => $color): 
                    $count = $statusCounts[$status] ?? 0;
                ?>
                    <div style="background: #0a0a0a; padding: 15px; border-radius: 8px; border-left: 5px solid <?php echo $color; ?>; display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #222; border-right: 1px solid #222; border-bottom: 1px solid #222;">
                        <span style="font-weight: bold; color: #ccc; text-transform: uppercase; font-size: 13px; letter-spacing: 1px;"><?php echo $status; ?></span>
                        <span style="font-size: 20px; color: <?php echo $color; ?>; font-weight: bold;"><?php echo $count; ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div style="background: #111; padding: 25px; border-radius: 12px; border: 1px solid #222;">
            <h3 style="font-family: 'Playfair Display', serif; font-size: 24px; margin-top: 0; margin-bottom: 20px; color: gold; border-bottom: 1px solid #333; padding-bottom: 10px;">7-Day Revenue Trend</h3>
            <div style="position: relative; height: 250px; width: 100%;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    
    let gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(255, 215, 0, 0.4)'); 
    gradient.addColorStop(1, 'rgba(255, 215, 0, 0.0)'); 

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo $chartLabels; ?>,
            datasets: [{
                label: 'Daily Revenue (₹)',
                data: <?php echo $chartData; ?>,
                borderColor: '#ffd700',
                backgroundColor: gradient,
                borderWidth: 3,
                pointBackgroundColor: '#111',
                pointBorderColor: '#ffd700',
                pointBorderWidth: 2,
                pointRadius: 4,
                fill: true,
                tension: 0.4 
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#888' } },
                x: { grid: { display: false }, ticks: { color: '#888' } }
            }
        }
    });
});
</script>

<?php include "../includes/footer.php"; ?>