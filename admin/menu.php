<?php
session_start();
require_once "../includes/db.php";
require_once "../includes/admin-auth.php";

// Handle form submissions (Add/Update/Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // ADD ITEM
    if (isset($_POST['add_item'])) {
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
    } 
    
    // UPDATE ITEM (The Missing Functionality!)
    elseif (isset($_POST['update_item'])) {
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
    }
    
    // DELETE ITEM
    elseif (isset($_POST['delete_item'])) {
        $id = (int) $_POST['item_id'];
        $deleteQuery = "DELETE FROM menu_items WHERE id=?";
        $stmt = mysqli_prepare($conn, $deleteQuery);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $success = "Menu item deleted successfully!";
    }
}

// Fetch all menu items
$query = "SELECT * FROM menu_items ORDER BY category, name";
$result = mysqli_query($conn, $query);
$menuItems = [];
while ($row = mysqli_fetch_assoc($result)) {
    $menuItems[] = $row;
}
?>

<?php include "../includes/admin-header.php"; ?>

<style>
body { background-color: #050505; color: #fff; }
.admin-container { max-width: 1400px; margin: 40px auto; padding: 0 20px; }
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid #333; padding-bottom: 20px; }
.page-header h1 { color: gold; font-family: 'Playfair Display', serif; font-size: 32px; margin: 0; }
.btn-back { color: #888; text-decoration: none; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; transition: color 0.3s; }
.btn-back:hover { color: gold; }

.menu-layout { display: grid; grid-template-columns: 350px 1fr; gap: 30px; align-items: start; }

/* Form Styles */
.form-card { background: #111; padding: 25px; border-radius: 12px; border: 1px solid #222; }
.form-card h3 { color: gold; margin-top: 0; margin-bottom: 20px; border-bottom: 1px solid #333; padding-bottom: 10px; }
.form-group { margin-bottom: 15px; }
.form-group label { display: block; margin-bottom: 6px; color: #aaa; font-size: 12px; text-transform: uppercase; }
.form-group input, .form-group textarea, .form-group select { width: 100%; padding: 10px; background: #000; border: 1px solid #333; color: #fff; border-radius: 6px; font-family: 'Roboto', sans-serif;}
.form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: gold; outline: none; }
.checkbox-group { display: flex; align-items: center; gap: 10px; }

.btn-submit { width: 100%; padding: 12px; background: gold; color: #000; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; text-transform: uppercase; transition: 0.3s; margin-top: 10px; }
.btn-submit:hover { background: #ffcc00; box-shadow: 0 5px 15px rgba(255,215,0,0.2); }

/* Table Styles */
.table-wrapper { background: #111; border: 1px solid #222; border-radius: 12px; overflow: hidden; }
table { width: 100%; border-collapse: collapse; text-align: left; }
th { background: #1a1a1a; color: gold; padding: 15px; font-size: 12px; text-transform: uppercase; border-bottom: 2px solid #333; }
td { padding: 15px; border-bottom: 1px solid #222; color: #ccc; font-size: 14px; vertical-align: middle; }
tr:hover { background: #151515; }
.item-img { width: 50px; height: 50px; object-fit: cover; border-radius: 8px; border: 1px solid #333; }
.category-badge { background: #222; color: #aaa; padding: 4px 10px; border-radius: 20px; font-size: 11px; text-transform: uppercase; }

.action-btn { background: transparent; border: none; color: #888; cursor: pointer; font-size: 16px; margin-right: 10px; transition: 0.2s;}
.action-btn:hover { color: gold; }
.action-btn.delete:hover { color: #ff4444; }

/* Modal Styles */
.modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 2000; justify-content: center; align-items: center; backdrop-filter: blur(5px); }
.modal-content { width: 100%; max-width: 450px; position: relative; }
.close-modal { position: absolute; top: 20px; right: 20px; background: transparent; color: #fff; border: none; font-size: 24px; cursor: pointer; transition: 0.3s; }
.close-modal:hover { color: gold; }
</style>

<div class="admin-container">
    <div class="page-header">
        <h1><i class="fas fa-utensils" style="margin-right: 10px; font-size: 24px; color: #555;"></i> Menu Management</h1>
        <a href="dashboard.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </div>

    <?php if (isset($success)): ?>
        <div style="background: rgba(0,255,136,0.1); color: #00ff88; padding: 15px; border-radius: 8px; border: 1px solid #00ff88; margin-bottom: 20px;">
            <i class="fas fa-check-circle"></i> <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <div class="menu-layout">
        <div class="form-card">
            <h3>Add New Dish</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Dish Name</label>
                    <input type="text" name="name" required placeholder="e.g. Truffle Pasta">
                </div>
                <div class="form-group">
                    <label>Price (₹)</label>
                    <input type="number" step="0.01" name="price" required placeholder="299.00">
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select name="category" required>
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
                    <label>Image File Path</label>
                    <input type="text" name="image" placeholder="pizza/margherita.jpg">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="3" required placeholder="A brief description..."></textarea>
                </div>
                <div class="form-group checkbox-group">
                    <input type="checkbox" id="is_veg" name="is_veg" value="1" checked style="width: auto;">
                    <label for="is_veg" style="margin:0; font-size: 14px; color: #fff;">This item is Vegetarian</label>
                </div>
                <button type="submit" name="add_item" class="btn-submit">Add to Menu</button>
            </form>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th width="70">Image</th>
                        <th>Details</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($menuItems as $item): ?>
                        <tr>
                            <td>
                                <img src="../assets/images/<?php echo htmlspecialchars($item['image']); ?>" class="item-img" onerror="this.src='../assets/images/others/placeholder.jpg'">
                            </td>
                            <td>
                                <div style="color: #fff; font-weight: bold; margin-bottom: 4px;">
                                    <span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:<?php echo $item['is_veg'] ? '#00ff88' : '#ff4444'; ?>; margin-right:5px;"></span>
                                    <?php echo htmlspecialchars($item['name']); ?>
                                </div>
                                <div style="font-size: 12px; color: #666;"><?php echo substr(htmlspecialchars($item['description']), 0, 40); ?>...</div>
                            </td>
                            <td><span class="category-badge"><?php echo htmlspecialchars($item['category']); ?></span></td>
                            <td style="color: gold; font-weight: bold;">₹<?php echo number_format($item['price'], 2); ?></td>
                            <td style="text-align: right;">
                                
                                <button class="action-btn" onclick='openEditModal(<?php echo json_encode($item, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)' title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this dish?');">
                                    <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                    <button type="submit" name="delete_item" class="action-btn delete" title="Delete"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="editModal" class="modal-overlay">
    <div class="form-card modal-content">
        <button type="button" class="close-modal" onclick="closeEditModal()">&times;</button>
        <h3>Edit Dish</h3>
        <form method="POST">
            <input type="hidden" name="item_id" id="edit_id">
            
            <div class="form-group">
                <label>Dish Name</label>
                <input type="text" name="name" id="edit_name" required>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Price (₹)</label>
                    <input type="number" step="0.01" name="price" id="edit_price" required>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select name="category" id="edit_category" required>
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
                <label>Image File Path</label>
                <input type="text" name="image" id="edit_image">
            </div>
            
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" id="edit_description" rows="3" required></textarea>
            </div>
            
            <div class="form-group checkbox-group">
                <input type="checkbox" id="edit_is_veg" name="is_veg" value="1" style="width: auto;">
                <label for="edit_is_veg" style="margin:0; font-size: 14px; color: #fff;">This item is Vegetarian</label>
            </div>
            
            <button type="submit" name="update_item" class="btn-submit">Save Changes</button>
        </form>
    </div>
</div>

<script>
function openEditModal(item) {
    // Fill the hidden ID field
    document.getElementById('edit_id').value = item.id;
    
    // Fill the text fields
    document.getElementById('edit_name').value = item.name;
    document.getElementById('edit_price').value = item.price;
    document.getElementById('edit_category').value = item.category;
    document.getElementById('edit_image').value = item.image;
    document.getElementById('edit_description').value = item.description;
    
    // Handle the checkbox (1 = true/checked in DB)
    document.getElementById('edit_is_veg').checked = (item.is_veg == 1);
    
    // Show the modal using Flexbox
    document.getElementById('editModal').style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}
</script>

<?php include "../includes/footer.php"; ?>