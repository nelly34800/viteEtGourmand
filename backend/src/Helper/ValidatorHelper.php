<?php

namespace App\Helper;

class ValidatorHelper
{
    public static function validateUuid(string $id): void
    {
        if (!preg_match(
            '/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-5][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}$/',
            $id
        )) {
            throw new \InvalidArgumentException("Invalid UUID format");
        }
    }
}