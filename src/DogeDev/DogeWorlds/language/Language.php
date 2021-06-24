<?php

declare(strict_types=1);

namespace DogeDev\DogeWorlds\language;

use InvalidArgumentException;
use pocketmine\utils\TextFormat;

class Language
{
    public const MESSAGE_TYPE_DEFAULT = 0;
    public const MESSAGE_TYPE_ERROR = 1;

    protected string $lang;
    protected array $errors;
    protected array $messages;

    public function __construct(string $lang, string $dataPath)
    {
        $this->lang = $lang;
        if (!file_exists($dataPath . $lang . ".json")) {
            throw new InvalidArgumentException($lang . "'s language path was not found, make sure your files are up to date.");
        }
        $parsed = json_decode(file_get_contents($dataPath . $lang . ".json"), true);
        $this->errors = $parsed["errors"];
        $this->messages = $parsed["messages"];
    }

    public function getLang(): string
    {
        return $this->lang;
    }

    public function getMessage(string $message, array $variables = [], int $type = Language::MESSAGE_TYPE_DEFAULT): string
    {
        $category = $type === Language::MESSAGE_TYPE_DEFAULT ? $this->messages : $this->errors;
        return isset($category[$message]) ? TextFormat::colorize(str_replace(array_keys($variables), array_values($variables), $category[$message])) : "Translation not found.";
    }

    public function getAllMessages(): array
    {
        return $this->messages;
    }

    public function getAllErrors(): array
    {
        return $this->errors;
    }
}
