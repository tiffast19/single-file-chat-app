 <?php

    $host = "localhost";
    $user = "uctcv_chat_user";
    $password = "O#k32wh49";
    $db_name = "chat_app_tiffast";

    $db = mysqli_connect($host, $user, $password, $db_name);
    // Turn on extended character sets for Emoji support
    $db->set_charset("utf8mb4");

    if ($db->connect_error) {
        die('Connection Error (' . $db->connect_errno . ') ' . $db->connect_error);
        // } else {
        //     echo 'yay';
    }




    ?>