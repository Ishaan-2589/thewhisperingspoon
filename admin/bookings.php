<?php
session_start();
require_once "../includes/db.php";
require_once "../includes/admin-auth.php";

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id']) && isset($_POST['status'])) {
    $bookingId = (int) $_POST['booking_id'];
    $status = $_POST['status'];

    $validStatuses = ['Pending', 'Confirmed', 'Completed', 'Declined'];
    if (in_array($status, $validStatuses)) {
        $updateQuery = "UPDATE bookings SET status = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $updateQuery);
        mysqli_stmt_bind_param($stmt, "si", $status, $bookingId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $success = "Reservation status updated successfully!";
    }
}

// Get all bookings, ordering by the closest upcoming dates first
$query = "SELECT * FROM bookings ORDER BY date ASC, time ASC";
$result = mysqli_query($conn, $query);
$bookings = [];
while ($row = mysqli_fetch_assoc($result)) {
    $bookings[] = $row;
}
?>

<?php include "../includes/admin-header.php"; ?>

<style>
body { background-color: #050505; color: #fff; }
.admin-container { max-width: 1300px; margin: 40px auto; padding: 0 20px; }
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid #333; padding-bottom: 20px; }
.page-header h1 { color: gold; font-family: 'Playfair Display', serif; font-size: 32px; margin: 0; }
.btn-back { color: #888; text-decoration: none; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; transition: color 0.3s; }
.btn-back:hover { color: gold; }

.table-wrapper { background: #111; border: 1px solid #222; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
table { width: 100%; border-collapse: collapse; text-align: left; }
th { background: #1a1a1a; color: gold; padding: 18px 20px; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; border-bottom: 2px solid #333; }
td { padding: 18px 20px; border-bottom: 1px solid #222; color: #ccc; font-size: 14px; vertical-align: middle; }
tr:hover { background: #151515; }

.status-select { 
    background: #000; 
    color: #fff; 
    border: 1px solid #444; 
    padding: 8px 12px; 
    border-radius: 6px; 
    font-size: 13px;
    font-weight: bold;
    cursor: pointer;
    outline: none;
    transition: 0.3s;
}
.status-select:focus { border-color: gold; }

/* Status Colors within Select */
.status-select option[value="Pending"] { color: #ffcc00; }
.status-select option[value="Confirmed"] { color: #00ff88; }
.status-select option[value="Completed"] { color: #00c3ff; }
.status-select option[value="Declined"] { color: #ff4444; }

.guest-badge { background: #222; color: gold; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; border: 1px solid #444; }
.message-box { font-size: 12px; color: #888; background: #0a0a0a; padding: 8px; border-radius: 6px; border-left: 3px solid #444; margin-top: 5px; max-width: 250px;}

/* Highlight reservations that are for TODAY */
.row-today td { background: rgba(255, 215, 0, 0.03); }
.today-badge { background: gold; color: #000; font-size: 10px; padding: 2px 6px; border-radius: 4px; font-weight: bold; margin-left: 8px; text-transform: uppercase;}
</style>

<div class="admin-container">
    <div class="page-header">
        <h1><i class="fas fa-chair" style="margin-right: 10px; font-size: 24px; color: #555;"></i> Table Reservations</h1>
        <a href="dashboard.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </div>

    <?php if (isset($success)): ?>
        <div style="background: rgba(0,255,136,0.1); color: #00ff88; padding: 15px; border-radius: 8px; border: 1px solid #00ff88; margin-bottom: 20px;">
            <i class="fas fa-check-circle"></i> <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Ref ID</th>
                    <th>Customer Details</th>
                    <th>Date & Time</th>
                    <th>Party Size</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($bookings) === 0): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px; color: #666;">No table reservations found.</td>
                    </tr>
                <?php else: ?>
                    <?php 
                    $today = date('Y-m-d');
                    foreach ($bookings as $booking): 
                        $isToday = ($booking['date'] === $today);
                    ?>
                        <tr class="<?php echo $isToday ? 'row-today' : ''; ?>">
                            <td style="font-family: monospace; font-size: 16px; font-weight: bold; color: #fff;">
                                #<?php echo str_pad($booking['id'], 5, '0', STR_PAD_LEFT); ?>
                            </td>
                            
                            <td>
                                <div style="color: gold; font-weight: bold; margin-bottom: 4px; font-size: 16px;">
                                    <?php echo htmlspecialchars($booking['name']); ?>
                                </div>
                                <div style="font-size: 12px; color: #aaa; margin-bottom: 2px;">
                                    <i class="fas fa-envelope" style="width: 15px; color: #555;"></i> <?php echo htmlspecialchars($booking['email']); ?>
                                </div>
                                <div style="font-size: 12px; color: #aaa;">
                                    <i class="fas fa-phone" style="width: 15px; color: #555;"></i> <?php echo htmlspecialchars($booking['phone']); ?>
                                </div>
                                
                                <?php if (!empty($booking['message'])): ?>
                                    <div class="message-box">
                                        <strong><i class="fas fa-comment-alt"></i> Note:</strong> <?php echo htmlspecialchars($booking['message']); ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            
                            <td>
                                <div style="color: #fff; font-weight: bold; margin-bottom: 4px; font-size: 15px;">
                                    <?php echo date('D, M d, Y', strtotime($booking['date'])); ?>
                                    <?php if($isToday) echo '<span class="today-badge">Today</span>'; ?>
                                </div>
                                <div style="color: #00c3ff; font-weight: bold;">
                                    <i class="far fa-clock" style="margin-right: 5px;"></i>
                                    <?php echo date('h:i A', strtotime($booking['time'])); ?>
                                </div>
                            </td>
                            
                            <td>
                                <span class="guest-badge"><i class="fas fa-user-friends"></i> <?php echo $booking['guests']; ?> Guests</span>
                            </td>
                            
                            <td>
                                <form method="POST" style="margin: 0;">
                                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                    <select name="status" class="status-select" onchange="this.form.submit()" style="border-left: 4px solid 
                                        <?php 
                                            if($booking['status']=='Pending') echo '#ffcc00';
                                            elseif($booking['status']=='Confirmed') echo '#00ff88';
                                            elseif($booking['status']=='Completed') echo '#00c3ff';
                                            elseif($booking['status']=='Declined') echo '#ff4444';
                                            elseif($booking['status']=='Cancelled') echo '#888';
                                            else echo '#888';
                                        ?>;">
                                        <option value="Pending" <?php echo $booking['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Confirmed" <?php echo $booking['status'] === 'Confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                        <option value="Completed" <?php echo $booking['status'] === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                        <option value="Declined" <?php echo $booking['status'] === 'Declined' ? 'selected' : ''; ?>>Declined</option>
                                        <option value="Cancelled" <?php echo $booking['status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled (By User)</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include "../includes/footer.php"; ?>