<?php

declare(strict_types=1);

namespace DogeDev\DogeWorlds\command;

use DogeDev\DogeWorlds\command\subcommand\CreateSubCommand;
use DogeDev\DogeWorlds\command\subcommand\DeleteSubCommand;
use DogeDev\DogeWorlds\command\subcommand\ListSubCommand;
use DogeDev\DogeWorlds\command\subcommand\LoadSubCommand;
use DogeDev\DogeWorlds\command\subcommand\RenameSubCommand;
use DogeDev\DogeWorlds\command\subcommand\TeleportSubCommand;
use DogeDev\DogeWorlds\command\subcommand\UnloadSubCommand;
use DogeDev\DogeWorlds\DogeWorlds;
use DogeDev\DogeWorlds\language\Messages;
use pocketmine\command\CommandSender;

final class DogeWorldsCommand extends BaseCommand
{
    public function __construct(DogeWorlds $plugin)
    {
        parent::__construct($plugin, "dogeworlds");
    }

    public function onRun(CommandSender $sender, array $args): void
    {
        $sender->sendMessage($this->getUsage());
    }

    protected function prepare(): void
    {
        $this->addSubCommand(new CreateSubCommand($this->getOwningPlugin()));
        $this->addSubCommand(new DeleteSubCommand($this->getOwningPlugin()));
        $this->addSubCommand(new ListSubCommand($this->getOwningPlugin()));
        $this->addSubCommand(new LoadSubCommand($this->getOwningPlugin()));
        $this->addSubCommand(new RenameSubCommand($this->getOwningPlugin()));
        $this->addSubCommand(new TeleportSubCommand($this->getOwningPlugin()));
        $this->addSubCommand(new UnloadSubCommand($this->getOwningPlugin()));

        $this->setUsage($this->getOwningPlugin()->getLanguage()->getMessage(Messages::USAGE_COMMAND_DEFAULT,
            [],
            Messages::MESSAGE_CATEGORY_USAGE
        ));
        $this->setAliases($this->getOwningPlugin()->getLanguage()->getArray(Messages::ALIAS_COMMAND_DEFAULT, Messages::MESSAGE_CATEGORY_ALIAS));
        $this->setPermissionMessage($this->getOwningPlugin()->getLanguage()->getMessage(Messages::ERROR_NO_ENOUGH_PRIVILEGES,
            [],
            Messages::MESSAGE_CATEGORY_ERROR
        ));
    }

    public function getOwningPlugin(): DogeWorlds
    {
        return $this->owningPlugin;
    }
}
