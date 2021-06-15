<?php

declare(strict_types=1);

namespace DogeDev\DogeWorlds\command\subcommand;

use DogeDev\DogeWorlds\command\WorldCommand;
use DogeDev\DogeWorlds\DogeWorlds;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

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
        $sender->sendMessage(TextFormat::RED . "You don't have enough permissions to perform this command.");
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

    public function getPlugin(): DogeWorlds
    {
        return $this->getParentCommand()->getPlugin();
    }

    public function getParentCommand(): WorldCommand
    {
        return $this->parentCommand;
    }
}
