<?php

declare(strict_types=1);

namespace DogeDev\DogeWorlds\asynchronous\task;

use DogeDev\DogeWorlds\asynchronous\DogeWorldsAsyncPool;
use pocketmine\scheduler\AsyncTask;

abstract class AsyncCallbackTask extends AsyncTask
{
    final public function onCompletion(): void
    {
        DogeWorldsAsyncPool::processAsyncCallback($this);
    }
}
