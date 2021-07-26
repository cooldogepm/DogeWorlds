<?php

declare(strict_types=1);

namespace DogeDev\DogeWorlds\command\subcommand;

use DogeDev\DogeWorlds\command\SubCommand;
use DogeDev\DogeWorlds\language\Messages;
use DogeDev\DogeWorlds\permission\Permissions;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\world\format\io\exception\UnsupportedWorldFormatException;
use pocketmine\world\Position;

class TeleportSubCommand extends SubCommand
{
    public function __construct(Plugin $owningPlugin)
    {
        parent::__construct($owningPlugin, "teleport");
    }

    public function prepare(): void
    {
        $this->setPermission(Permissions::DOGEWORLDS_SUBCOMMAND_TELEPORT);
        $this->setAliases($this->getOwningPlugin()->getLanguage()->getArray(Messages::ALIAS_SUBCOMMAND_TELEPORT, Messages::MESSAGE_CATEGORY_ALIAS));
        $this->setPermissionMessage($this->getOwningPlugin()->getLanguage()->getMessage(Messages::ERROR_NO_ENOUGH_PRIVILEGES,
            [],
            Messages::MESSAGE_CATEGORY_ERROR
        ));
    }

    protected function onRun(CommandSender $sender, array $args): void
    {
        if (!$sender instanceof Player && count($args) < 2) {
            $sender->sendMessage($this->getOwningPlugin()->getLanguage()->getMessage(Messages::USAGE_SUBCOMMAND_TELEPORT_CONSOLE,
                [],
                Messages::MESSAGE_CATEGORY_USAGE
            ));
            return;
        }
        if (count($args) < 1) {
            $sender->sendMessage($this->getOwningPlugin()->getLanguage()->getMessage(Messages::USAGE_SUBCOMMAND_TELEPORT,
                [],
                Messages::MESSAGE_CATEGORY_USAGE
            ));
            return;
        }

        $worldName = $args[0];

        if (!$this->getOwningPlugin()->getServer()->getWorldManager()->isWorldGenerated($worldName)) {
            $sender->sendMessage($this->getOwningPlugin()->getLanguage()->getMessage(Messages::ERROR_WORLD_NOT_FOUND,
                [
                    "{WORLD}" => $worldName
                ],
                Messages::MESSAGE_CATEGORY_ERROR
            ));
            return;
        }

        if (!$this->getOwningPlugin()->getServer()->getWorldManager()->isWorldLoaded($worldName)) {
            try {
                $this->getOwningPlugin()->getServer()->getWorldManager()->loadWorld($worldName);
            } catch (UnsupportedWorldFormatException $exception) {
                $sender->sendMessage($this->getOwningPlugin()->getLanguage()->getMessage(Messages::ERROR_WORLD_FORMAT_UNSUPPORTED,
                    [
                        "{WORLD}" => $worldName
                    ],
                    Messages::MESSAGE_CATEGORY_ERROR
                ));
                return;
            }
        }

        $world = $this->getOwningPlugin()->getServer()->getWorldManager()->getWorldByName($worldName);

        $target = $sender instanceof Player ? $sender : null;

        if (isset($args[1])) {
            $target = $args[1];
            if (!$this->getOwningPlugin()->getServer()->getPlayerByPrefix($target)) {
                $sender->sendMessage($this->getOwningPlugin()->getLanguage()->getMessage(Messages::ERROR_PLAYER_IS_OFFLINE,
                    [
                        "{PLAYER}" => $target
                    ],
                    Messages::MESSAGE_CATEGORY_ERROR
                ));
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
                $sender->sendMessage($this->getOwningPlugin()->getLanguage()->getMessage(Messages::MESSAGE_WORLD_TELEPORTED,
                    [
                        "{PLAYER}" => $target->getName(),
                        "{WORLD}" => $worldName,
                        "{TIME}" => $time,
                    ]
                ));

                $target->teleport(Position::fromObject($spawnLocation->add(0.5, 0, 0.5), $spawnLocation->getWorld()));
            },
            function () use ($sender, $worldName, $target): void {
                $sender->sendMessage($this->getOwningPlugin()->getLanguage()->getMessage(Messages::ERROR_WORLD_TELEPORT_FAIL,
                    [
                        "{PLAYER}" => $target->getName(),
                        "{WORLD}" => $worldName,
                    ],
                    Messages::MESSAGE_CATEGORY_ERROR
                ));
            }
        );
    }
}
