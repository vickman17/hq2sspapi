<?php
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'hq2app';

$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    die('Connection failed: ' . mysqli_connect_error());
}
?>
