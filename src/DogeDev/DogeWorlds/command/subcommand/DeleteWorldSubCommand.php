<?php

declare(strict_types=1);

namespace DogeDev\DogeWorlds\command\subcommand;

use DogeDev\DogeWorlds\asynchronous\task\RecursiveDeletionAsyncTask;
use DogeDev\DogeWorlds\command\WorldCommand;
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
            $sender->sendMessage(TextFormat::RED . "Usage /dw delete <world : name>");
            return;
        }

        $worldName = $args[0];

        $worlds = [];
        foreach (scandir($this->getPlugin()->getServer()->getDataPath() . "worlds") as $world) {
            if ($world === "." || $world === ".." || pathinfo($world, PATHINFO_EXTENSION) !== "") {
                continue;
            }
            $worlds[] = $world;
        }

        if (!in_array($worldName, $worlds)) {
            $sender->sendMessage(TextFormat::WHITE . $worldName . TextFormat::RED . " world was not found.");
            return;
        }

        if ($worldName === $this->getPlugin()->getServer()->getWorldManager()->getDefaultWorld()->getFolderName()) {
            $sender->sendMessage(TextFormat::WHITE . $worldName . TextFormat::RED . " world failed to delete, default world cannot be deleted.");
            return;
        }

        $world = $this->getPlugin()->getServer()->getWorldManager()->getWorldByName($worldName);
        if ($world) {
            $this->getPlugin()->getServer()->getWorldManager()->unloadWorld($world);
        }

        $this->getPlugin()->getAsyncPool()->queueAsyncCallback(new RecursiveDeletionAsyncTask([$this->getPlugin()->getServer()->getDataPath() . DIRECTORY_SEPARATOR . "worlds" . DIRECTORY_SEPARATOR . $worldName]), function (RecursiveDeletionAsyncTask $_task) use ($sender, $worldName): void {
            $sender->sendMessage(TextFormat::WHITE . $worldName . TextFormat::RED . " world was successfully deleted.");
        });
    }
}
