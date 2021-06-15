<?php

declare(strict_types=1);

namespace DogeDev\DogeWorlds\command\subcommand;

use DogeDev\DogeWorlds\command\WorldCommand;
use DogeDev\DogeWorlds\utils\DifficultyUtils;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\world\generator\GeneratorManager;
use pocketmine\world\generator\normal\Normal;
use pocketmine\world\World;
use pocketmine\world\WorldCreationOptions;

class CreateWorldSubCommand extends WorldSubCommand
{
    public function __construct(WorldCommand $parentCommand)
    {
        parent::__construct($parentCommand, "create", "dogeworlds.command.create", ["c"]);
    }

    protected function onRun(CommandSender $sender, array $args): void
    {
        $generators = GeneratorManager::getInstance()->getGeneratorList();
        if (count($args) < 2) {
            $sender->sendMessage(TextFormat::RED . "Usage /dw create <world : name> <generator : " . implode("|", $generators) . "> [difficulty : easy|hard|normal|peaceful]");
            return;
        }

        $name = $args[0];
        $generatorName = $args[1];
        $difficulty = isset($args[2]) ? World::getDifficultyFromString($args[2]) : 2;

        $generator = GeneratorManager::getInstance()->getGenerator($generatorName);

        $worlds = [];
        foreach (scandir($this->getPlugin()->getServer()->getDataPath() . "worlds") as $world) {
            if ($world === "." || $world === ".." || isset(pathinfo($world, PATHINFO_EXTENSION)["extension"])) {
                continue;
            }
            $worlds[] = $world;
        }

        if (in_array($name, $worlds)) {
            $sender->sendMessage(TextFormat::WHITE . $name . TextFormat::RED . " world name is already taken.");
            return;
        }

        if ($generator === Normal::class && strtolower($generatorName) !== "normal") {
            $sender->sendMessage(TextFormat::WHITE . $generatorName . TextFormat::RED . " is not a registered world generator.");
            return;
        }

        $options = new WorldCreationOptions();
        $options->setGeneratorClass($generator);
        $options->setDifficulty($difficulty === -1 ? 2 : $difficulty);
        $this->getPlugin()->getServer()->getWorldManager()->generateWorld($name, $options);
        $sender->sendMessage(TextFormat::WHITE . $name . TextFormat::GREEN . " world was created with the " . TextFormat::WHITE . ucwords($generatorName) . TextFormat::GREEN . " generator, " . TextFormat::WHITE . DifficultyUtils::getDifficultyNameFromInteger($difficulty) . TextFormat::GREEN . " difficulty.");
    }
}
