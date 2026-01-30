<?php
require_once "../../config/database.php";

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM media WHERE id=?");
$stmt->execute([$id]);
$media = $stmt->fetch();

$file = "../../storage/".$media['type']."s/".$media['file_path'];

header("Content-Type: video/mp4");
readfile($file);
