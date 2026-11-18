<?php
/**
 * Database configuration
 * Edit these values to match your MySQL setup (XAMPP/WAMP/LAMP default shown below).
 */
define('DB_HOST', 'localhost');
define('DB_NAME', 'EcommerceDB');
define('DB_USER', 'root');
define('DB_PASS', '');       // default XAMPP password is empty
define('DB_CHARSET', 'utf8mb4');

$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    die("<div style='font-family:sans-serif;padding:40px;color:#b00;'>
            <h2>Database Connection Failed</h2>
            <p>" . htmlspecialchars($e->getMessage()) . "</p>
            <p>Make sure MySQL is running and that you have imported <code>database/01_Schema.sql</code>
            into a database named <code>EcommerceDB</code>. Check the credentials in <code>config.php</code>.</p>
         </div>");
}

// Start session (used for flash messages)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/** Small helper to output safely escaped text */
function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/** Set a one-time flash message shown on the next page load */
function set_flash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/** Retrieve and clear the flash message, if any */
function get_flash() {
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/** Format a number as Indian Rupees */
function money($amount) {
    return '₹' . number_format((float)$amount, 2);
}
