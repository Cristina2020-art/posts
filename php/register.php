<?php
    session_start();
    require("../database.php");
    $database = new Database();
    $email = $_POST['email'];
    $password = $_POST['password'];
    $username = $_POST['username'];
    $err_message = 1;

    if (strlen($email) == 0 || strlen($password) == 0 || strlen($username) == 0) {
        $err_message = "Please complete with your email/password/username.";
    } else {
        $query = "SELECT * FROM users WHERE email='".$email."' OR username='".$username."' LIMIT 1";
        $check_user = $database->query($query);
        if (count($check_user) == 0) {
            $data = array(
                "email" => $email,
                "password" => md5($password),
                "username" => $username
            );
            $database->insert("users", $data);

            $database->where("email", $email);
            $user = $database->select("users", 1);
            $user = $user[0];
            $_SESSION['logged'] = true;
            $_SESSION['user_id'] = $user['user_id'];
        } else {
            $err_message = "There is already a user with this username or email!";
        }
    }

    echo $err_message;
?>