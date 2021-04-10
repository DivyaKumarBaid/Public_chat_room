<?php
session_start();
date_default_timezone_set('Asia/Kolkata');
if (isset($_SESSION['name'])) {
    $text = $_POST['text'];

    $text_message = "<div class='msgln' style='background: rgba(129, 126, 126, 0.3);width:max-content;padding:6px;border:0px solid black;border-radius:10px;backdrop-filter:blur(10px);border-left:4px solid rgba(106, 240, 106, 0.801);margin-bottom:12px;'><b class='user-name'>" . $_SESSION['name'] . "</b> " . stripslashes(htmlspecialchars($text)) . "&emsp; <span class='chat-time'>" . date("g:i A") . "</span> <br></div>";
    file_put_contents("log.html", $text_message, FILE_APPEND | LOCK_EX);
}
