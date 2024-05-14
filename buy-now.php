<?php 
include "include/dbcon.php";
if (isset($_SESSION["userId"])) {
    $userId = $_SESSION["userId"];
}

function isValidExpiryDate($dateString) {
    /**
     * Function that checks if Expiry Date inputted is within a certain range using String formatting.
     * Pattern is (2 digit Number 00 - 12) / (Year from 2024 - 2034).
     * 
     * @param string $dateString - The Expiry date.
     * 
     * @return bool Returns True or False.
     */
    $pattern = "/^(0[1-9]|1[0-2])\/(202[4-9]|203[0-4])$/";
    return preg_match($pattern, $dateString) ? true : false;
}
 
if (isset($_GET['productId'])) {
    $productId = $_GET['productId'];
    $quantity = $_GET['quantity'];
    $row = $db->select("products", "*", "product_id = :product_id", [":product_id" => $productId], "", false);
}

if (isset($_SESSION["userId"])) {
    if (isset($_POST['cardholderName']) && isset($_POST['cardNumber']) && isset($_POST['expiryDate']) && isset($_POST['ccv'])) {
        if (strlen($_POST['cardNumber']) !== 16) {
            echo "<script>alert('Card Number Invalid')</script>";
        }
        if (!isValidExpiryDate($_POST['expiryDate'])) {
            echo "<script>alert('Expiry Date out of Range')</script>";
        }
        if (strlen($_POST['ccv']) !== 3) {
            echo "<script>alert('CCV is 3 characters')</script>";
        }
        if (strlen($_POST['cardNumber']) === 16 && isValidExpiryDate($_POST['expiryDate']) && strlen($_POST['ccv']) === 3) {
            $cartData = [];
            $item = [
                'product_id' => $productId,
                'quantity' => $quantity
            ];
            $cartData[$row["product_name"]] = $item;
            $cartJson = json_encode($cartData);
            $purchaseDate = date("Y-m-d H:i:s");
            $total = $row["price"] * $quantity;
            $order = $db->insert("orders", [
                "user_id" => $userId,
                "cart" => $cartJson,
                "total_price" => $total,
                "date_of_purchase" => $purchaseDate,
                "status_id" => 1
            ]);
            if ($order > 0) {
                echo "<script>alert('Order Successful')</script>";
            }
        }
    }
} else {
    echo "<script>alert('You Must be Logged in to buy Items');
        setTimeout(function() {
            window.location.href = 'index.php';
        }, 1);
        </script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buy Now - <?php echo $row['product_name'] ?></title>
    <link rel="stylesheet" href="static/css/basicStyle.css">
    <link rel="stylesheet" href="static/css/formStyle.css">
    <link rel="stylesheet" href="static/css/buy.css">
</head>
<body>
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
    <main>
        <section class="cart-page">
            <section class="left-block">
                <h1>Your Item</h1>
                <article class="item">
                    <div class="image">
                        <img src="<?php echo $row["image_url"] ?>" alt="<?php echo $row["product_name"]?>">
                    </div>
                    <div class="text">
                        <h4><?php echo $row['product_name'] ?></h4>
                        <h5>£<?php echo $row['price'] ?></h5>
                        <h6 class="quantity">Quantity - <?php echo $quantity ?></h6>
                    </div>
                </article>
            </section>
            <section class="right-block">
                <p>Total - £<?php echo $row['price'] * $quantity ?></p>
                <form action="#" class="buy-form" method="POST">
                    <article class="input-box">
                        <label for="cardholderName">Cardholder Name</label>
                        <input type="text" id="cardholderName" name="cardholderName" placeholder="Mr John Doe"required>
                    </article>
                    <article class="input-box">
                        <label for="cardNumber">Card Number</label>
                        <input type="number" id="cardNumber" name="cardNumber" placeholder="1234123412341234" required>
                    </article>
                    <article class="input-box">
                        <label for="expiryDate">Expiry Date</label>
                        <input type="text" id="expiryDate" name="expiryDate" placeholder="MM/YYYY" required>
                    </article>
                    <article class="input-box">
                        <label for="ccv">CCV</label>
                        <input type="number" id="ccv" name="ccv" required>
                    </article>
                    <article class="submit">
                        <button type="submit">Place Order</button>
                    </article>
                </form>
            </section>
        </section>
    <main>
    <?php include "include/footer.php" ?>
</body>
</html>