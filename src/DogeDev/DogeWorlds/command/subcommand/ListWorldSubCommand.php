<?php

declare(strict_types=1);

namespace DogeDev\DogeWorlds\command\subcommand;

use DogeDev\DogeWorlds\command\WorldCommand;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class ListWorldSubCommand extends WorldSubCommand
{
    public function __construct(WorldCommand $parentCommand)
    {
        parent::__construct($parentCommand, "list", "dogeworlds.command.list", []);
    }

    protected function onRun(CommandSender $sender, array $args): void
    {
        $worlds = [];
        foreach (scandir($this->getOwningPlugin()->getServer()->getDataPath() . "worlds") as $world) {
            if ($world === "." || $world === ".." || pathinfo($world, PATHINFO_EXTENSION) !== "") {
                continue;
            }
            $worlds[] = $world;
        }

        $sender->sendMessage($this->getOwningPlugin()->getLanguage()->getMessage("worldList"));
        foreach ($worlds as $world) {
            $sender->sendMessage(($this->getOwningPlugin()->getServer()->getWorldManager()->isWorldLoaded($world) ? TextFormat::GREEN : TextFormat::RED) . $world);
        }
    }
}
