<?php

declare(strict_types=1);

namespace DogeDev\DogeWorlds\command\subcommand;

use DogeDev\DogeWorlds\command\WorldCommand;
use DogeDev\DogeWorlds\DogeWorlds;
use DogeDev\DogeWorlds\language\Language;
use pocketmine\command\CommandSender;

abstract class WorldSubCommand
{
    protected WorldCommand $parentCommand;
    protected string $name;
    protected string $permission;
    protected array $aliases;

    public function __construct(WorldCommand $parentCommand, string $name, string $permission, array $aliases)
    {
        $this->parentCommand = $parentCommand;
        $this->name = $name;
        $this->permission = $permission;
        $this->aliases = $aliases;
    }

    final public function execute(CommandSender $sender, array $args): void
    {
        if (!$this->testPermission($sender)) {
            $this->sendPermissionError($sender);
            return;
        }
        $this->onRun($sender, $args);
    }

    protected function testPermission(CommandSender $sender): bool
    {
        return $sender->hasPermission($this->getPermission());
    }

    public function getPermission(): string
    {
        return $this->permission;
    }

    protected function sendPermissionError(CommandSender $sender): void
    {
        $sender->sendMessage($this->getOwningPlugin()->getLanguage()->getMessage("permissionLack", [], Language::MESSAGE_TYPE_ERROR));
    }

    abstract protected function onRun(CommandSender $sender, array $args): void;

    public function getName(): string
    {
        return $this->name;
    }

    public function getAliases(): array
    {
        return $this->aliases;
    }

    public function getOwningPlugin(): DogeWorlds
    {
        return $this->getParentCommand()->getOwningPlugin();
    }

    public function getParentCommand(): WorldCommand
    {
        return $this->parentCommand;
    }
}
