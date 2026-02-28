<?php

namespace App\Helper;

class ResponseHelper
{
    public static function json($data, int $status = 200): void
    {
        http_response_code($status);
        echo json_encode($data);
        exit;
    }
}