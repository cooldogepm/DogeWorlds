<?php

declare(strict_types=1);

namespace DogeDev\DogeWorlds\language;

use InvalidArgumentException;
use pocketmine\utils\TextFormat;

final class Language
{
    protected string $lang;
    protected ?array $errors;
    protected ?array $messages;
    protected ?array $usages;
    protected ?array $aliases;

    public function __construct(string $lang, string $dataPath)
    {
        $this->lang = $lang;
        if (!file_exists($dataPath . $this->getLang() . ".json")) {
            throw new InvalidArgumentException($this->getLang() . "'s language file was not found.");
        }
        $parsed = json_decode(file_get_contents($dataPath . $lang . ".json"), true);
        $this->errors = $parsed["errors"] ?? null;
        $this->messages = $parsed["messages"] ?? null;
        $this->usages = $parsed["usages"] ?? null;
        $this->aliases = $parsed["aliases"] ?? null;
        if (!$this->getMessages() || !$this->getErrors() || !$this->getUsages() || !$this->getAliases()) {
            throw new InvalidArgumentException($this->getLang() . " is missing translations, an update to your configuration files is required.");
        }
    }

    public function getLang(): string
    {
        return $this->lang;
    }

    public function getMessages(): ?array
    {
        return $this->messages;
    }

    public function getErrors(): ?array
    {
        return $this->errors;
    }

    public function getUsages(): ?array
    {
        return $this->usages;
    }

    public function getAliases(): ?array
    {
        return $this->aliases;
    }

    public function getMessage(string $message, array $variables = [], int $type = Messages::MESSAGE_CATEGORY_REGULAR): string
    {
        return $this->getString($message, $type) ? TextFormat::colorize(str_replace(array_keys($variables), array_values($variables), $this->getString($message, $type))) : "Translation not found.";
    }

    public function getString(string $message, int $type = Messages::MESSAGE_CATEGORY_REGULAR): ?string
    {
        return $this->getMessagesFromType($type)[$message] ?? null;
    }

    public function getMessagesFromType(int $type): ?array
    {
        switch ($type) {
            case Messages::MESSAGE_CATEGORY_REGULAR:
                return $this->getMessages();
            case Messages::MESSAGE_CATEGORY_ERROR:
                return $this->getErrors();
            case Messages::MESSAGE_CATEGORY_USAGE:
                return $this->getUsages();
            case Messages::MESSAGE_CATEGORY_ALIAS:
                return $this->getAliases();
            default:
                return null;
        }
    }

    public function getArray(string $message, int $type = Messages::MESSAGE_CATEGORY_REGULAR): ?array
    {
        return $this->getMessagesFromType($type)[$message] ?? null;
    }

    public function getInteger(string $message, int $type = Messages::MESSAGE_CATEGORY_REGULAR): ?int
    {
        return $this->getMessagesFromType($type)[$message] ?? null;
    }
}
