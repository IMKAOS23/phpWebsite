<?php
include 'include/dbcon.php';
if (!isset($_SESSION['userId'])) {
    header('location: index.php');
    exit();
}

$row = $db->select("users", "*", "user_id = :user_id", [":user_id" => $_SESSION["userId"]], "", false);
$username = $row['username'];
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Home</title>
        <link rel="stylesheet" href="static/css/basicStyle.css">
        <link rel="stylesheet" href="static/css/formStyle.css">
        <link rel="stylesheet" href="static/css/home.css">
    </head>
    <body>
        <a href="#main-content" class="skip-nav">Skip to main content</a>
        <?php
        include 'include/logged-in.php';
        ?>
        <main>
        <section class="welcome-text">
                <h1>Welcome <?php echo $username ?></h1>
                <h2>Have a look at the featured Items</h2>
            </section>

            <section class="featured-container">
                <h3>Featured Items</h3>
                <section class="featured-grid-container">
                    <?php
                    // Just used to randomly select 3 items to Feature on the home page
                    $rows = $db->select("products", "*", "", [], "ORDER BY RAND() LIMIT 3");
                    foreach ($rows as $row) {?>
                        <article class="item">
                            <div class="content">
                                <img src="<?php echo $row["image_url"] ?>" alt="<?php echo $row['product_name'] ?>">
                                <h4><?php echo $row['product_name'] ?></h4>
                                <h5>£<?php echo $row['price'] ?></h5>
                                <p><?php echo $row['description'] ?></p>
                            </div>
                            <div class="buttons">
                                <form action="buy-now.php" method="GET">
                                    <input type="hidden" name="productId" value="<?php echo $row['product_id'] ?>">
                                    <label for="quantity-<?php echo $row['product_id'] ?>">Quantity:</label>
                                    <select name="quantity" id="quantity-<?php echo $row['product_id'] ?>">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                        <option value="7">7</option>
                                        <option value="8">8</option>
                                        <option value="9">9</option>
                                    </select>
                                    <button type="submit">Buy Now!</button>
                                </form>
                            </div>
                        </article>
                    <?php } ?>
                </section>
                <section class="view-products">
                        <a href="products.php">View All</a>
                </section>

            <section class="about-us">
                <article class="about-us-content">
                    <h1 class="about-us-heading">About VGEmporium</h1>
                    <p class="about-us-text">VGEmporium is more than just a store; it's a haven for gamers and collectors alike. As a start-up venture deeply entrenched in the world of video game memorabilia, we're on a mission to redefine how enthusiasts engage with gaming culture.</p>
                    <p class="about-us-text">Our vision is to become the ultimate destination for gamers seeking unique and authentic collectibles. From vintage classics to the latest releases, we pride ourselves on curating a diverse selection that caters to every gaming aficionado's taste.</p>
                    <p class="about-us-text">As we continue to grow, our commitment to quality, authenticity, and customer satisfaction remains unwavering. Join us on our journey as we strive to elevate the gaming memorabilia experience to new heights.</p>
                </article>
            </section>
        </main>
        <?php include 'include/footer.php' ?>
        <script src="static/js/script.js"></script>
    </body>
</html>
