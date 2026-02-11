<?php
session_start();

/* Kama user tayari amelogin */
if (isset($_SESSION["user_id"])) {

    if (isset($_SESSION["role"]) && $_SESSION["role"] === "admin") {
        header("Location: admin.php");
    } else {
        header("Location: login.php");
    }

} else {
    /* Kama haja login */
    header("Location: login.php");
}

exit();
?>

