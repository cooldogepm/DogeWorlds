<?php

declare(strict_types=1);

namespace DogeDev\DogeWorlds\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;

abstract class BaseCommand extends Command implements PluginOwned
{
    protected Plugin $owningPlugin;
    /**
     * @var SubCommand[]
     */
    protected array $subCommands;

    public function __construct(Plugin $plugin, string $name, string $description = "", ?string $usageMessage = null, array $aliases = [])
    {
        parent::__construct($name, $description, $usageMessage, $aliases);
        $this->owningPlugin = $plugin;
        $this->subCommands = [];
        $this->prepare();
    }

    abstract protected function prepare(): void;

    public function getOwningPlugin(): Plugin
    {
        return $this->owningPlugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$this->testPermission($sender)) {
            return;
        }
        if (count($args) > 0) {
            $subCommand = $this->findMatchingSubCommand($args[0]);
            if ($subCommand) {
                array_shift($args);
                $subCommand->execute($sender, $args);
                return;
            }
        }
        $this->onRun($sender, $args);
    }

    public function findMatchingSubCommand(string $search): ?SubCommand
    {
        foreach ($this->getSubCommands() as $subCommand) {
            if (strtolower($subCommand->getName()) === strtolower($search) || in_array(strtolower($search), $subCommand->getAliases())) {
                return $subCommand;
            }
        }
        return null;
    }

    /**
     * @return SubCommand[]
     */
    public function getSubCommands(): array
    {
        return $this->subCommands;
    }

    abstract protected function onRun(CommandSender $sender, array $args): void;

    public function addSubCommand(SubCommand $subCommand): bool
    {
        if ($this->hasSubCommand($subCommand->getName())) {
            return false;
        }
        $this->subCommands[$subCommand->getName()] = $subCommand;
        return true;
    }

    public function hasSubCommand(string $subCommand): bool
    {
        return isset($this->subCommands[$subCommand]);
    }
}
