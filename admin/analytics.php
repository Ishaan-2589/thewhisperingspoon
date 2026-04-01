<?php
session_start();
require_once "../includes/db.php";
require_once "../includes/admin-auth.php";

// Set default month and year to current, or get from filter form
$selectedMonth = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
$selectedYear = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

// 1. Get Overall Summary for selected period (Excluding Cancelled orders)
$summaryQuery = "SELECT COUNT(id) as total_orders, SUM(total_amount) as total_revenue 
                 FROM orders 
                 WHERE MONTH(created_at) = ? AND YEAR(created_at) = ? AND status != 'Cancelled'";
$stmtSum = mysqli_prepare($conn, $summaryQuery);
mysqli_stmt_bind_param($stmtSum, "ii", $selectedMonth, $selectedYear);
mysqli_stmt_execute($stmtSum);
$summary = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtSum));
mysqli_stmt_close($stmtSum);

$totalOrders = $summary['total_orders'] ?? 0;
$totalRevenue = $summary['total_revenue'] ?? 0;

// 2. Get Best Sellers Data
$itemsQuery = "SELECT mi.name, mi.category, SUM(oi.quantity) as total_sold, SUM(oi.quantity * oi.price) as item_revenue
               FROM order_items oi
               JOIN orders o ON oi.order_id = o.id
               JOIN menu_items mi ON oi.menu_item_id = mi.id
               WHERE MONTH(o.created_at) = ? AND YEAR(o.created_at) = ? AND o.status != 'Cancelled'
               GROUP BY mi.id
               ORDER BY total_sold DESC";
$stmtItems = mysqli_prepare($conn, $itemsQuery);
mysqli_stmt_bind_param($stmtItems, "ii", $selectedMonth, $selectedYear);
mysqli_stmt_execute($stmtItems);
$itemsResult = mysqli_stmt_get_result($stmtItems);

$reportData = [];
$chartLabels = [];
$chartData = [];
$totalItemsSold = 0;

$count = 0;
while ($row = mysqli_fetch_assoc($itemsResult)) {
    $reportData[] = $row;
    $totalItemsSold += $row['total_sold'];
    
    // Grab top 5 for the chart
    if ($count < 5) {
        $chartLabels[] = $row['name'];
        $chartData[] = $row['total_sold'];
        $count++;
    }
}
mysqli_stmt_close($stmtItems);

$topDish = count($reportData) > 0 ? $reportData[0]['name'] : "N/A";

// Arrays for the form dropdowns
$months = [
    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
    5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
];

// Generate years (from a reasonable start year up to current + 1)
$currentYear = (int)date('Y');
$years = range($currentYear - 2, $currentYear + 1);

?>

<?php include "../includes/admin-header.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body { background-color: #050505; color: #fff; }
.admin-container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid #333; padding-bottom: 20px; }
.page-header h1 { color: gold; font-family: 'Playfair Display', serif; font-size: 32px; margin: 0; }

.filter-bar { background: #111; padding: 20px; border-radius: 12px; border: 1px solid #222; margin-bottom: 30px; display: flex; align-items: center; justify-content: space-between;}
.filter-form { display: flex; gap: 15px; align-items: center; }
.filter-select { background: #000; color: #fff; border: 1px solid #444; padding: 10px 15px; border-radius: 6px; font-size: 14px; outline: none;}
.filter-select:focus { border-color: gold; }
.btn-filter { background: gold; color: #000; border: none; padding: 10px 20px; border-radius: 6px; font-weight: bold; cursor: pointer; text-transform: uppercase; letter-spacing: 1px; transition: 0.3s;}
.btn-filter:hover { background: #ffcc00; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(255,215,0,0.2);}

.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px; }
.stat-card { background: linear-gradient(145deg, #1a1a1a, #111); border: 1px solid #333; border-radius: 12px; padding: 25px; text-align: center; border-top: 3px solid gold;}
.stat-number { font-size: 32px; color: gold; font-family: 'Playfair Display', serif; font-weight: bold; margin-bottom: 5px; }
.stat-label { color: #888; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; }

.dashboard-layout { display: grid; grid-template-columns: 1fr 2fr; gap: 30px; }

@media (max-width: 900px) { .dashboard-layout { grid-template-columns: 1fr; } .filter-bar { flex-direction: column; gap: 15px; align-items: stretch;} .filter-form { flex-direction: column; } }

.chart-container { background: #111; border: 1px solid #222; border-radius: 12px; padding: 20px; }
.table-wrapper { background: #111; border: 1px solid #222; border-radius: 12px; overflow: hidden; }
table { width: 100%; border-collapse: collapse; text-align: left; }
th { background: #1a1a1a; color: gold; padding: 15px 20px; font-size: 12px; text-transform: uppercase; border-bottom: 2px solid #333; }
td { padding: 15px 20px; border-bottom: 1px solid #222; color: #ccc; font-size: 14px; }
tr:hover { background: #151515; }
.category-badge { background: #222; color: #aaa; padding: 4px 10px; border-radius: 20px; font-size: 11px; text-transform: uppercase; }
</style>

<div class="admin-container">
    <div class="page-header">
        <h1><i class="fas fa-chart-pie" style="margin-right: 10px; font-size: 24px; color: #555;"></i> Sales Analytics</h1>
        <a href="dashboard.php" style="color: #888; text-decoration: none; text-transform: uppercase; font-size: 14px;"><i class="fas fa-arrow-left"></i> Back</a>
    </div>

    <div class="filter-bar">
        <h3 style="margin: 0; color: #fff; font-size: 18px;">Report Period</h3>
        <form method="GET" class="filter-form">
            <select name="month" class="filter-select">
                <?php foreach ($months as $num => $name): ?>
                    <option value="<?php echo $num; ?>" <?php echo $num === $selectedMonth ? 'selected' : ''; ?>>
                        <?php echo $name; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="year" class="filter-select">
                <?php foreach ($years as $year): ?>
                    <option value="<?php echo $year; ?>" <?php echo $year === $selectedYear ? 'selected' : ''; ?>>
                        <?php echo $year; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="btn-filter">Generate Report</button>
        </form>
    </div>

    <div class="stats-grid">
        <div class="stat-card" style="border-top-color: #00ff88;">
            <div class="stat-number" style="color: #00ff88;">₹<?php echo number_format($totalRevenue, 2); ?></div>
            <div class="stat-label">Period Revenue</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo number_format($totalOrders); ?></div>
            <div class="stat-label">Total Orders</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo number_format($totalItemsSold); ?></div>
            <div class="stat-label">Dishes Prepared</div>
        </div>
        <div class="stat-card" style="border-top-color: #00c3ff;">
            <div class="stat-number" style="color: #00c3ff; font-size: 22px; margin-top: 10px;"><?php echo htmlspecialchars($topDish); ?></div>
            <div class="stat-label">Best Seller</div>
        </div>
    </div>

    <div class="dashboard-layout">
        <div class="chart-container">
            <h3 style="color: gold; margin-top: 0; border-bottom: 1px solid #333; padding-bottom: 15px; font-family: 'Playfair Display', serif;">Top 5 Dishes</h3>
            <?php if (empty($chartData)): ?>
                <p style="color: #666; text-align: center; padding: 40px 0;">No sales data for this period.</p>
            <?php else: ?>
                <div style="position: relative; height: 300px; width: 100%;">
                    <canvas id="topItemsChart"></canvas>
                </div>
            <?php endif; ?>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Dish Name</th>
                        <th>Category</th>
                        <th style="text-align: center;">Units Sold</th>
                        <th style="text-align: right;">Total Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($reportData)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 30px; color: #666;">No items sold in this period.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($reportData as $row): ?>
                            <tr>
                                <td style="color: #fff; font-weight: bold;"><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><span class="category-badge"><?php echo htmlspecialchars($row['category']); ?></span></td>
                                <td style="text-align: center; color: gold; font-weight: bold;"><?php echo $row['total_sold']; ?></td>
                                <td style="text-align: right; color: #00ff88;">₹<?php echo number_format($row['item_revenue'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (!empty($chartData)): ?>
    const ctx = document.getElementById('topItemsChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($chartLabels); ?>,
            datasets: [{
                label: 'Units Sold',
                data: <?php echo json_encode($chartData); ?>,
                backgroundColor: [
                    'rgba(255, 215, 0, 0.8)',
                    'rgba(255, 215, 0, 0.6)',
                    'rgba(255, 215, 0, 0.4)',
                    'rgba(255, 215, 0, 0.3)',
                    'rgba(255, 215, 0, 0.2)'
                ],
                borderColor: 'gold',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255, 255, 255, 0.05)' },
                    ticks: { color: '#888', stepSize: 1 }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#ccc', font: { size: 11 } }
                }
            }
        }
    });
    <?php endif; ?>
});
</script>

<?php include "../includes/footer.php"; ?>