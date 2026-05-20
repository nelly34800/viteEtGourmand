<?php

namespace App\Helper;

class ValidatorHelper
{
    // Valide si bien UUID
    public static function validateUuid(string $id): void
    {
        if (!preg_match(
            '/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-5][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}$/',
            $id
        )) {
            throw new \InvalidArgumentException("Invalid UUID format");
        }
    }
    // Valide un tableau d'UUID
    public static function validateUuidArray(array $ids): void
{
    foreach ($ids as $id) {
        self::validateUuid($id);
    }
}
   // Valide le format et le nom de l'image
public static function validatePicture(string $filename): void
{
    $filename = trim($filename);

    if (empty($filename)) {
        throw new \InvalidArgumentException("Image required");
    }
    // Autorise uniquement :lettres, chiffres, tirets, underscores + extension image
    if (!preg_match('/^[a-zA-Z0-9_-]+\.(jpg|jpeg|png|webp)$/i', $filename)) {
        throw new \InvalidArgumentException("Invalid image format");
    }
  }
}
