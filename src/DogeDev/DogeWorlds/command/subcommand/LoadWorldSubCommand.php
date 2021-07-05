<?php

declare(strict_types=1);

namespace DogeDev\DogeWorlds\command\subcommand;

use DogeDev\DogeWorlds\command\WorldCommand;
use DogeDev\DogeWorlds\language\Language;
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
            $sender->sendMessage(TextFormat::RED . "Usage /dw load <world: name> [auto upgrade: false|true]");
            return;
        }

        $worldName = $args[0];
        $autoUpgrade = $args[1] ?? false;

        if ($this->getOwningPlugin()->getServer()->getWorldManager()->isWorldLoaded($worldName)) {
            $sender->sendMessage($this->getOwningPlugin()->getLanguage()->getMessage("worldAlreadyLoaded", ["{WORLD}" => $worldName], Language::MESSAGE_TYPE_ERROR));
            return;
        }

        if (!$this->getOwningPlugin()->getServer()->getWorldManager()->isWorldGenerated($worldName)) {
            $sender->sendMessage($this->getOwningPlugin()->getLanguage()->getMessage("worldNameInvalid", ["{WORLD}" => $worldName], Language::MESSAGE_TYPE_ERROR));
            return;
        }

        try {
            $succeeded = $this->getOwningPlugin()->getServer()->getWorldManager()->loadWorld($worldName, (bool)$autoUpgrade);
        } catch (UnsupportedWorldFormatException $exception) {
            $sender->sendMessage($this->getOwningPlugin()->getLanguage()->getMessage("worldFormatUnsupported", ["{WORLD}" => $worldName], Language::MESSAGE_TYPE_ERROR));
            return;
        }
        if (!$succeeded) {
            $sender->sendMessage($this->getOwningPlugin()->getLanguage()->getMessage("worldLoadingFailed", ["{WORLD}" => $worldName], Language::MESSAGE_TYPE_ERROR));
            return;
        }

        $sender->sendMessage($this->getOwningPlugin()->getLanguage()->getMessage("worldLoad", ["{WORLD}" => $worldName]));
    }
}
