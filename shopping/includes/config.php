<?php
// ============================================
// DATABASE CONFIGURATION
// ============================================
define('DB_SERVER', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'shopping');
define('SITE_NAME', 'ShopEase');
define('SITE_URL', '/shopping');
define('CURRENCY', '₹');

// Database connection
$con = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
if (mysqli_connect_errno()) {
    die("Database connection failed: " . mysqli_connect_error());
}
mysqli_set_charset($con, "utf8mb4");

// ============================================
// HELPER FUNCTIONS
// ============================================

// Safe query helper
function safe_query($con, $sql) {
    $result = mysqli_query($con, $sql);
    return $result;
}

// Get cart count from session
function getCartCount() {
    $count = 0;
    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $count += $item['quantity'];
        }
    }
    return $count;
}

// Get cart total from session  
function getCartTotal($con) {
    $total = 0;
    if (!empty($_SESSION['cart'])) {
        $ids = array_keys($_SESSION['cart']);
        if (!empty($ids)) {
            $idStr = implode(',', array_map('intval', $ids));
            $query = mysqli_query($con, "SELECT id, productPrice, shippingCharge FROM products WHERE id IN($idStr)");
            if ($query) {
                while ($row = mysqli_fetch_assoc($query)) {
                    $qty = $_SESSION['cart'][$row['id']]['quantity'];
                    $total += ($row['productPrice'] * $qty) + $row['shippingCharge'];
                }
            }
        }
    }
    return $total;
}

// Get wishlist count
function getWishlistCount($con, $userId) {
    if (!$userId) return 0;
    $result = mysqli_query($con, "SELECT COUNT(id) as cnt FROM wishlist WHERE userId=" . intval($userId));
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['cnt'];
    }
    return 0;
}

// Generate order number
function generateOrderNumber() {
    return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
}

// Password compatibility: verify against both MD5 and bcrypt
function verifyPassword($inputPassword, $storedHash) {
    // First try bcrypt
    if (password_verify($inputPassword, $storedHash)) {
        return true;
    }
    // Fallback to MD5 for old passwords
    if (md5($inputPassword) === $storedHash) {
        return true;
    }
    return false;
}

// Migrate password from MD5 to bcrypt
function migratePassword($con, $table, $id, $newPassword) {
    $hash = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($con, "UPDATE $table SET password = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $hash, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $hash;
}

// Sanitize output
function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

// Format price
function formatPrice($price) {
    return CURRENCY . number_format($price, 2);
}

// Calculate discount percentage
function calcDiscount($original, $selling) {
    if ($original > 0 && $original > $selling) {
        return round((($original - $selling) / $original) * 100);
    }
    return 0;
}

// Get stock status
function getStockStatus($qty) {
    if ($qty <= 0) return ['text' => 'Out of Stock', 'class' => 'stock-out', 'badge' => 'danger'];
    if ($qty <= 5) return ['text' => 'Low Stock (' . $qty . ' left)', 'class' => 'stock-low', 'badge' => 'warning'];
    return ['text' => 'In Stock', 'class' => 'stock-in', 'badge' => 'success'];
}

// CSRF Token
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Flash messages
function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
?>