<?php
$host = 'localhost';
$dbname = 'hq2app';
$dbUsername = 'root';
$dbPassword = '';
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
