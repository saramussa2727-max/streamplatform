<?php
require_once "../config/cors.php";
require_once "../middleware/auth.php";
require_once "../config/database.php";

$user = authRequired();
$data = json_decode(file_get_contents("php://input"), true);

$stmt = $pdo->prepare(
    "INSERT IGNORE INTO subscriptions (user_id,channel_id) VALUES (?,?)"
);
$stmt->execute([$user['id'],$data['channel_id']]);

jsonResponse(["message"=>"Subscribed"]);

