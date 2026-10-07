<?php

session_start();

class Auth {

    public $dataFile;

    public $firstname_login, $firstname_signup;
    public $lastname_login, $lastname_signup;
    public $email_login, $email_signup;
    public $password_login, $password_signup;

    function __construct(string $dataFile = __DIR__ . '/users.json'){

        $this->dataFile = $dataFile;
    
        $this->firstname_login = $_POST["Fname"];
        $this->lastname_login = $_POST["Lname"];
        $this->email_login = $_POST["Email"];
        $this->password_login = $_POST["Pass"];

        $this->firstname_signup = $_POST["fname"];
        $this->lastname_signup = $_POST["lname"];
        $this->email_signup = $_POST["email"];
        $this->password_signup = $_POST["password"];

    }

    function checkUserLogin(): ?array
    {
        if (!file_exists($this->dataFile)) {
            return null;
        }

        $users = json_decode(file_get_contents($this->dataFile), true);
        if (!is_array($users)) {
            return null;
        }

        foreach ($users as $user) {
            if (strcasecmp($user['firstname'], $this->firstname_login) === 0 &&
                strcasecmp($user['lastname'], $this->lastname_login) === 0 &&
                $user['email'] === $this->email_login &&
                password_verify($this->password_login, $user['password'])) {
                return $user;
            }
        }

        return null;
    }

    function signUp(): void
    {
        if (isset($_POST["SignUpBtn"])) {
            header("Location: signup.php");
            exit;
        }
    }

    function registerUser(): string
    {
        if (isset($_POST["submitSignUp"])) {

            if (!file_exists($this->dataFile)) {
                file_put_contents($this->dataFile, json_encode([]));
            }

            $users = json_decode(file_get_contents($this->dataFile), true);
            if (!is_array($users)) {
                $users = [];
            }

            $newUser = [
                'userId'    => bin2hex(random_bytes(6)),
                'firstname' => $this->firstname_signup,
                'lastname'  => $this->lastname_signup,
                'email'     => $this->email_signup,
                'password'  => password_hash($this->password_signup, PASSWORD_DEFAULT)
            ];

            $users[] = $newUser;

            file_put_contents($this->dataFile, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            $this->startSession($newUser);
            header("Location: home.php");
            exit;
        }

        return '';
    }

    function logIn(): string
    {
        if (isset($_POST["LogInBtn"])) {

            $user = $this->checkUserLogin();

            if ($user !== null && !empty($user['userId'])) {
                
                $this->startSession($user);
                header("Location: home.php");
                exit;
            } else {
                $loginError = "No account found with that email/password. Please sign up first.";
            }
        }

        return '';
    }

    function startSession(array $user): void
    {
        $_SESSION['userId']    = $user['userId'];
        $_SESSION['firstname'] = $user['firstname'];
    }

}

// function logOut(): void
// {
//     if (isset($_POST["logout-btn"])) {
//         session_unset();
//         session_destroy();
//         header("Location: login.php");
//         exit;
//     }
// }


// MAIN CODE

$auth_functions = new Auth();

$auth_functions->signup();
$signupError = $auth_functions->registerUser();
$loginError  = $auth_functions->logIn();
