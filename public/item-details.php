<?php
session_start();
require_once "../includes/db.php";

if (!isset($_GET['id'])) {
    header("Location: menu.php");
    exit;
}

$itemId = (int) $_GET['id'];

// Handle Review Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    if (!isset($_SESSION['user_id'])) {
        $error = "You must be logged in to leave a review.";
    } else {
        $userId = $_SESSION['user_id'];
        $rating = (int) $_POST['rating'];
        $reviewText = trim($_POST['review_text']);
        
        $insertQuery = "INSERT INTO reviews (user_id, menu_item_id, rating, review_text) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insertQuery);
        mysqli_stmt_bind_param($stmt, "iiis", $userId, $itemId, $rating, $reviewText);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $success = "Thank you for your review!";
    }
}

// Fetch Item Details
$query = "SELECT * FROM menu_items WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $itemId);
mysqli_stmt_execute($stmt);
$item = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$item) {
    die("Dish not found.");
}

// Fetch Reviews & Average Rating
$reviewsQuery = "SELECT r.*, u.name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.menu_item_id = ? ORDER BY r.created_at DESC";
$stmtRev = mysqli_prepare($conn, $reviewsQuery);
mysqli_stmt_bind_param($stmtRev, "i", $itemId);
mysqli_stmt_execute($stmtRev);
$reviewsResult = mysqli_stmt_get_result($stmtRev);

$reviews = [];
$totalRating = 0;
while ($row = mysqli_fetch_assoc($reviewsResult)) {
    $reviews[] = $row;
    $totalRating += $row['rating'];
}
$avgRating = count($reviews) > 0 ? round($totalRating / count($reviews), 1) : 0;

include "../includes/header.php"; 
?>

<style>
.details-wrapper { max-width: 1000px; margin: 40px auto; padding: 0 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 40px; }
@media (max-width: 768px) { .details-wrapper { grid-template-columns: 1fr; } }

.dish-image { width: 100%; height: 400px; object-fit: cover; border-radius: 16px; border: 1px solid #333; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
.dish-info h1 { font-family: 'Playfair Display', serif; font-size: 36px; color: gold; margin: 0 0 10px 0; }
.dish-badge { background: #222; color: #aaa; padding: 4px 12px; border-radius: 20px; font-size: 12px; text-transform: uppercase; margin-bottom: 20px; display: inline-block; border: 1px solid #444;}
.dish-desc { color: #ccc; font-size: 16px; line-height: 1.6; margin-bottom: 30px; }
.dish-price { font-size: 28px; color: #fff; font-weight: bold; font-family: 'Playfair Display', serif; margin-bottom: 30px; }

.btn-add { display: block; width: 100%; padding: 15px; background: gold; color: #000; text-align: center; border: none; border-radius: 30px; font-weight: bold; text-decoration: none; text-transform: uppercase; letter-spacing: 1px; transition: 0.3s; }
.btn-add:hover { background: #ffcc00; transform: translateY(-2px); }

/* Reviews Section */
.reviews-section { max-width: 1000px; margin: 0 auto 80px; padding: 0 20px; }
.reviews-header { border-bottom: 1px solid #333; padding-bottom: 15px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;}
.reviews-header h2 { font-family: 'Playfair Display', serif; color: #fff; margin: 0; }
.avg-stars { color: gold; font-size: 20px; }

.review-card { background: #111; padding: 20px; border-radius: 12px; border: 1px solid #222; margin-bottom: 15px; }
.reviewer-name { font-weight: bold; color: gold; margin-bottom: 5px; }
.review-date { font-size: 12px; color: #666; margin-bottom: 10px; }
.review-text { color: #ccc; font-size: 14px; line-height: 1.5; }

.review-form { background: #0a0a0a; padding: 25px; border-radius: 12px; border: 1px solid gold; margin-top: 40px; }
.review-form h3 { color: gold; margin-top: 0; font-family: 'Playfair Display', serif; }
.form-group { margin-bottom: 15px; }
.form-group select, .form-group textarea { width: 100%; padding: 12px; background: #000; border: 1px solid #333; color: #fff; border-radius: 6px; box-sizing: border-box;}
</style>

<div class="details-wrapper">
    <div>
        <img src="../assets/images/<?php echo htmlspecialchars($item['image']); ?>" class="dish-image" onerror="this.src='../assets/images/others/placeholder.jpg'">
    </div>
    
    <div class="dish-info">
        <a href="menu.php" style="color: #888; text-decoration: none; margin-bottom: 15px; display: inline-block;">&larr; Back to Menu</a>
        <h1><?php echo htmlspecialchars($item['name']); ?></h1>
        <div class="dish-badge">
            <span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:<?php echo $item['is_veg'] ? '#00ff88' : '#ff4444'; ?>; margin-right:5px;"></span>
            <?php echo htmlspecialchars($item['category']); ?>
        </div>
        
        <p class="dish-desc"><?php echo nl2br(htmlspecialchars($item['description'])); ?></p>
        <div class="dish-price">₹<?php echo number_format($item['price'], 2); ?></div>
        
        <form action="../includes/cart-action.php" method="POST">
            <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
            <input type="hidden" name="action" value="add">
            <button type="submit" class="btn-add"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
        </form>
    </div>
</div>

<div class="reviews-section">
    <div class="reviews-header">
        <h2>Customer Reviews (<?php echo count($reviews); ?>)</h2>
        <div class="avg-stars">
            <?php echo $avgRating; ?> <i class="fas fa-star"></i>
        </div>
    </div>

    <?php if (isset($success)) echo "<p style='color: #00ff88;'>$success</p>"; ?>
    <?php if (isset($error)) echo "<p style='color: #ff4444;'>$error</p>"; ?>

    <?php if (count($reviews) === 0): ?>
        <p style="color: #666; font-style: italic;">No reviews yet. Be the first to review this dish!</p>
    <?php else: ?>
        <?php foreach ($reviews as $rev): ?>
            <div class="review-card">
                <div class="reviewer-name"><?php echo htmlspecialchars($rev['name']); ?> 
                    <span style="color: gold; font-size: 12px; margin-left: 10px;">
                        <?php for($i=0; $i<$rev['rating']; $i++) echo "<i class='fas fa-star'></i>"; ?>
                    </span>
                </div>
                <div class="review-date"><?php echo date('F j, Y', strtotime($rev['created_at'])); ?></div>
                <div class="review-text"><?php echo nl2br(htmlspecialchars($rev['review_text'])); ?></div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="review-form">
            <h3>Leave a Review</h3>
            <form method="POST">
                <div class="form-group">
                    <label style="color: #aaa; font-size: 12px; text-transform: uppercase;">Rating</label>
                    <select name="rating" required>
                        <option value="5">5 Stars - Excellent</option>
                        <option value="4">4 Stars - Very Good</option>
                        <option value="3">3 Stars - Average</option>
                        <option value="2">2 Stars - Poor</option>
                        <option value="1">1 Star - Terrible</option>
                    </select>
                </div>
                <div class="form-group">
                    <label style="color: #aaa; font-size: 12px; text-transform: uppercase;">Your Review</label>
                    <textarea name="review_text" rows="4" placeholder="What did you think of the flavor, presentation, and portion size?" required></textarea>
                </div>
                <button type="submit" name="submit_review" class="btn-add" style="width: auto; padding: 10px 30px;">Submit Review</button>
            </form>
        </div>
    <?php else: ?>
        <p style="margin-top: 30px; padding: 20px; background: #111; text-align: center; border-radius: 8px; color: #888;">
            Please <a href="../auth/login.php" style="color: gold;">log in</a> to leave a review.
        </p>
    <?php endif; ?>
</div>

<?php include "../includes/footer.php"; ?>