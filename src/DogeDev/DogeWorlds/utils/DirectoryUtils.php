<?php

declare(strict_types=1);

namespace DogeDev\DogeWorlds\utils;

class DirectoryUtils
{
    public static function recursiveDelete(string $path): void
    {
        if (basename($path) === "." or basename($path) === "..") {
            return;
        }
        foreach (scandir($path) as $item) {
            if ($item === "." or $item === "..") {
                continue;
            }
            if (is_dir($path . DIRECTORY_SEPARATOR . $item)) {
                DirectoryUtils::recursiveDelete($path . DIRECTORY_SEPARATOR . $item);
            }
            if (is_file($path . DIRECTORY_SEPARATOR . $item)) {
                unlink($path . DIRECTORY_SEPARATOR . $item);
            }
        }
        rmdir($path);
    }
}
