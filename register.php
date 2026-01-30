<?php
require_once "../../config/cors.php";
require_once "../../config/database.php";
require_once "../../utils/response.php";

/* -------- SAFE INPUT HANDLING -------- */
$rawInput = file_get_contents("php://input");
$data = [];

if (!empty($rawInput)) {
    $json = json_decode($rawInput, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
        $data = $json;
    }
}

if (empty($data)) {
    $data = $_POST ?? [];
}

/* -------- VALIDATION -------- */
$name     = trim($data['name'] ?? '');
$email    = trim($data['email'] ?? '');
$password = trim($data['password'] ?? '');

if ($name === '' || $email === '' || $password === '') {
    jsonResponse(["error" => "Name, email and password are required"], 422);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(["error" => "Invalid email format"], 422);
}

/* -------- CHECK DUPLICATE -------- */
$check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$check->execute([$email]);

if ($check->rowCount() > 0) {
    jsonResponse(["error" => "Email already registered"], 409);
}

/* -------- DETERMINE ROLE -------- */
/* If no users exist yet → first user is ADMIN */
$countStmt = $pdo->query("SELECT COUNT(*) FROM users");
$userCount = (int) $countStmt->fetchColumn();

$role = ($userCount === 0) ? 'admin' : 'user';

/* -------- CREATE USER -------- */
$hash = password_hash($password, PASSWORD_BCRYPT);

$stmt = $pdo->prepare(
    "INSERT INTO users (name, email, password, role)
     VALUES (?, ?, ?, ?)"
);
$stmt->execute([$name, $email, $hash, $role]);

jsonResponse([
    "message" => "User registered successfully",
    "role"    => $role
], 201);
