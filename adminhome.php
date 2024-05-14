<?php
include "include/dbcon.php";

if (!isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] !=- true) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Terminal - Home</title>
    <link rel="stylesheet" href="static/css/basicStyle.css">
    <link rel="stylesheet" href="static/css/formStyle.css">
    <link rel="stylesheet" href="static/css/home.css">
    <link rel="stylesheet" href="static/css/admin.css">
</head>
<body>
    <a href="#main-content" class="skip-nav">Skip to main content</a>
    <?php include "include/admin.php" ?>

    <main class="align-center" id="main-content">
        <h1>Welcome to the Admin Panel</h1>
        <p>Here you can add, view, edit and delete Users, Orders and products for sale</p>

        <section class="view-admin">
            <h2>Products Table - Add/View/Edit/Delete Products</h2>
            <?php
             $products = $db->select("products") ?>
             <form action="addForm.php" class="add-form" method="GET">
                <input type="hidden" name="add" value="product">
                <button type="submit">Add New Product</button>
             </form>
             <table class="table">
                <thead>
                    <tr class="table-head">
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Description</th>
                        <th>Image</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product) { ?>
                        <tr>
                            <td><?php echo $product["product_id"] ?></td>
                            <td><?php echo $product["product_name"] ?></td>
                            <td><?php echo $product["price"] ?></td>
                            <td><?php echo $product["description"] ?></td>
                            <td><img src="<?php echo $product["image_url"] ?>" width="100" height="100" alt="Product Image -    <?php echo $product["product_name"] ?>"></td>
                            <td>
                                <form action="editform.php" method="GET" class="edit">
                                    <input type="hidden" name="edit" value="<?php echo $product["product_id"] ?>">
                                    <input type="hidden" name="editTable" value="product">
                                    <button type="submit">Edit Product</button>
                                </form>
                            </td>
                            <td>
                                <form action="adminDelete.php" method="POST" id="deleteForm_<?php echo $product['product_id'] ?>" class="delete">
                                    <input type="hidden" name="productIdDelete" value="<?php echo $product['product_id'] ?>">
                                    <input type="hidden" name="deleting" value="true">
                                    <button type="button" onclick="confirmDelete('deleteForm_<?php echo $product['product_id'] ?>','<?php echo $product['product_name'] ?>')">Delete Product</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
             </table>
        </section>
    </main>

    <?php include "include/footer.php" ?>
    <script src="static/js/admin.js"></script>
</body>
</html>
