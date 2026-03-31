<?php
session_start();
require_once "../includes/db.php";
require_once "../includes/admin-auth.php";

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id']) && isset($_POST['status'])) {
    $bookingId = (int) $_POST['booking_id'];
    $status = $_POST['status'];

    $validStatuses = ['Pending', 'Confirmed', 'Cancelled'];
    if (in_array($status, $validStatuses)) {
        $updateQuery = "UPDATE bookings SET status = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $updateQuery);
        mysqli_stmt_bind_param($stmt, "si", $status, $bookingId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $success = "Booking status updated successfully!";
    }
}

// Get all bookings with user details
$query = "SELECT b.*, u.name as user_name, u.email as user_email
          FROM bookings b
          JOIN users u ON b.user_id = u.id
          ORDER BY b.created_at DESC";
$result = mysqli_query($conn, $query);
$bookings = [];
while ($row = mysqli_fetch_assoc($result)) {
    $bookings[] = $row;
}
?>

<?php include "../includes/header.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .bookings-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .bookings-table {
            width: 100%;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .bookings-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .bookings-table th, .bookings-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .bookings-table th {
            background-color: #f8f8f8;
            font-weight: bold;
            color: #8B4513;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.9em;
            font-weight: bold;
        }

        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-confirmed { background-color: #d4edda; color: #155724; }
        .status-cancelled { background-color: #f8d7da; color: #721c24; }

        .status-select {
            padding: 4px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .success {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #8B4513;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="bookings-container">
    <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
    <h1 style="color: #8B4513; margin-bottom: 20px;">Manage Table Bookings</h1>

    <?php if (isset($success)): ?>
        <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>

    <div class="bookings-table">
        <table>
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Customer</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Guests</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td>#<?php echo $booking['id']; ?></td>
                        <td><?php echo htmlspecialchars($booking['name']); ?></td>
                        <td><?php echo htmlspecialchars($booking['email']); ?></td>
                        <td><?php echo htmlspecialchars($booking['phone']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($booking['date'])); ?></td>
                        <td><?php echo date('H:i', strtotime($booking['time'])); ?></td>
                        <td><?php echo $booking['guests']; ?></td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                <select name="status" class="status-select" onchange="this.form.submit()">
                                    <option value="Pending" <?php echo $booking['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Confirmed" <?php echo $booking['status'] === 'Confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                    <option value="Cancelled" <?php echo $booking['status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </form>
                        </td>
                        <td>
                            <?php if (!empty($booking['message'])): ?>
                                <button onclick="alert('<?php echo addslashes($booking['message']); ?>')" style="background: #17a2b8; color: white; border: none; padding: 4px 8px; border-radius: 4px; cursor: pointer;">View Message</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>

<?php include "../includes/footer.php"; ?>