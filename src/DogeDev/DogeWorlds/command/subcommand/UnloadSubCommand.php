<?php

declare(strict_types=1);

namespace DogeDev\DogeWorlds\command\subcommand;

use DogeDev\DogeWorlds\command\SubCommand;
use DogeDev\DogeWorlds\language\Messages;
use DogeDev\DogeWorlds\permission\Permissions;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;

class UnloadSubCommand extends SubCommand
{
    public function __construct(Plugin $owningPlugin)
    {
        parent::__construct($owningPlugin, "unload");
    }

    public function prepare(): void
    {
        $this->setPermission(Permissions::DOGEWORLDS_SUBCOMMAND_UNLOAD);
        $this->setAliases($this->getOwningPlugin()->getLanguage()->getArray(Messages::ALIAS_SUBCOMMAND_UNLOAD, Messages::MESSAGE_CATEGORY_ALIAS));
        $this->setPermissionMessage($this->getOwningPlugin()->getLanguage()->getMessage(Messages::ERROR_NO_ENOUGH_PRIVILEGES,
            [],
            Messages::MESSAGE_CATEGORY_ERROR
        ));
    }


    protected function onRun(CommandSender $sender, array $args): void
    {
        if (count($args) < 1) {
            $sender->sendMessage($this->getOwningPlugin()->getLanguage()->getMessage(Messages::USAGE_SUBCOMMAND_UNLOAD,
                [],
                Messages::MESSAGE_CATEGORY_USAGE
            ));
            return;
        }

        $worldName = $args[0];
        $force = $args[1] ?? false;

        $world = $this->getOwningPlugin()->getServer()->getWorldManager()->getWorldByName($worldName);
        if (!$world || !$this->getOwningPlugin()->getServer()->getWorldManager()->unloadWorld($world, (bool)$force)) {
            $sender->sendMessage($this->getOwningPlugin()->getLanguage()->getMessage(Messages::ERROR_WORLD_IS_NOT_LOADED,
                [
                    "{WORLD}" => $worldName
                ],
                Messages::MESSAGE_CATEGORY_ERROR
            ));
            return;
        }
        $sender->sendMessage($this->getOwningPlugin()->getLanguage()->getMessage(Messages::MESSAGE_WORLD_UNLOADED,
            [
                "{WORLD}" => $worldName
            ]
        ));
    }
}
