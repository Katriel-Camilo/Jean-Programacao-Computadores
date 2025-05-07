<?php
session_start();
//verificar se foi click (POST)
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    session_destroy();
    header('location:../index.php?saiu=ok');
}
