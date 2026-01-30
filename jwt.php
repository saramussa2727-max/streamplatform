<?php
define("JWT_SECRET", "super_strong_secret_ChangeMe");
define("JWT_ALGO", "HS256");
define("JWT_EXPIRE", 3600);

function base64UrlEncode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function generateJWT($payload) {
    $header = base64UrlEncode(json_encode(["alg"=>"HS256","typ"=>"JWT"]));
    $payload = base64UrlEncode(json_encode($payload));
    $signature = base64UrlEncode(
        hash_hmac("sha256", "$header.$payload", JWT_SECRET, true)
    );
    return "$header.$payload.$signature";
}

function verifyJWT($token) {
    [$h,$p,$s] = explode('.', $token);
    $valid = base64UrlEncode(
        hash_hmac("sha256", "$h.$p", JWT_SECRET, true)
    );
    if ($s !== $valid) return false;
    return json_decode(base64_decode($p), true);
}
