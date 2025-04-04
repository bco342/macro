<?php declare(strict_types=1);

namespace App\Service\Importer;

class Normalizer
{
    public static function normalizePhones(string $value): string
    {
        return preg_replace(['~[^\d,]~', '~^8~'], ['', '7'], $value);
    }

    /**
     * @param string $value
     * @return int
     */
    public static function normalizePrice(string $value): int
    {
        return (int)preg_replace('~\D~', '', $value);
    }

}