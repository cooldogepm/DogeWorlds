<?php

declare(strict_types=1);

namespace DogeDev\DogeWorlds\command\subcommand;

use DogeDev\DogeWorlds\command\WorldCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
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
            $sender->sendMessage(TextFormat::RED . "Usage /dw teleport <world : name> <player : name>");
            return;
        }
        if (count($args) < 1) {
            $sender->sendMessage(TextFormat::RED . "Usage /dw teleport <world : name> [player : name]");
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
            $sender->sendMessage(TextFormat::WHITE . $worldName . TextFormat::RED . " world is not found.");
        }

        if (!$this->getPlugin()->getServer()->getWorldManager()->isWorldLoaded($worldName)) {
            $sender->sendMessage(TextFormat::WHITE . $worldName . TextFormat::GREEN . " world is not loaded, loading the world...");
            $this->getPlugin()->getServer()->getWorldManager()->loadWorld($worldName);
        }

        $world = $this->getPlugin()->getServer()->getWorldManager()->getWorldByName($worldName);

        $target = $sender instanceof Player ? $sender : null;

        if (isset($args[1])) {
            $target = $args[1];
            if (!$this->getPlugin()->getServer()->getPlayerByPrefix($target)) {
                $sender->sendMessage(TextFormat::WHITE . $target . TextFormat::RED . " player is not online.");
                return;
            }
            $target = $this->getPlugin()->getServer()->getPlayerByPrefix($target);
        }

        $spawnLocation = $world->getSpawnLocation();
        $world->requestChunkPopulation($spawnLocation->getFloorX() >> 4, $spawnLocation->getFloorZ() >> 4, null)->onCompletion(
            function () use ($sender, $worldName, $target, $spawnLocation): void {
                if ($target && !$target->isConnected()) {
                    return;
                }
                $sender->sendMessage(TextFormat::WHITE . $target->getName() . TextFormat::GREEN . " player was successfully teleported to the " . TextFormat::WHITE . $worldName . TextFormat::GREEN . " world.");
                $target->teleport(Position::fromObject($spawnLocation->add(0.5, 0, 0.5), $spawnLocation->getWorld()));
            },
            static function () use ($sender, $worldName, $target): void {
                $sender->sendMessage(TextFormat::WHITE . $target->getName() . TextFormat::RED . " player failed to teleport to the " . TextFormat::WHITE . $worldName . " world.");
            }
        );
    }
}
