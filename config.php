<?php
// Sesuaikan user/password dengan MySQL Anda (XAMPP default: root, tanpa password)
$pdo = new PDO('mysql:host=localhost;dbname=mindcampus;charset=utf8mb4', 'root', '160307', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
