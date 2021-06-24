<?php

declare(strict_types=1);

namespace DogeDev\DogeWorlds\command\subcommand;

use DogeDev\DogeWorlds\command\WorldCommand;
use DogeDev\DogeWorlds\language\Language;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\world\format\io\exception\UnsupportedWorldFormatException;
use pocketmine\world\Position;

class TeleportWorldSubCommand extends WorldSubCommand
{
    public function __construct(WorldCommand $parentCommand)
    {
        parent::__construct($parentCommand, "teleport", "dogeworlds.command.teleport", ["tp"]);
    }

    protected function onRun(CommandSender $sender, array $args): void
    {
        if (!$sender instanceof Player && count($args) < 2) {
            $sender->sendMessage(TextFormat::RED . "Usage /dw teleport <world: name> <player: name>");
            return;
        }
        if (count($args) < 1) {
            $sender->sendMessage(TextFormat::RED . "Usage /dw teleport <world: name> [player: name]");
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
            $sender->sendMessage($this->getOwningPlugin()->getLanguage()->getMessage("worldNameInvalid", ["{WORLD}" => $worldName], Language::MESSAGE_TYPE_ERROR));
        }

        if (!$this->getOwningPlugin()->getServer()->getWorldManager()->isWorldLoaded($worldName)) {
            try {
                $this->getOwningPlugin()->getServer()->getWorldManager()->loadWorld($worldName);
            } catch (UnsupportedWorldFormatException $exception) {
                $sender->sendMessage($this->getOwningPlugin()->getLanguage()->getMessage("worldFormatUnsupported", ["{WORLD}" => $worldName], Language::MESSAGE_TYPE_ERROR));
                return;
            }
        }

        $world = $this->getOwningPlugin()->getServer()->getWorldManager()->getWorldByName($worldName);

        $target = $sender instanceof Player ? $sender : null;

        if (isset($args[1])) {
            $target = $args[1];
            if (!$this->getOwningPlugin()->getServer()->getPlayerByPrefix($target)) {
                $sender->sendMessage($this->getOwningPlugin()->getLanguage()->getMessage("playerOffline", ["{PLAYER}" => $target], Language::MESSAGE_TYPE_ERROR));
                return;
            }
            $target = $this->getOwningPlugin()->getServer()->getPlayerByPrefix($target);
        }

        $time = microtime(true);

        $spawnLocation = $world->getSpawnLocation();
        $world->requestChunkPopulation($spawnLocation->getFloorX() >> 4, $spawnLocation->getFloorZ() >> 4, null)->onCompletion(
            function () use ($sender, $worldName, $target, $time, $spawnLocation): void {
                if ($target && !$target->isConnected()) {
                    return;
                }
                $sender->sendMessage($this->getOwningPlugin()->getLanguage()->getMessage("worldTeleport", ["{PLAYER}" => $target->getName(), "{WORLD}" => $worldName, "{TIME}" => $time]));
                $target->teleport(Position::fromObject($spawnLocation->add(0.5, 0, 0.5), $spawnLocation->getWorld()));
            },
            static function () use ($sender, $worldName, $target): void {
                $sender->sendMessage($this->getOwningPlugin()->getLanguage()->getMessage("worldTeleportFail", ["{PLAYER}" => $target, "{WORLD}" => $worldName], Language::MESSAGE_TYPE_ERROR));
            }
        );
    }
}
