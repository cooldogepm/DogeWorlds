<?php

declare(strict_types=1);

namespace DogeDev\DogeWorlds;

use DogeDev\DogeWorlds\asynchronous\DogeWorldsAsyncPool;
use DogeDev\DogeWorlds\command\WorldCommand;
use DogeDev\DogeWorlds\generator\void\VoidGenerator;
use pocketmine\plugin\PluginBase;
use pocketmine\world\generator\GeneratorManager;

class DogeWorlds extends PluginBase
{
    protected DogeWorldsAsyncPool $asyncPool;

    public function getAsyncPool(): DogeWorldsAsyncPool
    {
        return $this->asyncPool;
    }

    protected function onLoad(): void
    {
        GeneratorManager::getInstance()->addGenerator(VoidGenerator::class, "void");
    }

    protected function onEnable(): void
    {
        $this->asyncPool = new DogeWorldsAsyncPool($this->getConfig()->get("thread")["workers"], $this->getConfig()->get("thread")["worker-limit"], $this->getServer()->getLoader(), $this->getServer()->getLogger(), $this->getServer()->getTickSleeper());
        $this->getServer()->getCommandMap()->registerAll("dogeworlds",
            [
                new WorldCommand($this)
            ]
        );
    }
}
