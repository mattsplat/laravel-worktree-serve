<?php

namespace Mattcoleman\PrServe\Service;

class DirectoryValidator
{
    public static function isGitRepository(string $path): bool
    {
        return is_dir($path . '/.git');
    }
}