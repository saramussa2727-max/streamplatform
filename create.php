<?php
require_once "../config/cors.php";
require_once "../middleware/auth.php";
require_once "../config/database.php";

$user = authRequired();
$data = json_decode(file_get_contents("php://input"), true);

$stmt = $pdo->prepare(
    "INSERT INTO channels (user_id,name,description) VALUES (?,?,?)"
);
$stmt->execute([$user['id'],$data['name'],$data['description']]);

jsonResponse(["message"=>"Channel created"]);


