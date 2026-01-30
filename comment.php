<?php
require_once "../middleware/auth.php";
require_once "../config/database.php";

$user = authRequired();
$data = json_decode(file_get_contents("php://input"), true);

$stmt = $pdo->prepare(
 "INSERT INTO comments (user_id,media_id,comment) VALUES (?,?,?)"
);
$stmt->execute([$user['id'],$data['media_id'],$data['comment']]);

jsonResponse(["message"=>"Comment added"]);

