<?php
require_once "../../config/cors.php";
require_once "../../middleware/auth.php";
require_once "../../config/database.php";

$user = authRequired();

$stmt = $pdo->prepare("SELECT id,name,email,role FROM users WHERE id=?");
$stmt->execute([$user['id']]);

jsonResponse($stmt->fetch(PDO::FETCH_ASSOC));
