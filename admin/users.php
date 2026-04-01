<?php
session_start();
require_once "../includes/db.php";
require_once "../includes/admin-auth.php";

// Get all users
$query = "SELECT * FROM users ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
$users = [];
while ($row = mysqli_fetch_assoc($result)) {
    $users[] = $row;
}
?>

<?php include "../includes/admin-header.php"; ?>

<style>
/* Luxury Admin Data Table Styles */
body { background-color: #050505; color: #fff; }
.admin-container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid #333; padding-bottom: 20px; }
.page-header h1 { color: gold; font-family: 'Playfair Display', serif; font-size: 32px; margin: 0; }
.btn-back { color: #888; text-decoration: none; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; transition: color 0.3s; }
.btn-back:hover { color: gold; }

.table-wrapper { background: #111; border: 1px solid #222; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
table { width: 100%; border-collapse: collapse; text-align: left; }
th { background: #1a1a1a; color: gold; padding: 18px 20px; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; border-bottom: 2px solid #333; }
td { padding: 18px 20px; border-bottom: 1px solid #222; color: #ccc; font-size: 15px; }
tr:hover { background: #151515; }
tr:last-child td { border-bottom: none; }

.user-avatar { width: 35px; height: 35px; background: #222; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; color: gold; font-weight: bold; margin-right: 15px; font-size: 14px; border: 1px solid #444;}
</style>

<div class="admin-container">
    <div class="page-header">
        <h1><i class="fas fa-users" style="margin-right: 10px; font-size: 24px; color: #555;"></i> Customer Database</h1>
        <a href="dashboard.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Customer Details</th>
                    <th>Email Address</th>
                    <th>System ID</th>
                    <th>Member Since</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): 
                    $initial = strtoupper(substr($user['name'], 0, 1));
                ?>
                    <tr>
                        <td style="display: flex; align-items: center; color: #fff;">
                            <div class="user-avatar"><?php echo $initial; ?></div>
                            <?php echo htmlspecialchars($user['name']); ?>
                        </td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td style="font-family: monospace; color: #888;">#<?php echo str_pad($user['id'], 4, '0', STR_PAD_LEFT); ?></td>
                        <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include "../includes/footer.php"; ?>