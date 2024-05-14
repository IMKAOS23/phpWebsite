<?php 
include "include/dbcon.php";

if (!isset($_SESSION["userId"])) {
    header("location: index.php");
}

$row = $db->select("users", "*", "user_id = :user_id", [":user_id" => $_SESSION["userId"]], "", false);
$username = $row["username"];   
$password = $row["password"];
$selectedGender = $row["gender_id"];
?>  
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Account</title>
        <link rel="stylesheet" href="static/css/basicStyle.css">
        <link rel="stylesheet" href="static/css/formStyle.css">
        <link rel="stylesheet" href="static/css/account.css">
    </head>
    <body>
        <a href="#main-content" class="skip-nav">Skip to main content</a>
        <?php
        include 'include/logged-in.php';
        ?>
        <main id="main-content">
            <section class="forms">
                <?php 
                if (isset($_POST['username'])) {
                    try {
                        $db->update("users", ["username" => $_POST["username"]], "user_id = :user_id", [":user_id" => $_SESSION["userId"]]);
                        header("location: account.php");
                        exit();
                    } catch (PDOException $e) {
                        error_log($e->getMessage());
                        echo "<script>alert('". $e->getMessage() . "')</script>";
                    }
                }

                if (isset($_POST["userEmail"])) {
                    try {
                        $row = $db->select("users", "*", "email = :email", [":email" => $_POST["userEmail"]], "", false);
                        if (empty($row)) {
                            $db->update("users", ["email" => $_POST["userEmail"]], "user_id = :user_id", [":user_id" => $_SESSION["userId"]]);
                            header('location: account.php');
                            exit();
                        } else {
                            echo "<script>alert('Email Already Exists');
                            setTimeout(function() {
                                window.location.href = 'account.php';
                            }, 1);
                            </script>";
                        }
                    } catch (PDOException $e) {
                        echo "<script>alert('". $e->getMessage() . "')</script>";
                    }
                }

                if (isset($_POST['currPassword']) && isset($_POST['newPassword']) && isset($_POST['confPassword'])) {
                    try {
                        if (password_verify($_POST['currPassword'], $password)) {
                            if ($_POST['new_password'] === $_POST['confPassword'] && strlen($_POST['newPassword']) >= 8) {
                                $newPassword = password_hash($_POST['newNassword'], PASSWORD_DEFAULT);
                                if ($db->update("users", ["password" => $newPassword], "user_id = :user_id", [":user_id" => $_SESSION["userId"]])) {
                                    header('location: account.php');
                                    exit();
                                }
                            }
                            if ($_POST['newPassword'] !== $_POST['confPassword']) {
                                echo "<script>alert('Passwords do not match')</script>";
                            }
                            if (strlen($_POST['newPassword'])){
                                echo "<script>alert('Password Must be Greater than 8 characters')</script>";
                            }
                        } else {
                            echo "<script>alert('Current Password Entered is Incorrect')</script>";
                        }
                    } catch (PDOException $e) {
                        echo "<script>alert('". $e->getMessage() . "')</script>";
                    }
                }

                if (isset($_POST["fName"]) && isset($_POST["lName"]) && isset($_POST["birth"]) && isset($_POST["gender"])) {
                    try {
                        $db->update("users", ["f_name" => $_POST["fName"],
                        "l_name" => $_POST["lName"],
                        "birth_date" => $_POST["birth"],
                        "gender_id" => $_POST["gender"]
                    ], "user_id = :user_id", [":user_id" => $_SESSION["userId"]]);
                        header("location: account.php");
                        exit();
                    } catch (PDOException $e) {
                        echo "<script>alert('". $e->getMessage() . "')</script>";
                    }
                }
                ?>
                <form class="change-details" method="POST">
                    <fieldset>
                        <legend>Change Username</legend>
                        <article class="input-box">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" value="<?php echo $username ?>" required>
                        </article>
                        <button type="submit" class="submit-form">Change Username</button>
                    </fieldset>
                </form>

                <form class="change-details" method="POST">
                    <fieldset>
                        <legend>Change Email</legend>
                        <article class="input-box">
                            <label for="userEmail">Email</label>
                            <input type="email" id="userEmail" name="userEmail" value="<?php echo $row['email'] ?>" required>    
                        </article>
                        <button type="submit" class="submit-form">Change Email</button>
                    </fieldset>
                </form>
                <form class="change-details" method="POST">
                    <fieldset>
                        <legend>Change Password</legend>
                        <article class="input-box">
                            <label for="currPassword">Current Password</label>
                            <input type="password" id="currPassword" name="currPassword" placeholder="Enter Your Current Password" required>
                        </article>
                        <article class="input-box">
                            <label for="new_password">New Password</label>
                            <input type="password" id="new_password" name="newPassword" placeholder="Enter Your New Password" required>
                        </article>
                        <article class="input-box">
                            <label for="confPassword">Confirm Password</label>
                            <input type="password" id="confPassword" name="confPassword" placeholder="Confirm Your Password" required>
                        </article>
                        <button type="submit" class="submit-form">Change Password</button>
                    </fieldset>
                </form>
                <form class="change-details" method="POST">
                    <fieldset>
                        <legend>Change Personal Details</legend>
                        <article class="input-box">
                            <label for="fName">First Name</label>
                            <input type="text" id="fName" name="fName" value="<?php echo $row['f_name'] ?>" required>
                        </article>
                        <article class="input-box">
                            <label for="lName">Last Name</label>
                            <input type="text" id="lName" name="lName" value="<?php echo $row['l_name'] ?>" required>
                        </article>
                        <article class="input-box">
                            <label for="birth">Birth Date</label>
                            <input type="date" id="birth" name="birth" value="<?php echo $row['birth_date'] ?>" required>
                        </article>
                        <article class="input-box">
                            <label for="gender">Gender</label>
                            <select name="gender" id="gender" name="gender" required>
                                <?php
                                $rows = $db->select("gender");
                                foreach ($rows as $row) {
                                    $genderId = $row['gender_id'];
                                    $gender = $row['gender'];
                                    ?>
                                    <option value="<?php echo $genderId ?>" <?php if ($genderId === $selectedGender) {echo "selected"; } ?>><?php echo $gender ?></option>
                                <?php } ?>
                            </select>
                        </article>
                        <button type="submit" class="submit-form">Change Details</button>
                    </fieldset>
                </form>
            </section>

            <section class="view-orders">
                <h2>View Orders</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Order Number</th>
                            <th>Username</th>
                            <th>Number of Items</th>
                            <th>Order Status</th>
                            <th>View Orders</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $orders = $db->select("orders", "*, 
                        CASE WHEN status_id = 1 THEN 'Processing'
                        WHEN status_id = 2 THEN 'Delivered'
                        WHEN status_id = 3 THEN 'Declined'
                        WHEN status_id = 4 THEN 'Posted'
                        WHEN status_id = 5 THEN 'Returned'
                        END AS status_name", "user_id = :user_id", [":user_id" => $_SESSION["userId"]]);
                        foreach ($orders as $order) { 
                            $totalItems = 0;
                            $orderJson = json_decode($order["cart"], true);
                            foreach ($orderJson as $item) {
                                $totalItems += $item['quantity'];
                            }
                        ?>
                        <tr>
                            <td><?php echo $order['order_id'] ?></td>
                            <td><?php echo $username ?></td>
                            <td><?php echo $totalItems ?></td>
                            <td><?php echo $order['status_name'] ?></td>
                            <td>
                                <form action="view-order.php" method="GET">
                                    <input type="hidden" name="orderId" value="<?php echo $order['order_id'] ?>">
                                    <button type="submit">View Order</button>
                                </form>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </section>
            <h3>Delete Account</h3>
            <form method="POST" id="deleteForm" action="deleteaccount.php">
                <input type="hidden" value="<?php echo $_SESSION["userId"]?>">
                <button type="button" class="submit-delete" onclick="confirmDelete()">Delete Account</button>
            </form>
            </form>
        </main>
        <?php include 'include/footer.php' ?>
        <script>
            function confirmDelete() {
                var confirmed = confirm("Are you sure you want to delete account?");
                if (confirmed) {
                    document.getElementById("deleteForm").submit()
                }
            }
        </script>
    </body>
</html>
