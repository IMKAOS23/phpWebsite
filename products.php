<?php 
include 'include/dbcon.php';
$goTo = 'products.php';

/*
Improvements
----------------
# Search Function
# Complete Mobile Style
*/

// Used for Pagination - Calculating Total Pages Required to Show All Items
$count = $db->select("products", "Count(*) as count", "", [], "", false);
$totalPages = ceil($count['count'] / 9);

// Is used to Calculate Offset
if (isset($_GET['page'])) {
    $currentPage = intval($_GET['page']);
}
else {
    $currentPage = 1;
}
$offset = ($currentPage -1) * 9;
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Products</title>
        <link rel="stylesheet" href="static/css/basicStyle.css">
        <link rel="stylesheet" href="static/css/formStyle.css">
        <link rel="stylesheet" href="static/css/product.css">
    </head>
    <body>
        <a href="#main-content" class="skip-nav">Skip to Main Content</a>
        <?php
        // Navbar
        if (isset($_SESSION['userId'])) {
            include 'include/logged-in.php';
        }
        else {
            include 'include/not-logged-in.php';
        }
        ?>
        <main id="main-content">
            <h1>View Products</h1>

            <section class="product-container">
                <?php
                // Simple Code to Generate the Articles with the Required pieces of Information from the SQL Query below.
                $rows = $db->select("products", "*", "", [], "LIMIT $offset, 9");
                if (empty($rows)) {
                    echo "No Results Found!";
                }
                else {
                foreach ($rows as $row) { ?>
                <article class="product">
                    <div class="content">
                        <img src="<?php echo $row['image_url']?>" alt="Photo of <?php echo $row['product_name']?>">
                        <h2><?php echo $row['product_name'] ?></h2>
                        <h3>£<?php echo $row['price'] ?></h3>
                        <p><?php echo $row['description']?></p>
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
                <?php }}?>
            </section>

            <section class="pagination">
                <form action="products.php" class="pagination-form" method="GET">
                    <label for="page">Go To Page:</label>
                    <select name="page" id="page" class="pagination-select">
                        <?php 
                        // This is Just Programatically create options
                        for ($i = 1; $i <= $totalPages; $i++) {
                            // Ternary Condition - Basically making it so it shows the page your on as selected for ease of use
                            $selected = ($i === $currentPage) ? 'selected' : '';
                            echo "<option value=\"$i\" $selected>Page $i</option>";
                        }
                        ?>
                    </select>
                    <button type="submit">Go!</button>
                </form>
        </main>
        <?php include 'include/footer.php' ?>
    </body>
</html>