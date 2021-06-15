<?php

declare(strict_types=1);

namespace DogeDev\DogeWorlds\command\subcommand;

use DogeDev\DogeWorlds\command\WorldCommand;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class LoadWorldSubCommand extends WorldSubCommand
{
    public function __construct(WorldCommand $parentCommand)
    {
        parent::__construct($parentCommand, "load", "dogeworlds.command.load", []);
    }

    protected function onRun(CommandSender $sender, array $args): void
    {
        if (count($args) < 1) {
            $sender->sendMessage(TextFormat::RED . "Usage /dw load <world : name> [auto upgrade : false|true]");
            return;
        }
        $name = $args[0];
        $autoUpgrade = $args[1] ?? false;
        if ($this->getPlugin()->getServer()->getWorldManager()->isWorldLoaded($name)) {
            $sender->sendMessage(TextFormat::WHITE . $name . TextFormat::RED . " world is already loaded.");
            return;
        }
        $succeeded = $this->getPlugin()->getServer()->getWorldManager()->loadWorld($name, $autoUpgrade);
        if (!$succeeded) {
            $sender->sendMessage(TextFormat::WHITE . $name . TextFormat::RED . " world failed to load.");
            return;
        }
        $sender->sendMessage(TextFormat::WHITE . $name . TextFormat::GREEN . " world was successfully loaded.");
    }
}
