<?php
session_start();
header('Content-Type: application/json');
require_once "../includes/db.php";

// Check Admin Auth
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? '';

// 1. FETCH LIVE ORDERS
if ($action === 'fetch') {
    // NEW: We added o.special_requests to this query
    $query = "SELECT o.id, o.status, o.created_at, o.special_requests, u.name as customer_name 
              FROM orders o 
              LEFT JOIN users u ON o.user_id = u.id 
              WHERE o.status IN ('Pending', 'Preparing', 'Ready') 
              ORDER BY o.created_at ASC";
              
    $result = mysqli_query($conn, $query);
    $orders = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Calculate wait time
            $created_time = strtotime($row['created_at']);
            $current_time = time();
            $minutes_waiting = floor(($current_time - $created_time) / 60);
            
            // Get order items
            $itemsQuery = "SELECT mi.name, oi.quantity FROM order_items oi JOIN menu_items mi ON oi.menu_item_id = mi.id WHERE oi.order_id = " . $row['id'];
            $itemsResult = mysqli_query($conn, $itemsQuery);
            $items = [];
            while ($item = mysqli_fetch_assoc($itemsResult)) {
                $items[] = $item;
            }

            $orders[] = [
                'id' => $row['id'],
                'status' => $row['status'],
                'created_at' => $row['created_at'],
                'customer_name' => $row['customer_name'],
                'special_requests' => $row['special_requests'], // Passing it to the frontend
                'minutes_waiting' => $minutes_waiting,
                'items' => $items
            ];
        }
    }
    
    echo json_encode(['status' => 'success', 'orders' => $orders]);
    exit;
}

// 2. UPDATE STATUS
if ($action === 'update') {
    $data = json_decode(file_get_contents('php://input'), true);
    $orderId = (int)$data['order_id'];
    $status = $data['status'];

    $updateQuery = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($stmt, "si", $status, $orderId);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
    }
    mysqli_stmt_close($stmt);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid Action']);