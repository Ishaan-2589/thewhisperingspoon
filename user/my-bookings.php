<?php
session_start();
require_once "/includes/db.php";

// Ensure user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: /auth/login.php");
    exit;
}

$userId = (int) $_SESSION["user_id"];

// HANDLE CANCELLATION REQUEST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking'])) {
    $bookingIdToCancel = (int) $_POST['booking_id'];
    
    $cancelQuery = "UPDATE bookings SET status = 'Cancelled' WHERE id = ? AND user_id = ? AND status IN ('Pending', 'Confirmed') AND date >= CURDATE()";
    $stmtCancel = mysqli_prepare($conn, $cancelQuery);
    mysqli_stmt_bind_param($stmtCancel, "ii", $bookingIdToCancel, $userId);
    
    if (mysqli_stmt_execute($stmtCancel) && mysqli_stmt_affected_rows($stmtCancel) > 0) {
        $success = "Reservation #".str_pad($bookingIdToCancel, 5, '0', STR_PAD_LEFT)." has been cancelled.";
    } else {
        $error = "Could not cancel this reservation. The date may have already passed.";
    }
    mysqli_stmt_close($stmtCancel);
}

// Fetch user's bookings
$query = "SELECT * FROM bookings WHERE user_id = ? ORDER BY date DESC, time DESC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$bookingsResult = mysqli_stmt_get_result($stmt);

include "/includes/header.php"; 
?>

<style>
.bookings-hero { text-align: center; padding: 40px 20px; border-bottom: 1px solid #333; margin-bottom: 40px; background: #050505;}
.bookings-hero h1 { font-size: 42px; color: gold; font-family: 'Playfair Display', serif; margin-bottom: 10px; }

.bookings-wrapper { max-width: 900px; margin: 0 auto; padding: 0 20px 80px; min-height: 50vh;}

.empty-state { text-align: center; padding: 60px 20px; background: #111; border: 1px dashed #333; border-radius: 16px; }
.empty-state i { font-size: 48px; color: #444; margin-bottom: 20px; display: block; }
.empty-state p { color: #888; font-size: 18px; margin-bottom: 25px; }

.booking-card { background: #0a0a0a; border: 1px solid #222; border-radius: 16px; margin-bottom: 20px; padding: 25px; display: flex; justify-content: space-between; align-items: center; transition: 0.3s; border-left: 4px solid #333;}
.booking-card:hover { border-color: #444; background: #111; }

.booking-info h3 { color: #fff; font-family: 'Playfair Display', serif; font-size: 22px; margin: 0 0 10px 0; }
.booking-details { color: #aaa; font-size: 14px; display: flex; gap: 20px; margin-bottom: 10px; }
.booking-details i { color: gold; width: 16px; text-align: center; margin-right: 5px; }
.booking-message { color: #666; font-size: 13px; font-style: italic; }

.status-badge { padding: 6px 16px; border-radius: 30px; font-size: 12px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; text-align: center;}
.status-Pending { background: rgba(255, 204, 0, 0.1); color: #ffcc00; border: 1px solid #ffcc00; }
.status-Confirmed { background: rgba(0, 255, 136, 0.1); color: #00ff88; border: 1px solid #00ff88; }
.status-Completed { background: rgba(0, 195, 255, 0.1); color: #00c3ff; border: 1px solid #00c3ff; }
.status-Declined { background: rgba(255, 68, 68, 0.1); color: #ff4444; border: 1px solid #ff4444; }
.status-Cancelled { background: transparent; color: #888; border: 1px dashed #555; }

.btn-book { display: inline-block; background: gold; color: #000; padding: 12px 30px; border-radius: 30px; font-weight: bold; text-decoration: none; transition: 0.3s; }
.btn-book:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(255, 215, 0, 0.2); }

/* Cancel Button Trigger */
.btn-cancel { background: transparent; border: 1px solid #ff4444; color: #ff4444; padding: 8px 16px; border-radius: 20px; font-size: 11px; font-weight: bold; cursor: pointer; transition: 0.3s; text-transform: uppercase; letter-spacing: 1px;}
.btn-cancel:hover { background: #ff4444; color: #fff; box-shadow: 0 4px 10px rgba(255, 68, 68, 0.3); }

.custom-modal-overlay {
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0,0,0,0.8); backdrop-filter: blur(8px);
    z-index: 9999; display: none; align-items: center; justify-content: center;
    animation: fadeIn 0.3s ease;
}
.custom-modal-box {
    background: #111; border: 1px solid #333; border-radius: 16px;
    padding: 40px; max-width: 450px; width: 90%; text-align: center;
    box-shadow: 0 20px 50px rgba(0,0,0,0.5);
    transform: translateY(20px); animation: slideUp 0.3s ease forwards;
}
.custom-modal-box .warning-icon { color: #ff4444; font-size: 50px; margin-bottom: 20px; text-shadow: 0 0 20px rgba(255,68,68,0.3);}
.custom-modal-box h3 { color: #fff; font-family: 'Playfair Display', serif; font-size: 26px; margin: 0 0 10px 0; }
.custom-modal-box p { color: #aaa; font-size: 15px; line-height: 1.6; margin-bottom: 30px; }
.modal-buttons { display: flex; gap: 15px; justify-content: center; }
.btn-modal-close { background: transparent; border: 1px solid #555; color: #ccc; padding: 12px 24px; border-radius: 30px; cursor: pointer; font-weight: bold; transition: 0.3s; }
.btn-modal-close:hover { background: #222; color: #fff; }
.btn-modal-confirm { background: rgba(255,68,68,0.1); border: 1px solid #ff4444; color: #ff4444; padding: 12px 24px; border-radius: 30px; cursor: pointer; font-weight: bold; transition: 0.3s; }
.btn-modal-confirm:hover { background: #ff4444; color: #fff; box-shadow: 0 5px 15px rgba(255,68,68,0.3);}

@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes slideUp { to { transform: translateY(0); } }

.alert { padding: 15px; border-radius: 8px; margin-bottom: 25px; font-weight: bold; text-align: center; }
.alert-error { background: rgba(255, 68, 68, 0.1); color: #ff4444; border: 1px solid #ff4444; }
.alert-success { background: rgba(0, 255, 136, 0.1); color: #00ff88; border: 1px solid #00ff88; }
</style>

<div class="bookings-hero">
    <h1>My Reservations</h1>
    <p style="color: #888;">Track your upcoming and past dining experiences.</p>
</div>

<div class="bookings-wrapper">
    
    <?php if (isset($error)) echo "<div class='alert alert-error'>$error</div>"; ?>
    <?php if (isset($success)) echo "<div class='alert alert-success'><i class='fas fa-check-circle'></i> $success</div>"; ?>

    <?php if (mysqli_num_rows($bookingsResult) === 0): ?>
        <div class="empty-state">
            <i class="fas fa-calendar-times"></i>
            <p>You haven't made any table reservations yet.</p>
            <a href="book-table.php" class="btn-book">Book a Table Now</a>
        </div>
    <?php else: ?>
        
        <?php while ($booking = mysqli_fetch_assoc($bookingsResult)): 
            $canCancel = false;
            if (($booking['status'] === 'Pending' || $booking['status'] === 'Confirmed') && $booking['date'] >= date('Y-m-d')) {
                $canCancel = true;
            }

            $borderColor = '#333';
            if($booking['status'] == 'Pending') $borderColor = '#ffcc00';
            if($booking['status'] == 'Confirmed') $borderColor = '#00ff88';
            if($booking['status'] == 'Declined') $borderColor = '#ff4444';
            if($booking['status'] == 'Cancelled') $borderColor = '#555';
        ?>
            <div class="booking-card" style="border-left-color: <?php echo $borderColor; ?>">
                <div class="booking-info">
                    <h3 style="<?php echo ($booking['status'] == 'Cancelled') ? 'text-decoration: line-through; color: #666;' : ''; ?>">
                        <?php echo date('l, F j, Y', strtotime($booking['date'])); ?>
                    </h3>
                    <div class="booking-details">
                        <span><i class="far fa-clock"></i> <?php echo date('h:i A', strtotime($booking['time'])); ?></span>
                        <span><i class="fas fa-user-friends"></i> <?php echo $booking['guests']; ?> Guests</span>
                        <span style="font-family: monospace; color: #888;">ID: #<?php echo str_pad($booking['id'], 5, '0', STR_PAD_LEFT); ?></span>
                    </div>
                </div>
                
                <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 15px;">
                    <div class="status-badge status-<?php echo $booking['status']; ?>">
                        <?php echo $booking['status']; ?>
                    </div>

                    <?php if ($canCancel): ?>
                        <button type="button" class="btn-cancel" onclick="openCancelModal(<?php echo $booking['id']; ?>)">Cancel Booking</button>
                        
                        <form id="cancel-form-<?php echo $booking['id']; ?>" method="POST" style="display: none;">
                            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                            <input type="hidden" name="cancel_booking" value="1">
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>

    <?php endif; ?>
    <?php mysqli_stmt_close($stmt); ?>
</div>

<div id="custom-cancel-modal" class="custom-modal-overlay">
    <div class="custom-modal-box">
        <i class="fas fa-exclamation-triangle warning-icon"></i>
        <h3>Cancel Reservation?</h3>
        <p>Are you absolutely sure you want to cancel this reservation? This action cannot be undone and your table will be released.</p>
        <div class="modal-buttons">
            <button class="btn-modal-close" onclick="closeCancelModal()">Keep My Table</button>
            <button class="btn-modal-confirm" onclick="confirmCancellation()">Yes, Cancel It</button>
        </div>
    </div>
</div>

<script>
let currentBookingToCancel = null;

function openCancelModal(bookingId) {
    currentBookingToCancel = bookingId;
    document.getElementById('custom-cancel-modal').style.display = 'flex';
}

function closeCancelModal() {
    currentBookingToCancel = null;
    document.getElementById('custom-cancel-modal').style.display = 'none';
}

function confirmCancellation() {
    if (currentBookingToCancel) {
        document.getElementById('cancel-form-' + currentBookingToCancel).submit();
    }
}
</script>

<?php include "/includes/footer.php"; ?>