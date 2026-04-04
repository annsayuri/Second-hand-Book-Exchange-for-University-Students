<?php
// ============================================
// AUTH CHECK — BookBridge Sri Lanka
// Include this file on any page that requires
// the user to be logged in
// ============================================

if (!isset($_SESSION['user_id'])) {
    header('Location: /bookbridge/login.php');
    exit();
}
?>
