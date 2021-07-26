<?php

declare(strict_types=1);

namespace DogeDev\DogeWorlds\command\subcommand;

use DogeDev\DogeWorlds\command\SubCommand;
use DogeDev\DogeWorlds\language\Messages;
use DogeDev\DogeWorlds\permission\Permissions;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;
use pocketmine\world\format\io\data\BaseNbtWorldData;
use pocketmine\world\format\io\exception\UnsupportedWorldFormatException;

class RenameSubCommand extends SubCommand
{
    public function __construct(Plugin $owningPlugin)
    {
        parent::__construct($owningPlugin, "rename");
    }

    public function prepare(): void
    {
        $this->setPermission(Permissions::DOGEWORLDS_SUBCOMMAND_RENAME);
        $this->setAliases($this->getOwningPlugin()->getLanguage()->getArray(Messages::ALIAS_SUBCOMMAND_RENAME, Messages::MESSAGE_CATEGORY_ALIAS));
        $this->setPermissionMessage($this->getOwningPlugin()->getLanguage()->getMessage(Messages::ERROR_NO_ENOUGH_PRIVILEGES,
            [],
            Messages::MESSAGE_CATEGORY_ERROR
        ));
    }

    protected function onRun(CommandSender $sender, array $args): void
    {
        if (count($args) < 2) {
            $sender->sendMessage($this->getOwningPlugin()->getLanguage()->getMessage(Messages::USAGE_SUBCOMMAND_RENAME,
                [],
                Messages::MESSAGE_CATEGORY_USAGE
            ));
            return;
        }

        $worldName = $args[0];
        $newName = $args[1];

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

        $data = $world->getProvider()->getWorldData();
        if ($data instanceof BaseNbtWorldData) {
            $data->getCompoundTag()->setString("LevelName", $newName);
            $data->save();
        }

        $this->getOwningPlugin()->getServer()->getWorldManager()->unloadWorld($world);

        $oldPath = $this->getOwningPlugin()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . $worldName;
        $newPath = $this->getOwningPlugin()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . $newName;

        $succeeded = rename($oldPath, $newPath);

        if (!$succeeded) {
            $sender->sendMessage($this->getOwningPlugin()->getLanguage()->getMessage(Messages::ERROR_WORLD_RENAME_FAIL,
                [
                    "{WORLD}" => $worldName,
                    "{NEW_NAME}" => $newName,
                ],
                Messages::MESSAGE_CATEGORY_ERROR
            ));
            return;
        }

        $sender->sendMessage($this->getOwningPlugin()->getLanguage()->getMessage(Messages::MESSAGE_WORLD_RENAMED,
            [
                "{WORLD}" => $worldName,
                "{NEW_NAME}" => $newName,
            ],
        ));
    }
}
