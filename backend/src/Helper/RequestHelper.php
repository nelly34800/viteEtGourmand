<?php

namespace App\Helper;

class RequestHelper
{
    public static function getJson(): array
    {
    $raw = file_get_contents("php://input");

    $data = json_decode($raw, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        ResponseHelper::json(['error' => 'Invalid JSON'], 400);
        exit;
    }

    return $data ?? [];
    }
}
