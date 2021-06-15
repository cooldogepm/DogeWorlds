<?php

declare(strict_types=1);

namespace DogeDev\DogeWorlds\command\subcommand;

use DogeDev\DogeWorlds\command\WorldCommand;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class UnloadWorldSubCommand extends WorldSubCommand
{
    public function __construct(WorldCommand $parentCommand)
    {
        parent::__construct($parentCommand, "unload", "dogeworlds.command.unload", []);
    }

    protected function onRun(CommandSender $sender, array $args): void
    {
        if (count($args) < 1) {
            $sender->sendMessage(TextFormat::RED . "Usage /dw unload <world : name> <force : false|true>");
            return;
        }

        $name = $args[0];
        $force = $args[1] ?? false;

        $world = $this->getPlugin()->getServer()->getWorldManager()->getWorldByName($name);
        if (!$world) {
            $sender->sendMessage(TextFormat::WHITE . $name . TextFormat::RED . " world is not loaded.");
            return;
        }

        $sender->sendMessage(TextFormat::WHITE . $world->getFolderName() . TextFormat::RED . " world was successfully unloaded.");
        $this->getPlugin()->getServer()->getWorldManager()->unloadWorld($world, (bool)$force);
    }
}
