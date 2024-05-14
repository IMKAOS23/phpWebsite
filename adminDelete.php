<?php 
include "include/dbcon.php";

if (isset($_SESSION["isAdmin"]) !== true) {
    header("location: index.php");
    exit();
}

if (!isset($_POST["deleting"])) {
    header("location: adminhome.php");
}

if (isset($_POST["productIdDelete"])) {
    $db->delete("products", "product_id = :product_id", [":order_id" => $_POST["productIdDelete"]]);
    header("adminhome.php");
    exit();
}

if (isset($_POST["userIdDelete"])) {
    $db->delete("users", "user_id = :user_id", [":user_id" => $_POST["userIdDelete"]]);
    header("location: adminusers.php");
    exit();
}

if (isset($_POST["orderIdDelete"])) {
    $db->delete("orders", "order_id = :order_id", [":order_id" => $_POST["orderIdDelete"]]);
    header("location: adminorders.php");
    exit();
}