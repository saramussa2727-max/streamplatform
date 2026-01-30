<?php
require_once __DIR__ . "/../config/jwt.php";
require_once __DIR__ . "/../utils/response.php";

function authRequired() {
    $headers = getallheaders();

    if (!isset($headers['Authorization'])) {
        jsonResponse(["error" => "Unauthorized"], 401);
    }

    $token = str_replace("Bearer ", "", $headers['Authorization']);

    $payload = json_decode(
        base64_decode(explode('.', $token)[1]),
        true
    );

    if ($payload['exp'] < time()) {
        jsonResponse(["error" => "Token expired"], 401);
    }

    return $payload;
}
