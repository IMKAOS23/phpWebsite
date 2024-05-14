<?php 
include "include/dbcon.php";
if (!isset($_SESSION["isAdmin"]) || $_SESSION["isAdmin"] === false) {
    header("location: index.php");
    exit();
}

if (!isset($_GET["editTable"]) | !isset($_GET["edit"])) {
    header("location: adminhome.php");
    exit();
}

if (isset($_GET["editTable"]) && $_GET["editTable"] === "product") {
    $product = $db->select("products", "*", "product_id = :product_id", [":product_id" => $_GET["edit"]], "", false);

    if (isset($_POST["productName"]) && isset($_POST["productPrice"]) && isset($_POST["productDesc"]) ) {
        if (isset($_FILES["newImage"])) {
            $uploadFile = "static/img/uploads/" . time() . basename($_FILES["newImage"]["name"]);
            if (move_uploaded_file($_FILES['newImage']['tmp_name'], $uploadFile)) {
                try {   
                    $db->update("products", ["product_name" => $_POST["productName"], "price" => $_POST["productPrice"],
                    "description" => $_POST["productDesc"], "image_url" => $uploadFile],
                    "product_id = :product_id", [":product_id" => $_POST["editVal"]]);
                } catch (PDOException $e) {
                    error_log($e->getMessage());
                }
            } 
        } else {
            try {
                $try = $db->update("products", ["product_name" => $_POST["productName"], "price" => $_POST["productPrice"],
                "description" => $_POST["productDesc"]], "product_id = :product_id", [":product_id" => $_POST["editVal"]]);
            } catch (PDOException $e) {
                error_log($e->getMessage());
            }
        }
        header("location: adminhome.php");
        exit();
    }
}

if (isset($_GET["editTable"]) && $_GET["editTable"] === "user") { 
    $user = $db->select("users", "*", "user_id = :user_id", [":user_id" => $_GET["edit"]],"", false);
    $selectedGender = $user["gender_id"];
    $selectedRole = $user["role_id"];

    if (isset($_POST["firstName"]) && isset($_POST["lastName"]) && isset($_POST["gender"]) && isset($_POST["dob"]) && isset($_POST["username"]) && isset($_POST["editEmail"])) {
        if (isset($_POST["editPword"])) {
            $newHashedPassword = password_hash($_POST["editPword"], PASSWORD_DEFAULT);
            try {
                $db->update("users", ["f_name" => $_POST["firstName"], "l_name" => $_POST["lastName"],
                "birth_date" => $_POST["dob"], "gender_id" => $_POST["gender"],
                "username" => $_POST["username"], "email" => $_POST["editEmail"], "role_id" => $_POST["role"], "password" => $newHashedPassword],
                "user_id = :user_id", [":user_id" => $_POST["editVal"]]);
            } catch (PDOException $e) {
                error_log($e->getMessage());
            }
        } else {
            try {
                $db->update("users", ["f_name" => $_POST["firstName"], "l_name" => $_POST["lastName"],
                "email" => $_POST["editEmail"], "birth_date" => $_POST["dob"], "gender_id" => $_POST["gender"],
                "username" => $_POST["username"], "role_id" => $_POST["role"]], "user_id = :user_id", [":user_id" => $_POST["editVal"]]);
            } catch (PDOException $e) {
                error_log($e->getMessage());
            }
        }
        header("location: adminusers.php");
        exit();
    }
}

if (isset($_GET["editTable"]) && $_GET["editTable"] === "order") {
    $order = $db->select("orders", "*", "order_id = :order_id", [":order_id" => $_GET["edit"]],"", false);
    $cart = json_decode($order["cart"], true);
    $quantity = 0;

    if (!empty($cart)) {
        $item = reset($cart);
        $quantity = $item["quantity"];
    }
    $selectedUser = $order["user_id"];
    $selectedStatus = $order["status_id"];
    $users = $db->select("users");
    $products = $db->select("products");

    if (isset($_POST["orderProduct"]) && isset($_POST["quantity"]) && isset($_POST["orderUser"]) && isset($_POST["orderStatus"])) {
        $cart = [];
        $item = [
            "product_id" => $_POST["orderProduct"],
            "quantity" => $_POST["quantity"]
        ];
        $row = $db->select("products", "*", "product_id = :product_id", [":product_id" => $_POST["orderProduct"]], "", false);
        $cart[$row["product_name"]] = $item;
        try {
            $db->update("orders", ["cart" => json_encode($cart), "user_id" => $_POST["orderUser"],
            "status_id" => $_POST["orderStatus"]], "order_id = :order_id", [":order_id" => $_POST["editVal"]]);
            header("location: adminorders.php");
            exit();
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo "Edit " . $_GET["editTable"] . ": " . $_GET["edit"] ?></title>
        <link rel="stylesheet" href="static/css/basicStyle.css">
        <link rel="stylesheet" href="static/css/formStyle.css">
        <link rel="stylesheet" href="static/css/home.css">
        <link rel="stylesheet" href="static/css/admin.css">
    </head>
    <body>
        <a href="#main-content" class="skip-nav">Skip to main content</a>
        <main id="main-content">
            <?php include "include/admin.php"; ?>
            <section class="admin-form">
                <?php if ($_GET["editTable"] === "product") { ?>
                <form method="POST" class="form" id="productForm">
                    <fieldset>
                        <legend>Edit Product - <?php echo $_GET["edit"] ?></legend>

                        <input type="hidden" name="editVal" value="<?php echo $_GET["edit"] ?>">

                        <article class="input-box">
                        <label for="product-name">Product Name:</label>
                        <input type="text" id="product-name" name="productName" required value="<?php echo $product['product_name']?>">
                        </article>
                        
                        <article class="input-box">
                        <label for="price">Price:</label>
                        <input type="number" step="any" id="price" name="productPrice" required value="<?php echo $product['price'] ?>">
                        </article>

                        <article class="input-box">
                        <label for="description">Description:</label>
                        <textarea id="description" col="50" rows="4" name="productDesc" required><?php echo $product['description']?></textarea>
                        </article>

                        <article class="show-image">
                        <label for="curr-image">Current Image:</label>
                        <img src="<?php echo $product['image_url']; ?>" id="curr-image" width="100" height="100" alt="Current Image">
                        </article>

                        <article class="input-box">
                        <label for="image">Select New Image (PNG/JGP)</label>
                        <input type="file" id="image" name="newImage" class="file-input">
                        </article>

                        <article class="submit">
                            <button type="submit">Edit Product</button>
                        </article>
                    </fieldset>
                </form>
                <?php } 
                if ($_GET["editTable"] === "user") { ?>
                <form class="form" method="POST">
                <fieldset>
                    <legend>Edit User - <?php echo $_GET["edit"] ?></legend>
                    <input type="hidden" value="<?php echo $_GET["edit"] ?>" name="editVal">

                    <article class="column">
                        <article class="input-box">
                            <label for="first-name">First Name:</label>
                            <input type="text" id="first-name" name="firstName" value="<?php echo $user["f_name"]?>" required>
                        </article>
                        <article class="input-box">
                            <label for="last-name">Last Name:</label>
                            <input type="text" id="last-name" name="lastName" value="<?php echo $user["l_name"] ?>" required>
                        </article>   
                    </article>
                    <article class="column">
                    <article class="input-box">
                        <label for="dob">Date of Birth:</label>
                        <input type="date" id="dob" name="dob" value="<?php echo $user["birth_date"] ?>" required>
                    </article>
                    <article class="input-box">
                        <label for="gender">Gender:</label>
                        <select name="gender" id="gender">
                            <?php $genders = $db->select("gender");
                            foreach ($genders as $gender) { ?>
                            <option value="<?php echo $gender["gender_id"] ?>" <?php if ($selectedGender === $gender["gender_id"]) echo "selected" ?>><?php echo $gender["gender"] ?></option>
                            <?php } ?>
                        </select>
                    </article>
                    </article>
                    <article class="input-box">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" value="<?php echo $user["username"] ?>" required>
                    </article>
                    <article class="input-box">
                        <label for="edit-email">Email:</label>
                        <input type="email" id="edit-email"  name="editEmail" value="<?php echo $user["email"] ?>" required>
                    </article>
                    <article class="column">
                        <article class="input-box">
                            <label for="role">Role:</label>
                            <select name="role" id="role">
                                <?php
                                $roles = $db->select("role"); 
                                foreach ($roles as $role) { ?>
                                <option value="<?php echo $role["role_id"] ?>" <?php if ($selectedRole === $role["role_id"]) echo "selected" ?>><?php echo $role["role"] ?></option>
                                <?php } ?>
                            </select>
                        </article>
                        <article class="input-box">
                            <label for="edit-pword">Password:</label>
                            <input type="password" id="edit-pword" name="editPword" placeholder="Enter Password to change User Password">
                        </article>
                    </article>
                    <article class="submit">
                        <button type="submit">Edit User</button>
                    </article>
                </fieldset>
            </form>
            <?php } 
            if ($_GET["editTable"] === "order") { ?>
                <form class="form" method="POST">
                    <fieldset>
                        <legend>Edit Order - <?php echo $_GET["edit"] ?></legend>
                        <input type="hidden" name="editVal" value="<?php echo $_GET["edit"] ?>">
                        <article class="input-box">
                            <label for="product">Product:</label>
                            <select name="orderProduct" id="product">
                                <?php foreach ($products as $product) {?>
                                <option value="<?php echo $product["product_id"]?>"><?php echo $product["product_name"] ?></option>
                                <?php } ?>
                            </select>
                        </article>
                        <article class="input-box">
                            <label for="quantity">Quantity:</label>
                            <input type="number" id="quantity" name="quantity" step="1" min="1" max="9" value="<?php echo $quantity ?>">
                        </article>
                        <article class="input-box">
                            <label for="user">User:</label>
                            <select name="orderUser" id="user">
                                <?php $users = $db->select("users");
                                foreach ($users as $user) { ?>
                                <option value="<?php echo $user["user_id"]?>" <?php if ($selectedUser === $user["user_id"]) echo "selected" ?>><?php echo $user["email"] ?></option>
                                <?php } ?>
                            </select>
                        </article>
                        <article class="input-box">
                            <label for="status">Status:</label>
                            <select name="orderStatus" id="status">
                                <?php $statuses = $db->select("status");
                                foreach ($statuses as $status) {?>
                                <option value="<?php echo $status["status_id"]?>" <?php if ($selectedStatus === $status["status_id"]) echo "selected" ?>><?php echo $status["status"] ?></option>
                                <?php } ?>
                            </select>
                        </article>
                        <article class="submit">
                            <button type="submit">Edit Order</button>
                        </article>
                    </fieldset>
                </form>
                <?php } ?>
            </section>
        </main>
        <?php include "include/footer.php" ?>
        <script>
            // Abit of JavaScript to dynamically change Enctype of Product Edit form
            // This is due to how PHP cannot be used as PHP is made on page load. Thus Using JavaScript fixes this issue.

            // Selecting File Select Image
            document.getElementById('image').addEventListener('change', function() {

                var form = document.getElementById('productForm');
                var enctype = this.files.length > 0 ? 'multipart/form-data' : 'application/x-www-form-urlencoded';
                form.setAttribute('enctype', enctype);
            });
        </script>
    </body>
</html>
