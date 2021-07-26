<?php

declare(strict_types=1);

namespace DogeDev\DogeWorlds\command;

use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginOwned;

abstract class SubCommand implements PluginOwned
{
    protected Plugin $owningPlugin;
    protected string $name;
    protected ?string $permission;
    protected ?string $usage;
    protected ?string $permissionMessage;
    protected array $aliases;

    public function __construct(Plugin $owningPlugin, string $name, ?string $permission = null, ?string $usage = null, ?string $permissionMessage = null, array $aliases = [])
    {
        $this->owningPlugin = $owningPlugin;
        $this->name = $name;
        $this->permission = $permission;
        $this->usage = $usage;
        $this->permissionMessage = $permissionMessage;
        $this->aliases = $aliases;
        $this->prepare();
    }

    abstract public function prepare(): void;

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

    public function getPermission(): ?string
    {
        return $this->permission;
    }

    public function setPermission(?string $permission): void
    {
        $this->permission = $permission;
    }

    protected function sendPermissionError(CommandSender $sender): void
    {
        if ($this->getPermissionMessage()) {
            $sender->sendMessage($this->getPermissionMessage());
        }
    }

    public function getPermissionMessage(): ?string
    {
        return $this->permissionMessage;
    }

    public function setPermissionMessage(?string $permissionMessage): void
    {
        $this->permissionMessage = $permissionMessage;
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

    // For intellisense

    public function setAliases(array $aliases): void
    {
        $this->aliases = $aliases;
    }

    public function getOwningPlugin(): PluginBase
    {
        return $this->owningPlugin;
    }

    public function getUsage(): string
    {
        return $this->usage;
    }

    public function setUsage(string $usage): void
    {
        $this->usage = $usage;
    }
}
