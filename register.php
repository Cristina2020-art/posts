<?php
    session_start();
    if (isset($_SESSION['logged']) && $_SESSION['logged'] == true) {
        header("location: index.php");
        exit();
    }
    include("./utils/bootstrap.php");
    include("./utils/jquery.php");
    include("./utils/sweetAlert.php");
    $bootstrap = new Bootstrap();
    $jquery = new jquery_class();
    $sweetAlert = new sweet_alert();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <?php 
        echo $jquery->jquery(); 
        echo $bootstrap->bootstrap4Css();
        echo $bootstrap->bootstrap4JS();
        echo $sweetAlert->sweetAlert();
    ?>
    <style>
        #register_form {
            margin: 0 25%;
        }
        #profile_photo {
            cursor: pointer;
        }
        @media only screen and (max-width: 600px) {
            #register_form {
                margin: 0 25px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Register Form</h1>
        <form id="register_form" enctype="multipart/form-data">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" name="username" id="username" placeholder="Enter username">
                <label for="username" style="margin: 0; display: none;" id="check_username"></label>
            </div>
            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" class="form-control" name="email" id="email" placeholder="Enter email">
                <label for="email" style="margin: 0; display: none;" id="check_email"></label>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" name="password" id="password" placeholder="Password">
                <label for="password" style="margin: 0; display: none;" id="check_password"></label>
            </div>
            <div class="form-group">
                <label for="profile_photo">Profile Photo</label>
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="profile_photo" name="profile_photo">
                    <label class="custom-file-label" for="profile_photo">Choose Profile Photo</label>
                </div>
            </div>
            <div>
                <button type="button" class="btn btn-primary disabled" id="submit">Register</button>
            </div>
            <p class="text-center">Do you have already an account? <a href="login.php" class="btn btn-danger">Log in</a></p>
        </form>
        <img id="demo"></img>
    </div>
    <script>
        $(document).ready(function() {
            let profile_photo = "", accepted_images = ["jpeg", "jpg", "png", "gif"];
            $("#profile_photo").on("change", function() {
                var file = this.files[0];  
                var reader = new FileReader();  
                reader.onloadend = function() {  
                    profile_photo = reader.result;
                    let filename = $('#profile_photo').val().replace(/.*(\/|\\)/, '');
                    let ext = filename.split('.').pop();
                    ext = ext.toLowerCase();
                    if (accepted_images.includes(ext)) {
                        if (filename.length >= 25) {
                            filename = filename.substr(0,25) + "..." + ext;
                        }
                        $(".custom-file-label").text(filename);
                    } else {
                        $(".custom-file-label").text("Choose Profile Photo");
                        sweetAlert("The images we accept are: PNG, JPEG, JPG or GIF.", "error");
                    }
                }  
                reader.readAsDataURL(file);
            });

            let max_req = [];
            function checkRequires (current_action, type) {
                if (!max_req.includes(current_action)) {
                    max_req.push(current_action);
                }

                if (type == "add" && max_req.length == 3) {
                    $("#submit").removeClass("disabled");
                } else if (type == "remove") {
                    max_req.slice(max_req.indexOf(current_action), 1);
                    $("#submit").addClass("disabled");
                }
            }

            $("#submit").click(function() {
                if ($(this).hasClass("disabled")) return;
                $.ajax({
                    url: "./php/register.php",
                    type: "POST",
                    data: {
                        username: $("#username").val(),
                        email: $("#email").val(),
                        password: $("#password").val(),
                        profile_photo: profile_photo
                    },
                    success: function (data) {
                        if (data != 1) {
                            sweetAlert(data, "error");
                        } else {
                            window.location = "index.php";
                        }
                    }
                });
            });
            $("#username").on("input", function() {
                $.ajax({
                    url: "./php/checkUsername.php",
                    type: "POST",
                    data: {
                        username: $(this).val()
                    },
                    success: function(data) { 
                        data = JSON.parse(data);
                        console.log(data);
                        let text = data.text;
                        let type = data.type;
                        $("#check_username").css('display', 'inline');
                        switch (type) {
                            case "success":
                                $("#check_username").css("color", "green");
                                $("#check_username").text(text); 
                                checkRequires("username", "add");
                                break;
                            case "warning":
                                $("#check_username").css("color", "red");
                                $("#check_username").text(text); 
                                checkRequires("username", "remove");
                                break;
                        }
                    }
                });
            });
            $("#email").on("input", function() {
                $.ajax({
                    url: "./php/checkEmail.php",
                    type: "POST",
                    data: {
                        email: $(this).val()
                    },
                    success: function(data) { 
                        data = JSON.parse(data);
                        console.log(data);
                        let text = data.text;
                        let type = data.type;
                        $("#check_email").css('display', 'inline');
                        switch (type) {
                            case "success":
                                $("#check_email").css("color", "green");
                                $("#check_email").text(text); 
                                checkRequires("email", "add");
                                break;
                            case "warning":
                                $("#check_email").css("color", "red");
                                $("#check_email").text(text); 
                                checkRequires("email", "remove");
                                break;
                        }
                    }
                });
            });
            $("#password").on("input", function() {
                $.ajax({
                    url: "./php/checkPassword.php",
                    type: "POST",
                    data: {
                        password: $(this).val()
                    },
                    success: function(data) { 
                        data = JSON.parse(data);
                        console.log(data);
                        let text = data.text;
                        let type = data.type;
                        $("#check_password").css('display', 'inline');
                        switch (type) {
                            case "success":
                                $("#check_password").css("color", "green");
                                $("#check_password").text(text); 
                                checkRequires("password", "add");
                                break;
                            case "warning":
                                $("#check_password").css("color", "red");
                                $("#check_password").text(text); 
                                checkRequires("password", "remove");
                                break;
                        }
                    }
                });
            });
            $("#email, #password, #username").keypress(function(e) {
                if (e.which == 13) {
                    $("#submit").click();
                }
            });
        });
    </script>
</body>
</html>