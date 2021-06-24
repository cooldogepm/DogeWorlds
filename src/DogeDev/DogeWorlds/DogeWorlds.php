<?php

declare(strict_types=1);

namespace DogeDev\DogeWorlds;

use DogeDev\DogeWorlds\asynchronous\DogeWorldsAsyncPool;
use DogeDev\DogeWorlds\command\WorldCommand;
use DogeDev\DogeWorlds\generator\void\VoidGenerator;
use DogeDev\DogeWorlds\language\Language;
use DogeDev\DogeWorlds\task\AsyncPoolCollectionTask;
use pocketmine\plugin\PluginBase;
use pocketmine\world\generator\GeneratorManager;

class DogeWorlds extends PluginBase
{
    protected DogeWorldsAsyncPool $asyncPool;
    protected Language $language;

    public function getAsyncPool(): DogeWorldsAsyncPool
    {
        return $this->asyncPool;
    }

    public function getLanguage(): Language
    {
        return $this->language;
    }

    protected function onLoad(): void
    {
        GeneratorManager::getInstance()->addGenerator(VoidGenerator::class, "void");
        foreach ($this->getResources() as $resource) {
            $this->saveResource($resource->getFilename());
        }
    }

    protected function onEnable(): void
    {
        $this->asyncPool = new DogeWorldsAsyncPool($this->getConfig()->get("thread")["workers"] ?? 2, $this->getConfig()->get("thread")["worker-limit"] ?? 256, $this->getServer()->getLoader(), $this->getServer()->getLogger(), $this->getServer()->getTickSleeper());
        $this->language = new Language($this->getConfig()->get("language") ?? "en-US", $this->getDataFolder());
        $this->getScheduler()->scheduleRepeatingTask(new AsyncPoolCollectionTask($this), $this->getConfig()->get("thread")["collection-period"] ?? 1);
        $this->getServer()->getCommandMap()->registerAll("dogeworlds",
            [
                new WorldCommand($this)
            ]
        );
    }
}
