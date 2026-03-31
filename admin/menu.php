<?php
session_start();
require_once "../includes/db.php";
require_once "../includes/admin-auth.php";

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_item'])) {
        // Add new menu item
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $price = (float) $_POST['price'];
        $category = $_POST['category'];
        $is_veg = isset($_POST['is_veg']) ? 1 : 0;
        $image = trim($_POST['image']);

        $insertQuery = "INSERT INTO menu_items (name, description, price, image, category, is_veg) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insertQuery);
        mysqli_stmt_bind_param($stmt, "ssdssi", $name, $description, $price, $image, $category, $is_veg);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $success = "Menu item added successfully!";
    } elseif (isset($_POST['update_item'])) {
        // Update existing item
        $id = (int) $_POST['item_id'];
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $price = (float) $_POST['price'];
        $category = $_POST['category'];
        $is_veg = isset($_POST['is_veg']) ? 1 : 0;
        $image = trim($_POST['image']);

        $updateQuery = "UPDATE menu_items SET name=?, description=?, price=?, image=?, category=?, is_veg=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $updateQuery);
        mysqli_stmt_bind_param($stmt, "ssdssii", $name, $description, $price, $image, $category, $is_veg, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $success = "Menu item updated successfully!";
    } elseif (isset($_POST['delete_item'])) {
        // Delete item
        $id = (int) $_POST['item_id'];
        $deleteQuery = "DELETE FROM menu_items WHERE id=?";
        $stmt = mysqli_prepare($conn, $deleteQuery);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $success = "Menu item deleted successfully!";
    }
}

// Get all menu items
$query = "SELECT * FROM menu_items ORDER BY category, name";
$result = mysqli_query($conn, $query);
$menuItems = [];
while ($row = mysqli_fetch_assoc($result)) {
    $menuItems[] = $row;
}
?>

<?php include "../includes/header.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Menu - Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .menu-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .add-item-form, .menu-table {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .form-row {
            display: flex;
            gap: 15px;
        }

        .form-row .form-group {
            flex: 1;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
        }

        .checkbox-group input {
            width: auto;
            margin-right: 8px;
        }

        .btn {
            background: #8B4513;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn:hover {
            background: #654321;
        }

        .btn-danger {
            background: #dc3545;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .success {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f8f8f8;
            font-weight: bold;
        }

        .veg-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 5px;
        }

        .veg { background-color: #28a745; }
        .non-veg { background-color: #dc3545; }

        .actions {
            white-space: nowrap;
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

<div class="menu-container">
    <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
    <h1 style="color: #8B4513; margin-bottom: 20px;">Manage Menu Items</h1>

    <?php if (isset($success)): ?>
        <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>

    <div class="add-item-form">
        <h3>Add New Menu Item</h3>
        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="price">Price (₹)</label>
                    <input type="number" id="price" name="price" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category" name="category" required>
                        <option value="soup">Soup</option>
                        <option value="salad">Salad</option>
                        <option value="nibbles">Nibbles</option>
                        <option value="sandwich">Sandwich</option>
                        <option value="pasta">Pasta</option>
                        <option value="pizza">Pizza</option>
                        <option value="desserts">Desserts</option>
                        <option value="beverages">Beverages</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3"></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="image">Image Path</label>
                    <input type="text" id="image" name="image" placeholder="e.g., soup/manchow.jpg">
                </div>
                <div class="form-group checkbox-group">
                    <input type="checkbox" id="is_veg" name="is_veg" checked>
                    <label for="is_veg">Vegetarian</label>
                </div>
            </div>
            <button type="submit" name="add_item" class="btn">Add Item</button>
        </form>
    </div>

    <div class="menu-table">
        <h3>Existing Menu Items</h3>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Type</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($menuItems as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td><?php echo ucfirst($item['category']); ?></td>
                        <td>₹<?php echo number_format($item['price'], 2); ?></td>
                        <td>
                            <span class="veg-indicator <?php echo $item['is_veg'] ? 'veg' : 'non-veg'; ?>"></span>
                            <?php echo $item['is_veg'] ? 'Veg' : 'Non-Veg'; ?>
                        </td>
                        <td class="actions">
                            <button onclick="editItem(<?php echo $item['id']; ?>)" class="btn">Edit</button>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this item?')">
                                <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                <button type="submit" name="delete_item" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Edit Modal (simplified - in production, use proper modal) -->
<div id="editModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 8px; max-width: 500px; width: 90%;">
        <h3>Edit Menu Item</h3>
        <form method="POST" id="editForm">
            <input type="hidden" name="item_id" id="edit_item_id">
            <!-- Form fields similar to add form -->
            <div class="form-row">
                <div class="form-group">
                    <label for="edit_name">Name</label>
                    <input type="text" id="edit_name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="edit_price">Price (₹)</label>
                    <input type="number" id="edit_price" name="price" step="0.01" required>
                </div>
            </div>
            <div class="form-group">
                <label for="edit_description">Description</label>
                <textarea id="edit_description" name="description" rows="3"></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="edit_category">Category</label>
                    <select id="edit_category" name="category" required>
                        <option value="soup">Soup</option>
                        <option value="salad">Salad</option>
                        <option value="nibbles">Nibbles</option>
                        <option value="sandwich">Sandwich</option>
                        <option value="pasta">Pasta</option>
                        <option value="pizza">Pizza</option>
                        <option value="desserts">Desserts</option>
                        <option value="beverages">Beverages</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_image">Image Path</label>
                    <input type="text" id="edit_image" name="image">
                </div>
            </div>
            <div class="form-group checkbox-group">
                <input type="checkbox" id="edit_is_veg" name="is_veg">
                <label for="edit_is_veg">Vegetarian</label>
            </div>
            <div style="text-align: right; margin-top: 15px;">
                <button type="button" onclick="closeEditModal()" class="btn" style="background: #6c757d; margin-right: 10px;">Cancel</button>
                <button type="submit" name="update_item" class="btn">Update Item</button>
            </div>
        </form>
    </div>
</div>

<script>
function editItem(itemId) {
    // In a real application, you'd fetch item data via AJAX
    // For now, this is a placeholder
    alert('Edit functionality would be implemented with AJAX to fetch item data');
    document.getElementById('editModal').style.display = 'block';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}
</script>

</body>
</html>

<?php include "../includes/footer.php"; ?>