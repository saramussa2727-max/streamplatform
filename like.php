<?php
require_once "../middleware/auth.php";
require_once "../config/database.php";

$user = authRequired();
$data = json_decode(file_get_contents("php://input"), true);

$stmt = $pdo->prepare(
 "INSERT IGNORE INTO likes (user_id,media_id) VALUES (?,?)"
);
$stmt->execute([$user['id'],$data['media_id']]);

jsonResponse(["message"=>"Liked"]);

