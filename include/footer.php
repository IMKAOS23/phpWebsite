<footer class="footer">
    <section class="footer-container">
        <section class="footer-logo">
            <img src="static/img/logo-no-bg.png" alt="Logo for Website - VGEmporium" width="250px" height="30px">
        </section>
        <section class="footer-links">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="products.php">Products</a></li>
                <li><a href="account.php">Account</a></li>
                <li><a href="#">Contact Us</a></li>
                <?php
                if (isset($_SESSION["isAdmin"]) && $_SESSION["isAdmin"] === true) { ?>
                    <li><a href="adminhome.php">Go to Admin</a></li>
                <?php }?>
            </ul>
        </section>
        <section class="footer-social">
            <a href="#"><img src="static/img/facebook-logo.png" alt="Facebook"></a>
            <a href="#"><img src="static/img/instagram-logo.png" alt="Instagram"></a>
            <a href="#"><img src="static/img/x-logo.png" alt="X"></a>
        </section>
    </section>
</footer>