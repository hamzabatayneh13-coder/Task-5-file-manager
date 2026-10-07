<?php 

session_start();

require "auth.php";
require "cud.php";

$cud_functions = new Cud();

$cud_functions->isLoggedin();

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300&family=Josefin+Sans:wght@300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="login-style-2.css">
</head>

<body>
    <div class="container">
        <div class="sidebar">
            <h3 class="sign-up">Sign Up</h3>
            <p class="sign-up-p">sign up with your simple details it will be cross checked by the adminstration</p>

            <h4 class="sign-in">Sign in</h4>
            <p class="sign-in-p">sign in with your username and password</p>
        </div>

        <form action="" method="post">
            <div class="form">
                <label for="" class="sign-in-label">FIRST NAME</label><br>
                <input name="Fname" type="text" class="sign-in-input" placeholder="example.new" ><br>

                <label for="" class="sign-in-label">LAST NAME</label><br>
                <input name="Lname" type="text" class="sign-in-input" placeholder="example.new" ><br>

                <label for="" class="sign-in-label">EMAIL</label><br>
                <input name="Email" type="email" class="sign-in-input" placeholder="example@fx.com" ><br>

                <label for="" class="sign-in-label">PASSWORD</label><br>
                <input name="Pass" type="password" class="sign-in-input" placeholder="**************" ><br>

                <div class="check-box-div">
                    <input name="cb" type="checkbox" class="check-box">
                    <label for="cb" class="check-box-text">I agree with the terms and conditions</label>
                </div>

                <div class="buttons">
                    <button class="sign-up-btn" name="SignUpBtn" type="submit">Sign up</button>
                    <p>Or</p>
                    <button class="log-in-btn" name="LogInBtn" type="submit">Log in</button>
                </div>
            </div>
        </form>
    </div>
</body>

</html>