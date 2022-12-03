<?php
    require("../database.php");
    $database = new Database();
    $username = NULL;
    if (isset($_POST['username'])) {
        $username = $_POST['username'];
    }
    $admin = NULL;
    if (isset($_POST['admin'])) {
        $admin = $_POST['admin'];
    }
    $full_access = NULL;
    if (isset($_POST['full_access'])) {
        $full_access = $_POST['full_access'];
    }
    $max_users = NULL;
    if (isset($_POST['max_users'])) {
        $max_users = $_POST['max_users'];
    }
    if (!isset($username) || !isset($max_users)) {
        return;
    }
    $admin_string = NULL;
    if ($admin || $full_access) {
        $admin_string = "OR ip LIKE '%$username%' OR last_ip LIKE '%$username%'";
    }
    $query = "SELECT user_id, username FROM users WHERE username LIKE '%$username%' ".$admin_string." OR email LIKE '%$username%' ORDER BY created_at DESC LIMIT $max_users";
    $users = $database->query($query);
    echo json_encode($users);
?>