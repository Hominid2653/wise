<?php
function isAdmin() {
    if (!isset($_SESSION['admin_id'])) {
        return false;
    }
    
    // You can add additional checks here if needed
    return true;
}

function requireAdmin() {
    if (!isAdmin()) {
        header('Location: /admin/login.php');
        exit();
    }
} 