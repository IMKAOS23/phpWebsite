<?php
include "include/dbcon.php";

if (!isset($_SESSION["isAdmin"]) || $_SESSION["isAdmin"] === false) {
    header("location: index.php");
    exit();
}

if (!isset($_GET["add"])) {
    header("location: adminhome.php");
    exit();
}

if (isset($_POST["productName"]) && isset($_POST["productPrice"]) && isset($_POST["productDesc"]) && isset($_FILES["addImage"])) {
    $uploadFile = "static/img/uploads/" . time() . basename($_FILES["addImage"]["name"]);
    if (move_uploaded_file($_FILES['addImage']['tmp_name'], $uploadFile)) {
        try {   
            $db->insert("products", ["product_name" => $_POST["productName"], "price" => $_POST["productPrice"],
            "description" => $_POST["productDesc"], "image_url" => $uploadFile]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
    }
    header("location: adminhome.php");
    exit();
}

if (isset($_POST["firstName"]) && isset($_POST["lastName"]) &&
isset($_POST["dob"]) && isset($_POST["gender"]) && isset($_POST["username"]) && isset($_POST["addEmail"])
&& isset($_POST["role"]) && isset($_POST["addPword"])) {
    $hashedPassword = password_hash($_POST["addPword"], PASSWORD_DEFAULT);
    try {
        $db->insert("users", ["f_name" => $_POST["firstName"], "l_name" => $_POST["lastName"], "birth_date" => $_POST["dob"],
    "gender_id" => $_POST["gender"], "username" => $_POST["username"], "email" => $_POST["addEmail"],
    "role_id" => $_POST["role"], "password" => $hashedPassword]);
    } catch (PDOException $e) {
        error_log($e->getMessage());
    }
    header("location: adminusers.php");
    exit();
}

if (isset($_POST["orderProduct"]) && isset($_POST["quantity"]) && isset($_POST["orderUser"])) {
    $cart = [];
    $item = [
        "product_id" => $_POST["orderProduct"],
        "quantity" => $_POST["quantity"]
    ];
    $row = $db->select("products", "*", "product_id = :product_id", [":product_id" => $_POST["orderProduct"]], "", false);
    $cart[$row["product_name"]] = $item;
    try {
        $db->insert("orders", ["user_id" => $_POST["orderUser"], "cart" => json_encode($cart),
        "total_price" => ($row["price"] * $_POST["quantity"]), "date_of_purchase" => date("Y-m-d H:i:s"), "status_id" => 1]);
    } catch (PDOException $e) {
        error_log($e->getMessage());
    }
    header("location: adminorders.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo "Add" . $_GET["add"] ?></title>
        <link rel="stylesheet" href="static/css/basicStyle.css">
        <link rel="stylesheet" href="static/css/formStyle.css">
        <link rel="stylesheet" href="static/css/home.css">
        <link rel="stylesheet" href="static/css/admin.css">
    </head>
    <body>
        <main>
            <?php include "include/admin.php"; ?>
            <section class="admin-form">
                <?php if ($_GET["add"] === "product") { ?>
                <form method="POST" class="form" id="productForm" enctype="multipart/form-data">
                    <fieldset>
                        <legend>Add Product</legend>
                        <article class="input-box">
                        <label for="productName">Product Name:</label>
                        <input type="text" id="productName" name="productName" placeholder="Enter a product name" required>
                        </article>
                        
                        <article class="input-box">
                        <label for="price">Price:</label>
                        <input type="number" step="any" id="price" name="productPrice" placeholder="Enter a product price" required>
                        </article>

                        <article class="input-box">
                        <label for="description">Description:</label>
                        <textarea id="description" col="50" rows="4" name="productDesc" placeholder="Enter a product description" required></textarea>
                        </article>

                        <article class="input-box">
                        <label for="image">Select an Image (PNG/JGP)</label>
                        <input type="file" id="image" name="addImage" class="file-input">
                        </article>

                        <article class="submit">
                            <button type="submit">Add Product</button>
                        </article>
                    </fieldset>
                </form>
                <?php } 
                if ($_GET["add"] === "user") { ?>
                <form class="form" method="POST">
                <fieldset>
                    <legend>Add User</legend>
                    <article class="column">
                        <article class="input-box">
                            <label for="first-name">First Name:</label>
                            <input type="text" id="first-name" name="firstName" placeholder="Enter First Name" required>
                        </article>
                        <article class="input-box">
                            <label for="last-name">Last Name:</label>
                            <input type="text" id="last-name" name="lastName" placeholder="Enter Last Name" required>
                        </article>   
                    </article>
                    <article class="column">
                    <article class="input-box">
                        <label for="dob">Date of Birth:</label>
                        <input type="date" id="dob" name="dob" required>
                    </article>
                    <article class="input-box">
                        <label for="gender">Gender:</label>
                        <select name="gender" id="gender">
                            <?php $genders = $db->select("gender");
                            foreach ($genders as $gender) { ?>
                            <option value="<?php echo $gender["gender_id"]?>"><?php echo $gender["gender"] ?></option>
                            <?php } ?>
                        </select>
                    </article>
                    </article>
                    <article class="input-box">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" placeholder="Enter a Username" required>
                    </article>
                    <article class="input-box">
                        <label for="add-email">Email:</label>
                        <input type="email" id="add-email"  name="addEmail" placeholder="Enter Email" required>
                    </article>
                    <article class="column">
                        <article class="input-box">
                            <label for="role">Role:</label>
                            <select name="role" id="role">
                                <?php
                                $roles = $db->select("role"); 
                                foreach ($roles as $role) { ?>
                                <option value="<?php echo $role["role_id"] ?>"><?php echo $role["role"] ?></option>
                                <?php } ?>
                            </select>
                        </article>
                        <article class="input-box">
                            <label for="add-pword">Password:</label>
                            <input type="password" id="add-pword" name="addPword" placeholder="Enter Password">
                        </article>
                    </article>
                    <article class="submit">
                        <button type="submit">Add User</button>
                    </article>
                </fieldset>
            </form>
            <?php }
            if ($_GET["add"] === "order") {
            ?>
            <form class="form" method="POST">
                <fieldset>
                    <legend>Add Order</legend>
                    <article class="input-box">
                        <label for="product">Product:</label>
                        <select name="orderProduct" id="product">
                            <?php $products = $db->select("products");
                            foreach ($products as $product) {?>
                            <option value="<?php echo $product["product_id"]?>"><?php echo $product["product_name"] ?></option>
                            <?php } ?>
                        </select>
                    </article>
                    <article class="input-box">
                        <label for="quantity">Quantity:</label>
                        <input type="number" name="quantity" step="1" min="1" max="9" value="1">
                    </article>
                    <article class="input-box">
                        <label for="user">User:</label>
                        <select name="orderUser">
                            <?php $users = $db->select("users");
                            foreach ($users as $user) { ?>
                            <option value="<?php echo $user["user_id"]?>"><?php echo $user["email"] ?></option>
                            <?php } ?>
                        </select>
                    </article>
                    <article class="submit">
                        <button type="submit">Add Order</button>
                    </article>
                </fieldset>
            </form>
            <?php } ?>
            </section>
        </main>
        <?php include "include/footer.php" ?>   
    </body>