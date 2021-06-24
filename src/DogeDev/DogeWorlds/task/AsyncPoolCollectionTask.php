<?php

declare(strict_types=1);

namespace DogeDev\DogeWorlds\task;

use DogeDev\DogeWorlds\DogeWorlds;
use Exception;
use pocketmine\scheduler\Task;

class AsyncPoolCollectionTask extends Task
{
    protected DogeWorlds $plugin;

    public function __construct(DogeWorlds $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onRun(): void
    {
        try {
            $this->getPlugin()->getAsyncPool()->collectTasks();
        } catch (Exception $exception) {
            $this->getPlugin()->getLogger()->error($exception->getMessage());
        }
    }

    public function getPlugin(): DogeWorlds
    {
        return $this->plugin;
    }
}
