<?php

declare(strict_types=1);

namespace DogeDev\DogeWorlds\command\subcommand;

use DogeDev\DogeWorlds\command\SubCommand;
use DogeDev\DogeWorlds\language\Messages;
use DogeDev\DogeWorlds\permission\Permissions;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;
use pocketmine\world\format\io\exception\UnsupportedWorldFormatException;

class LoadSubCommand extends SubCommand
{
    public function __construct(Plugin $owningPlugin)
    {
        parent::__construct($owningPlugin, "load");
    }

    public function prepare(): void
    {
        $this->setPermission(Permissions::DOGEWORLDS_SUBCOMMAND_LOAD);
        $this->setAliases($this->getOwningPlugin()->getLanguage()->getArray(Messages::ALIAS_SUBCOMMAND_LOAD, Messages::MESSAGE_CATEGORY_ALIAS));
        $this->setPermissionMessage($this->getOwningPlugin()->getLanguage()->getMessage(Messages::ERROR_NO_ENOUGH_PRIVILEGES,
            [],
            Messages::MESSAGE_CATEGORY_ERROR
        ));
    }


    protected function onRun(CommandSender $sender, array $args): void
    {
        if (count($args) < 1) {
            $sender->sendMessage($this->getOwningPlugin()->getLanguage()->getMessage(Messages::USAGE_SUBCOMMAND_LOAD,
                [],
                Messages::MESSAGE_CATEGORY_USAGE
            ));
            return;
        }

        $worldName = $args[0];
        $autoUpgrade = $args[1] ?? false;

        if ($this->getOwningPlugin()->getServer()->getWorldManager()->isWorldLoaded($worldName)) {
            $sender->sendMessage($this->getOwningPlugin()->getLanguage()->getMessage(Messages::ERROR_WORLD_ALREADY_LOADED,
                [
                    "{WORLD}" => $worldName
                ],
                Messages::MESSAGE_CATEGORY_ERROR
            ));
            return;
        }

        if (!$this->getOwningPlugin()->getServer()->getWorldManager()->isWorldGenerated($worldName)) {
            $sender->sendMessage($this->getOwningPlugin()->getLanguage()->getMessage(Messages::ERROR_WORLD_NOT_FOUND,
                [
                    "{WORLD}" => $worldName
                ],
                Messages::MESSAGE_CATEGORY_ERROR
            ));
            return;
        }

        try {
            $succeeded = $this->getOwningPlugin()->getServer()->getWorldManager()->loadWorld($worldName, (bool)$autoUpgrade);
        } catch (UnsupportedWorldFormatException $exception) {
            $sender->sendMessage($this->getOwningPlugin()->getLanguage()->getMessage(Messages::ERROR_WORLD_FORMAT_UNSUPPORTED,
                [
                    "{WORLD}" => $worldName
                ],
                Messages::MESSAGE_CATEGORY_ERROR
            ));
            return;
        }
        if (!$succeeded) {
            $sender->sendMessage($this->getOwningPlugin()->getLanguage()->getMessage(Messages::ERROR_WORLD_LOADING_FAILED,
                [
                    "{WORLD}" => $worldName
                ],
                Messages::MESSAGE_CATEGORY_ERROR
            ));
            return;
        }

        $sender->sendMessage($this->getOwningPlugin()->getLanguage()->getMessage(Messages::MESSAGE_WORLD_LOADED,
            [
                "{WORLD}" => $worldName
            ]
        ));
    }
}
