<?php
session_start();
session_destroy();
header("Location: ../LogIN/login.html");
exit();
?>