<?php
include "include/dbcon.php";
if (!isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] !== true) {
    header("Location: index.php");
    exit();
}

$goto = "adminhome.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Terminal - Orders</title>
    <link rel="stylesheet" href="static/css/basicStyle.css">
    <link rel="stylesheet" href="static/css/formStyle.css">
    <link rel="stylesheet" href="static/css/home.css">
    <link rel="stylesheet" href="static/css/admin.css">
</head>
<body>
    <a href="#main-content" class="skip-nav">Skip to main content</a>
    <?php include "include/admin.php" ?>

    <main class="align-center" id="main-content">
        <h1>Admin Orders Panel</h1>
        <p>Here you can add, view, edit and delete Orders</p>

        <section class="view-admin">
            <h2>Orders Table - Add/View/Edit/Delete Products</h2>
            <?php
             $orders = $db->select("orders", "*, CASE WHEN status_id = 1 THEN 'Pending'
             WHEN status_id = 2 THEN 'Delivered'
             WHEN status_id = 3 THEN 'Declined'
             WHEN status_id = 4 THEN 'Posted'
             WHEN status_id = 5 THEN 'Returned'
             END AS status_name") ?>
             <form action="addForm.php" class="add-form" method="GET">
                <input type="hidden" name="add" value="order">
                <button type="submit">Add New Order</button>
             </form>
             <table class="table">
                <thead>
                    <tr class="table-head">
                        <th>Order ID</th>
                        <th>Cart</th>
                        <th>Email</th>
                        <th>Purchase Date</th>
                        <th>Status</th> 
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order) { ?>
                        <?php 
                        $user = $db->select("users", "*", "user_id = :user_id", [":user_id" => $order["user_id"]], "", false);
                        ?>
                        <tr>
                            <td><?php echo $order["order_id"] ?></td>
                            <td class="cart">
                                <ul>
                                <?php
                                $cart = json_decode($order["cart"], true);
                                foreach ($cart as $itemName => $details) { ?>
                                    <li><strong><?php echo $itemName ?></strong></li>
                                
                                <?php
                                    foreach ($details as $key => $value) { ?>
                                    <li><?php echo $key . " - " . $value ?></li>
                                <?php }} ?>
                                </ul>
                            </td>
                            <td><?php echo $user["email"] ?></td>
                            <td><?php echo $order["date_of_purchase"] ?></td>
                            <td><?php echo $order["status_name"] ?></td>
                            <td>
                                <form action="editform.php" class="edit" method="GET">
                                    <input type="hidden" name="edit" value="<?php echo $order["order_id"] ?>">
                                    <input type="hidden" name="editTable" value="order">
                                    <button type="submit">Edit Order</button>
                                </form>
                            </td>
                            <td>
                                <form action="adminDelete.php" method="POST" id="deleteForm_<?php echo $order['order_id'] ?>" class="delete">
                                    <input type="hidden" name="orderIdDelete" value="<?php echo $order['order_id'] ?>">
                                    <input type="hidden" name="deleting" value="true">
                                    <button type="button" onclick="confirmDelete('deleteForm_<?php echo $order['order_id'] ?>','<?php echo $order['order_id'] ?>')">Delete Order</button>
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
