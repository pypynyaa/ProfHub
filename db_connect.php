<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'pypynyaa';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Ошибка подключения к базе данных: " . $conn->connect_error);
}

$conn->set_charset("utf8");
?> 