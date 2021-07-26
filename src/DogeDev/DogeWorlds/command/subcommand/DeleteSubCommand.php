<?php

declare(strict_types=1);

namespace DogeDev\DogeWorlds\command\subcommand;

use DogeDev\DogeWorlds\asynchronous\task\RecursiveDeletionAsyncTask;
use DogeDev\DogeWorlds\command\SubCommand;
use DogeDev\DogeWorlds\language\Messages;
use DogeDev\DogeWorlds\permission\Permissions;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;

class DeleteSubCommand extends SubCommand
{
    public function __construct(Plugin $owningPlugin)
    {
        parent::__construct($owningPlugin, "delete");
    }

    public function prepare(): void
    {
        $this->setPermission(Permissions::DOGEWORLDS_SUBCOMMAND_DELETE);
        $this->setAliases($this->getOwningPlugin()->getLanguage()->getArray(Messages::ALIAS_SUBCOMMAND_DELETE, Messages::MESSAGE_CATEGORY_ALIAS));
        $this->setPermissionMessage($this->getOwningPlugin()->getLanguage()->getMessage(Messages::ERROR_NO_ENOUGH_PRIVILEGES,
            [],
            Messages::MESSAGE_CATEGORY_ERROR
        ));
    }


    protected function onRun(CommandSender $sender, array $args): void
    {
        if (count($args) < 1) {
            $sender->sendMessage($this->getOwningPlugin()->getLanguage()->getMessage(Messages::USAGE_SUBCOMMAND_DELETE,
                [],
                Messages::MESSAGE_CATEGORY_USAGE
            ));
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
            $sender->sendMessage($this->getOwningPlugin()->getLanguage()->getMessage(Messages::ERROR_WORLD_NOT_FOUND,
                [
                    "{WORLD}" => $worldName
                ],
                Messages::MESSAGE_CATEGORY_ERROR
            ));
            return;
        }

        if ($worldName === $this->getOwningPlugin()->getServer()->getWorldManager()->getDefaultWorld()->getFolderName()) {
            $sender->sendMessage($this->getOwningPlugin()->getLanguage()->getMessage(Messages::ERROR_WORLD_CANNOT_DELETE_DEFAULT,
                [
                    "{WORLD}" => $worldName
                ],
                Messages::MESSAGE_CATEGORY_ERROR
            ));
            return;
        }

        $world = $this->getOwningPlugin()->getServer()->getWorldManager()->getWorldByName($worldName);
        if ($world) {
            $this->getOwningPlugin()->getServer()->getWorldManager()->unloadWorld($world);
        }

        $this->getOwningPlugin()->getAsyncPool()->queueAsyncCallback(new RecursiveDeletionAsyncTask([$this->getOwningPlugin()->getServer()->getDataPath() . DIRECTORY_SEPARATOR . "worlds" . DIRECTORY_SEPARATOR . $worldName]), function (RecursiveDeletionAsyncTask $_) use ($sender, $worldName): void {
            $sender->sendMessage($this->getOwningPlugin()->getLanguage()->getMessage(Messages::MESSAGE_WORLD_DELETED,
                [
                    "{WORLD}" => $worldName
                ]
            ));
        });
    }
}
