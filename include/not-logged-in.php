<?php 
if (isset($_POST['login-email']) && isset($_POST['login-pword'])) {
    if (filter_var($_POST["login-email"], FILTER_VALIDATE_EMAIL)) {
        $formPassword = $_POST['login-pword'];
        $row = $db->select("users", "*", "email = :email", [":email" => $_POST['login-email']], "", false);
    
        if ($row && password_verify($formPassword, $row['password'])) {
            try{
                if (isset($_POST['remember-me']) && $_POST['remember-me'] === 'yes') {
                    $sessionId = generateRandomID(16);
                    $currentTime = date("Y-m-d H:i:s");
                    $expire = date("Y-m-d H:i:s", strtotime($currentTime . " +30 days"));
                    $db->insert("sessions", ["session_id" => $sessionId, "expiry_date" => $expire, "user_id" => $row['user_id']]);
                    setcookie('session', $sessionId, time() + (86400 * 30), "/");
                }
                $_SESSION['userId'] = $row['user_id'];
                header("Location: $goTo");
                exit();
            }
            catch(PDOException $e) {
                echo "<script>" . $e->getMessage() . "</script>";
            }
        }
        else {
            Echo "<script>alert('Email/Password Does not Match')</script>";
        }
    }
}

if (isset($_POST['sign-email'], $_POST['username'], $_POST['sign-pword'], $_POST['conf-password'], $_POST['first-name'], $_POST['last-name'], $_POST['dob'], $_POST['gender'])) {
    if ($_POST['sign-pword'] !== $_POST['conf-password']) {
        echo '<script>alert("Passwords do not match.")</script>';
        exit;
    }
    if (strlen($_POST['sign-pword'] < 8)) {
        echo '<script>alert("Password must be more than 8 Characters")<script>';
        exit();
    }

    $hashedPassword = password_hash($_POST['sign-pword'], PASSWORD_DEFAULT);

    try {
        $row = $db->select("users", "COUNT(*) AS count", "email = :email", [":email" => $_POST["sign-email"]], false);
        if ($row['count'] > 0) {
            echo "<script>alert('Email Already Exists')</script>";
        }
        
        $insertCount = $db->insert("users", ["email" => $_POST["sign-email"],
        "username" => $_POST["username"],
        "gender_id" => $_POST["gender"],
        "password" => $hashedPassword,
        "f_name" => $_POST["first-name"],
        "l_name" => $_POST["last-name"],
        "birth_date" => $_POST["dob"]
    ]);
        if ($insertCount > 0) {
            $_SESSION['userId'] = $insertCount;
            header("Location: $goTo");
            exit;
        } else {
            echo "<script>alert('Failed To Create New User')</script>";
        }
    } catch(PDOException $e) {
        echo "<script>" . $e->getMessage() . "</script>";
    }
}
?>
<div class="modal-overlay" id="modal-overlay"></div>
<header class="navbar-big">
    <nav class="navbar-big-buttons-left">
        <a class="navbar-link-sign-up" onclick="showSignUpForm()">Sign-Up</a>
    </nav>
    <nav class="navbar-big-buttons-right">
        <a class="navbar-link-login" onclick="showLoginForm()">Login</a>
    </nav>
    <a href="index.php" class="navbar-big-logo">
        <img src="static/img/logo-no-bg.png" alt="VGEmporium logo">
    </a>
</header>

<section class="hidden-login">
    <button class="close-button" onclick="closeLoginForm()">X</button>
    <h1>Login</h1>
    <form class="form" method="POST">
        <article class="input-box">
            <label for="login-email">Email:</label>
            <input type="text" id="login-email" name="login-email" placeholder="Enter Your Email" required>
        </article>
        <article class="input-box">
            <label for="login-pword">Password:</label>
            <input type="password" id="login-pword" name="login-pword" placeholder="Enter Password" required>
        </article>
        <article class="remember">
            <label for="remember-me">Remember Me? - </label>
            <input type="checkbox" id="remember-me" name="remember-me" value="yes">
        </article>
        <article class="submit">
            <button type="submit" name="login-submit">Login</button>
        </article>
    </form>
</section>

<section class="hidden-sign-up">
    <button class="close-button" onclick="closeSignUpForm()">X</button>
    <h1>Sign-Up</h1>
    <form class="form" method="POST">
        <article class="column">
            <article class="input-box">
                <label for="first-name">First Name:</label>
                <input type="text" id="first-name" name="first-name" placeholder="Enter First name" required>
            </article>
            <article class="input-box">
                <label for="last-name">Last Name:</label>
                <input type="text" id="last-name" name="last-name" placeholder="Enter Last name" required>
            </article>   
        </article>
        <article class="column">
        <article class="input-box">
            <label for="dob">Date of Birth:</label>
            <input type="date" id="dob" name="dob" required>
        </article>
        <article class="input-box">
            <label for="gender">Gender:</label>
            <select required name="gender" id="gender">
                <option value="" disabled selected>Select Gender</option>
                <option value="1">Male</option>
                <option value="2">Female</option>
                <option value="3">Other</option>
            </select>
        </article>
        </article>
        <article class="input-box">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" placeholder="Enter Username" required>
        </article>
        <article class="input-box">
            <label for="sign-email">Email:</label>
            <input type="email" id="sign-email"  name="sign-email" placeholder="Enter Your Email" required>
        </article>
        <article class="column">
            <article class="input-box">
                <label for="sign-pword">Password:</label>
                <input type="password" id="sign-pword" name="sign-pword" placeholder="Enter Password" required>
            </article>
            <article class="input-box">
                <label for="conf-password">Confirm Password:</label>
                <input type="password" id="conf-password" name="conf-password" placeholder="Confirm Password" required>
            </article>
        </article>
        <article class="submit">
            <button type="submit">Sign Up</button>
        </article>
    </form>
</section>
<script src="static/js/script.js"></script>