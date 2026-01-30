<?php
require_once "../../config/database.php";

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM media WHERE id=?");
$stmt->execute([$id]);
$m = $stmt->fetch();

$file = "../../storage/".$m['type']."s/".$m['file_path'];

header("Content-Disposition: attachment");
readfile($file);
