<?php

declare(strict_types=1);

namespace DogeDev\DogeWorlds\command\subcommand;

use DogeDev\DogeWorlds\command\SubCommand;
use DogeDev\DogeWorlds\language\Messages;
use DogeDev\DogeWorlds\permission\Permissions;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;

class ListSubCommand extends SubCommand
{
    public function __construct(Plugin $owningPlugin)
    {
        parent::__construct($owningPlugin, "list");
    }

    public function prepare(): void
    {
        $this->setPermission(Permissions::DOGEWORLDS_SUBCOMMAND_LIST);
        $this->setAliases($this->getOwningPlugin()->getLanguage()->getArray(Messages::ALIAS_SUBCOMMAND_LIST, Messages::MESSAGE_CATEGORY_ALIAS));
        $this->setPermissionMessage($this->getOwningPlugin()->getLanguage()->getMessage(Messages::ERROR_NO_ENOUGH_PRIVILEGES,
            [],
            Messages::MESSAGE_CATEGORY_ERROR
        ));
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

        $sender->sendMessage($this->getOwningPlugin()->getLanguage()->getMessage(Messages::MESSAGE_WORLD_LIST,
            []
        ));
        foreach ($worlds as $world) {
            $sender->sendMessage(($this->getOwningPlugin()->getServer()->getWorldManager()->isWorldLoaded($world) ? TextFormat::GREEN : TextFormat::RED) . $world);
        }
    }
}
