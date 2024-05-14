<?php 
include "include/dbcon.php";

if (isset($_SESSION["userId"])) {
    $db->delete("users", "user_id = :user_id", [":user_id" => $_SESSION["userId"]]);
    header("location: logout.php");
    exit();
} else {
    header("location: index.php");
    exit();
}