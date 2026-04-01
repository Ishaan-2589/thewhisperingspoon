<?php
session_start();
require_once "../includes/db.php";
require_once "../includes/admin-auth.php";

// Handle Status Toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $newStatus = $_POST['store_status'] === 'open' ? 'open' : 'closed';
    $updateQuery = "UPDATE settings SET setting_value = ? WHERE setting_key = 'store_status'";
    $stmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($stmt, "s", $newStatus);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    $success = "Store status updated to " . strtoupper($newStatus) . "!";
}

// Fetch Current Status
$statusQuery = "SELECT setting_value FROM settings WHERE setting_key = 'store_status'";
$statusResult = mysqli_fetch_assoc(mysqli_query($conn, $statusQuery));
$storeStatus = $statusResult['setting_value'] ?? 'open';

include "../includes/admin-header.php"; 
?>

<style>
body { background-color: #050505; color: #fff; }
.admin-container { max-width: 800px; margin: 40px auto; padding: 0 20px; }
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid #333; padding-bottom: 20px; }
.page-header h1 { color: gold; font-family: 'Playfair Display', serif; font-size: 32px; margin: 0; }

.settings-card { background: #111; border: 1px solid #222; border-radius: 12px; padding: 30px; text-align: center; }
.status-indicator { display: inline-block; padding: 10px 20px; border-radius: 30px; font-weight: bold; font-size: 18px; text-transform: uppercase; margin-bottom: 20px; border: 2px solid;}
.status-open { color: #00ff88; border-color: #00ff88; background: rgba(0, 255, 136, 0.1); }
.status-closed { color: #ff4444; border-color: #ff4444; background: rgba(255, 68, 68, 0.1); }

.toggle-form { display: flex; flex-direction: column; align-items: center; gap: 20px; margin-top: 20px;}
.status-select { background: #000; color: #fff; border: 1px solid #444; padding: 12px 20px; border-radius: 6px; font-size: 16px; width: 200px; outline: none; text-align: center;}
.btn-save { background: gold; color: #000; border: none; padding: 12px 30px; border-radius: 30px; font-weight: bold; cursor: pointer; text-transform: uppercase; transition: 0.3s; }
.btn-save:hover { background: #ffcc00; box-shadow: 0 5px 15px rgba(255, 215, 0, 0.2); }
</style>

<div class="admin-container">
    <div class="page-header">
        <h1><i class="fas fa-cog" style="margin-right: 10px; font-size: 24px; color: #555;"></i> Store Settings</h1>
        <a href="dashboard.php" style="color: #888; text-decoration: none; text-transform: uppercase; font-size: 14px;"><i class="fas fa-arrow-left"></i> Back</a>
    </div>

    <?php if (isset($success)) echo "<div style='background: rgba(0,255,136,0.1); color: #00ff88; padding: 15px; border-radius: 8px; border: 1px solid #00ff88; margin-bottom: 20px;'><i class='fas fa-check-circle'></i> $success</div>"; ?>

    <div class="settings-card">
        <h2 style="margin-top: 0; color: #ccc;">Current Store Status</h2>
        
        <div class="status-indicator <?php echo $storeStatus === 'open' ? 'status-open' : 'status-closed'; ?>">
            <?php echo $storeStatus === 'open' ? '🟢 Accepting Orders' : '🔴 Kitchen Closed'; ?>
        </div>

        <p style="color: #888; font-size: 14px; max-width: 400px; margin: 0 auto;">
            If you close the store, customers can still browse the menu, but the checkout button will be disabled.
        </p>

        <form method="POST" class="toggle-form">
            <select name="store_status" class="status-select">
                <option value="open" <?php echo $storeStatus === 'open' ? 'selected' : ''; ?>>Open for Business</option>
                <option value="closed" <?php echo $storeStatus === 'closed' ? 'selected' : ''; ?>>Close Kitchen</option>
            </select>
            <button type="submit" name="update_status" class="btn-save">Update Status</button>
        </form>
    </div>
</div>

<?php include "../includes/footer.php"; ?>