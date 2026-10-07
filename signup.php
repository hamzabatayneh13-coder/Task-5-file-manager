
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
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300&family=Josefin+Sans:wght@300&display=swap" rel="stylesheet">
    <title>Document</title>
    <link rel="stylesheet" href="signup-style.css">
</head>

<body>
    <div class="container-2">

        <div class="sidebar">
            <h3 class="sign-up">Sign Up</h3>
            <p class="sign-up-p">sign up with your simple details it will be cross checked by the adminstration</p>

            <h4 class="sign-in">Sign in</h4>
            <p class="sign-in-p">sign in with your username and password</p>
        </div>



        <form class="sign-up-div" action="" method="post">

            <h1>Regestration Form</h1>

            <div class="sign-up-field">
                <label for="">First Name :</label>
                <input name="fname" type="text" required>
                <span class="rule" id="fnameError"></span>
            </div>

            <div class="sign-up-field">
                <label for="">Last Name :</label>
                <input name="lname" type="text" required>
                <span class="rule" id="lnameError"></span>
            </div>

            <div class="sign-up-field">
                <label for="">Password :</label>
                <input name="password" type="password" equired>
                <span class="rule" id="passError"></span>
            </div>

            <div class="sign-up-field">
                <label for="">Address :</label>
                <input id="address" type="text" placeholder="">
                <span class="rule">Optional.</span>
            </div>

            <div class="sign-up-field">
                <label for="">Country :</label>
                <select id="country" class="list_country">
                    <option value="">(Please select a country)</option>
                    <option value="Jordan">Jordan</option>
                    <option value="Syria">Syria</option>
                    <option value="Iraq">Iraq</option>
                    <option value="KSA">KSA</option>
                    <option value="Egypt">Egypt</option>
                </select>
                <span class="rule" id="countryError"></span>
            </div>

            <div class="sign-up-field">
                <label for="">ZIP Code :</label>
                <input id="zip-code" type="text" equired>
                <span class="rule" id="zipCodeError"></span>
            </div>

            <div class="sign-up-field">
                <label for="">Email :</label>
                <input name="email" type="text" equired>
                <span class="rule" id="emailError"></span>
            </div>

            <div class="radio-sex">
                <label class="radio-sex-label" for="">Sex :</label>

                <input id="male" type="radio" name="sex" value="male">
                <label for="male">Male</label>

                <input id="female" type="radio" name="sex" value="female">
                <label for="female">Female</label>

                <span class="rule-1">Required.</span>
            </div>

            <div>
                <label>Language :</label>

                <input class="cb-language" id="english" type="checkbox" name="language" value="english">
                <label for="english">English</label>

                <input class="cb-language" id="non-english" type="checkbox" name="language" value="non-english">
                <label for="non-english">Non English</label>

                <span class="rule-1">Required.</span>
            </div>

            <div class="about">
                <label for="about">About :</label>
                <textarea name="about" id="about" placeholder="Optional." rows="5" cols="20"></textarea>
            </div>

            <div>
                <button name="submitSignUp" class="submit" type="submit">Submit</button>
            </div>
        </form>
    </div>
</body>

</html>