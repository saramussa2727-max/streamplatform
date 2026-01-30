<?php
require_once "../config/cors.php";
require_once "../middleware/auth.php";
require_once "../middleware/admin.php";
require_once "../config/database.php";
require_once "../utils/response.php";

/* -------- AUTH -------- */
$user = authRequired();

/* -------- ADMIN CHECK -------- */
adminOnly($user);

/* -------- VALIDATION -------- */
if (
    empty($_POST['channel_id']) ||
    empty($_POST['title']) ||
    empty($_POST['type']) ||
    empty($_FILES['file'])
) {
    jsonResponse(["error" => "Missing required fields"], 422);
}

$type = $_POST['type']; // video | audio

if (!in_array($type, ['video','audio'])) {
    jsonResponse(["error" => "Invalid media type"], 422);
}

/* -------- FILE SECURITY -------- */
$allowedTypes = [
    'video' => ['video/mp4'],
    'audio' => ['audio/mpeg','audio/mp3']
];

if (!in_array($_FILES['file']['type'], $allowedTypes[$type])) {
    jsonResponse(["error" => "Invalid file type"], 415);
}

/* 50MB limit */
if ($_FILES['file']['size'] > 50 * 1024 * 1024) {
    jsonResponse(["error" => "File too large"], 413);
}

/* -------- STORE FILE -------- */
$dir = "../../storage/{$type}s/";
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

$filename = uniqid()."_".basename($_FILES['file']['name']);
$path = $dir.$filename;

move_uploaded_file($_FILES['file']['tmp_name'], $path);

/* -------- SAVE DB -------- */
$stmt = $pdo->prepare(
    "INSERT INTO media (channel_id, title, type, file_path)
     VALUES (?, ?, ?, ?)"
);
$stmt->execute([
    $_POST['channel_id'],
    $_POST['title'],
    $type,
    $filename
]);

jsonResponse(["message" => "Media uploaded successfully"], 201);

