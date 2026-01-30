<?php
require_once __DIR__."/../utils/response.php";

function adminOnly($user) {
    if (($user['role'] ?? '') !== 'admin') {
        jsonResponse([
            "error" => "Admin access only"
        ], 403);
    }
}
