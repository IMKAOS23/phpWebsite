<?php
include "include/dbcon.php";

if (isset($_SESSION["userId"])) {
    $userId = $_SESSION["userId"];
} else {
    header("location: index.php");
    exit();
}

if (isset($_GET["orderId"])) {
    $order = $db->select("orders", "*, 
    CASE WHEN status_id = 1 THEN 'Processing'
    WHEN status_id = 2 THEN 'Delivered'
    WHEN status_id = 3 THEN 'Declined'
    WHEN status_id = 4 THEN 'Posted'
    WHEN status_id = 5 THEN 'Returned'
    END AS status_name", "order_id = :order_id", [":order_id" => $_GET["orderId"]], "", false);
    $cartData = json_decode($order["cart"], true);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Order - <?php echo $_GET["orderId"] ?></title>
    <link rel="stylesheet" href="static/css/basicStyle.css">
    <link rel="stylesheet" href="static/css/formStyle.css">
    <link rel="stylesheet" href="static/css/buy.css">
    <link rel="stylesheet" href="static/css/viewOrder.css">
</head>
<body>
    <a href="#main-content" class="skip-nav">Skip to Main Content</a>
    <header>
        <?php 
        if (isset($_SESSION['userId'])) {
            include 'include/logged-in.php';
        }
        else{
            include 'include/not-logged-in.php';
        }
        ?>
    </header>
    <main id="main-content">
        <section class="cart-page">
            <section class="left-block">
                <h1>Your Item</h1>
                <?php 
                foreach ($cartData as $key => $value) {
                    $product_id = $value["product_id"];
                    $quantity = $value["quantity"];
                    $row = $db->select("products", "*", "product_id = :product_id", [":product_id" => $product_id], "", false);?>
                    <article class="item">
                    <div class="image">
                        <img src="<?php echo $row["image_url"] ?>" alt="Photo of <?php echo $row["product_name"]?>">
                    </div>
                    <div class="text">
                        <h4><?php echo $row['product_name'] ?></h4>
                        <h5>£<?php echo $row['price'] ?></h5>
                        <h6 class="quantity">Quantity - <?php echo $quantity ?></h6>
                    </div>
                </article>
                <?php } ?>
            </section>
            <section class="right-block">
                <h1>View Order Information</h1>
                <h2>Total Price - £<?php echo $order["total_price"];?>
                <h3>Estimated Delivery - <?php echo date("Y-m-d", strtotime($order["date_of_purchase"]. "+3 days")) ?></h3>
                <h4>Order Status - <?php echo $order["status_name"] ?></h4>
            </section>
        </section>
    <main>
    <?php include "include/footer.php" ?>
</body>
</html>