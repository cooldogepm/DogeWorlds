<?php

declare(strict_types=1);

namespace DogeDev\DogeWorlds\command\subcommand;

use DogeDev\DogeWorlds\asynchronous\task\RecursiveDeletionAsyncTask;
use DogeDev\DogeWorlds\command\WorldCommand;
use DogeDev\DogeWorlds\language\Language;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class DeleteWorldSubCommand extends WorldSubCommand
{
    public function __construct(WorldCommand $parentCommand)
    {
        parent::__construct($parentCommand, "delete", "dogeworlds.command.delete", ["del"]);
    }

    protected function onRun(CommandSender $sender, array $args): void
    {
        if (count($args) < 1) {
            $sender->sendMessage(TextFormat::RED . "Usage /dw delete <world: name>");
            return;
        }

        $worldName = $args[0];

        $worlds = [];
        foreach (scandir($this->getOwningPlugin()->getServer()->getDataPath() . "worlds") as $world) {
            if ($world === "." || $world === ".." || pathinfo($world, PATHINFO_EXTENSION) !== "") {
                continue;
            }
            $worlds[] = $world;
        }

        if (!in_array($worldName, $worlds)) {
            $sender->sendMessage($this->getOwningPlugin()->getLanguage()->getMessage("worldNameInvalid", ["{WORLD}" => $worldName]));
            return;
        }

        if ($worldName === $this->getOwningPlugin()->getServer()->getWorldManager()->getDefaultWorld()->getFolderName()) {
            $sender->sendMessage($this->getOwningPlugin()->getLanguage()->getMessage("worldDefaultDeletion", ["{WORLD}" => $worldName], Language::MESSAGE_TYPE_ERROR));
            return;
        }

        $world = $this->getOwningPlugin()->getServer()->getWorldManager()->getWorldByName($worldName);
        if ($world) {
            $this->getOwningPlugin()->getServer()->getWorldManager()->unloadWorld($world);
        }

        $this->getOwningPlugin()->getAsyncPool()->queueAsyncCallback(new RecursiveDeletionAsyncTask([$this->getOwningPlugin()->getServer()->getDataPath() . DIRECTORY_SEPARATOR . "worlds" . DIRECTORY_SEPARATOR . $worldName]), function (RecursiveDeletionAsyncTask $_) use ($sender, $worldName): void {
            $sender->sendMessage($this->getOwningPlugin()->getLanguage()->getMessage("worldDeletion", ["{WORLD}" => $worldName]));
        });
    }
}
