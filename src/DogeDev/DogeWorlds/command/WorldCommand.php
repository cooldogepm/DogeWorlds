<?php

declare(strict_types=1);

namespace DogeDev\DogeWorlds\command;

use DogeDev\DogeWorlds\command\subcommand\CreateWorldSubCommand;
use DogeDev\DogeWorlds\command\subcommand\DeleteWorldSubCommand;
use DogeDev\DogeWorlds\command\subcommand\ListWorldSubCommand;
use DogeDev\DogeWorlds\command\subcommand\LoadWorldSubCommand;
use DogeDev\DogeWorlds\command\subcommand\TeleportWorldSubCommand;
use DogeDev\DogeWorlds\command\subcommand\UnloadWorldSubCommand;
use DogeDev\DogeWorlds\command\subcommand\WorldSubCommand;
use DogeDev\DogeWorlds\DogeWorlds;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class WorldCommand extends Command
{
    protected DogeWorlds $plugin;
    /**
     * @var WorldSubCommand[]
     */
    protected array $subCommands;

    public function __construct(DogeWorlds $plugin)
    {
        parent::__construct("dogeworlds", "DogeWorlds", "Usage /dw <create|delete|list|load|unload>", ["dw"]);
        $this->plugin = $plugin;
        $this->subCommands = [];
        $this->prepare();
    }

    protected function prepare(): void
    {
        $this->subCommands[] = new CreateWorldSubCommand($this);
        $this->subCommands[] = new DeleteWorldSubCommand($this);
        $this->subCommands[] = new ListWorldSubCommand($this);
        $this->subCommands[] = new LoadWorldSubCommand($this);
        $this->subCommands[] = new TeleportWorldSubCommand($this);
        $this->subCommands[] = new UnloadWorldSubCommand($this);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$this->testPermission($sender)) {
            $sender->sendMessage(TextFormat::RED . "You don't have enough permissions to perform this command.");
            return;
        }
        if (count($args) < 1) {
            $sender->sendMessage(TextFormat::RED . $this->getUsage());
            return;
        }
        foreach ($this->getSubCommands() as $subCommand) {
            if (strtolower($subCommand->getName()) === strtolower($args[0]) || in_array(strtolower($args[0]), $subCommand->getAliases())) {
                array_shift($args);
                $subCommand->execute($sender, $args);
                return;
            }
        }
        $sender->sendMessage(TextFormat::RED . $this->getUsage());
    }

    /**
     * @return WorldSubCommand[]
     */
    public function getSubCommands(): array
    {
        return $this->subCommands;
    }

    public function getPlugin(): DogeWorlds
    {
        return $this->plugin;
    }
}
