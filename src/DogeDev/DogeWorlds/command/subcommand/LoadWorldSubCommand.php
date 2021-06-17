<?php

declare(strict_types=1);

namespace DogeDev\DogeWorlds\command\subcommand;

use DogeDev\DogeWorlds\command\WorldCommand;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\world\format\io\exception\UnsupportedWorldFormatException;

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

        $worldName = $args[0];
        $autoUpgrade = $args[1] ?? false;

        if ($this->getPlugin()->getServer()->getWorldManager()->isWorldLoaded($worldName)) {
            $sender->sendMessage(TextFormat::WHITE . $worldName . TextFormat::RED . " world is already loaded.");
            return;
        }

        $sender->sendMessage(TextFormat::WHITE . $worldName . TextFormat::GREEN . " world is not loaded, loading the world...");
        try {
            $succeeded = $this->getPlugin()->getServer()->getWorldManager()->loadWorld($worldName, (bool)$autoUpgrade);
        } catch (UnsupportedWorldFormatException $exception) {
            $sender->sendMessage(TextFormat::RED . "That world is not supported, use " . TextFormat::WHITE . $worldName . "/dw load " . $worldName . " true" . TextFormat::RED . " to convert the world upon loading.");
            return;
        }
        if (!$succeeded) {
            $sender->sendMessage(TextFormat::WHITE . $worldName . TextFormat::RED . " world failed to load.");
            return;
        }

        $sender->sendMessage(TextFormat::WHITE . $worldName . TextFormat::GREEN . " world was successfully loaded.");
    }
}
