<?php
require_once "../../config/cors.php";
require_once "../../config/database.php";
require_once "../../config/jwt.php";
require_once "../../utils/response.php";

/* -------- SAFE INPUT HANDLING -------- */
$rawInput = file_get_contents("php://input");
$data = [];

/* Try JSON first */
if (!empty($rawInput)) {
    $json = json_decode($rawInput, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
        $data = $json;
    }
}

/* Fallback to form-data / x-www-form-urlencoded */
if (empty($data)) {
    $data = $_POST ?? [];
}

/* -------- VALIDATION (NO WARNINGS) -------- */
$email    = trim($data['email']    ?? '');
$password = trim($data['password'] ?? '');

if ($email === '' || $password === '') {
    jsonResponse([
        "error" => "Email and password are required"
    ], 422);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse([
        "error" => "Invalid email format"
    ], 422);
}

/* -------- FETCH USER -------- */
$stmt = $pdo->prepare(
    "SELECT id, name, email, password, role
     FROM users
     WHERE email = ?"
);
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($password, $user['password'])) {
    jsonResponse([
        "error" => "Invalid email or password"
    ], 401);
}

/* -------- CREATE JWT -------- */
$payload = [
    "id"   => $user['id'],
    "role" => $user['role'],
    "exp"  => time() + JWT_EXPIRE
];

$token = generateJWT($payload);

/* -------- SUCCESS -------- */
jsonResponse([
    "message" => "Login successful",
    "token"   => $token,
    "user"    => [
        "id"    => $user['id'],
        "name"  => $user['name'],
        "email" => $user['email'],
        "role"  => $user['role']
    ]
]);
