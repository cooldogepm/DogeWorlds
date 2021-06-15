<?php

declare(strict_types=1);

namespace DogeDev\DogeWorlds\utils;

use pocketmine\world\World;

class DifficultyUtils
{
    public static function getDifficultyNameFromInteger(int $difficulty): string
    {
        switch ($difficulty) {
            case World::DIFFICULTY_PEACEFUL:
                return "Peaceful";
            case World::DIFFICULTY_EASY:
                return "Easy";
            case World::DIFFICULTY_NORMAL:
                return "Normal";
            case World::DIFFICULTY_HARD:
                return "Hard";
            default:
                return "Unknown";
        }
    }
}
