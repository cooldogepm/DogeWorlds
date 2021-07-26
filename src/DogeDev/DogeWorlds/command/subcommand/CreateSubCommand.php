<?php

declare(strict_types=1);

namespace DogeDev\DogeWorlds\command\subcommand;

use DogeDev\DogeWorlds\command\SubCommand;
use DogeDev\DogeWorlds\language\Messages;
use DogeDev\DogeWorlds\permission\Permissions;
use DogeDev\DogeWorlds\utils\DifficultyUtils;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;
use pocketmine\world\generator\GeneratorManager;
use pocketmine\world\generator\normal\Normal;
use pocketmine\world\World;
use pocketmine\world\WorldCreationOptions;

class CreateSubCommand extends SubCommand
{
    public function __construct(Plugin $owningPlugin)
    {
        parent::__construct($owningPlugin, "create");
    }

    public function prepare(): void
    {
        $this->setPermission(Permissions::DOGEWORLDS_SUBCOMMAND_CREATE);
        $this->setAliases($this->getOwningPlugin()->getLanguage()->getArray(Messages::ALIAS_SUBCOMMAND_CREATE, Messages::MESSAGE_CATEGORY_ALIAS));
        $this->setPermissionMessage($this->getOwningPlugin()->getLanguage()->getMessage(Messages::ERROR_NO_ENOUGH_PRIVILEGES,
            [],
            Messages::MESSAGE_CATEGORY_ERROR
        ));
    }

    protected function onRun(CommandSender $sender, array $args): void
    {
        $generators = GeneratorManager::getInstance()->getGeneratorList();
        if (count($args) < 2) {
            $sender->sendMessage($this->getOwningPlugin()->getLanguage()->getMessage(Messages::USAGE_SUBCOMMAND_CREATE,
                [
                    "{GENERATORS}" => implode("|", $generators)
                ],
                Messages::MESSAGE_CATEGORY_USAGE
            ));
            return;
        }

        $name = $args[0];
        $generatorName = $args[1];
        $difficulty = isset($args[2]) ? World::getDifficultyFromString($args[2]) : 2;

        $generator = GeneratorManager::getInstance()->getGenerator($generatorName);

        if ($this->getOwningPlugin()->getServer()->getWorldManager()->isWorldGenerated($name)) {
            $sender->sendMessage($this->getOwningPlugin()->getLanguage()->getMessage(Messages::ERROR_WORLD_NOT_FOUND,
                [
                    "{WORLD}" => $name
                ],
                Messages::MESSAGE_CATEGORY_ERROR
            ));
            return;
        }

        if ($generator === Normal::class && strtolower($generatorName) !== "normal") {
            $sender->sendMessage($this->getOwningPlugin()->getLanguage()->getMessage(Messages::ERROR_WORLD_GENERATOR_INVALID,
                [
                    "{GENERATOR}" => $generatorName
                ],
                Messages::MESSAGE_CATEGORY_ERROR
            ));
            return;
        }

        $options = new WorldCreationOptions();
        $options->setGeneratorClass($generator);
        $options->setDifficulty($difficulty === -1 ? 2 : $difficulty);
        $this->getOwningPlugin()->getServer()->getWorldManager()->generateWorld($name, $options);
        $sender->sendMessage($this->getOwningPlugin()->getLanguage()->getMessage(Messages::MESSAGE_WORLD_CREATED,
            [
                "{WORLD}" => $name,
                "{GENERATOR}" => ucwords($generatorName),
                "{DIFFICULTY}" => DifficultyUtils::getDifficultyNameFromInteger($difficulty),
            ]
        ));
    }
}
