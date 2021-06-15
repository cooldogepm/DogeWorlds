<?php

declare(strict_types=1);

namespace DogeDev\DogeWorlds\asynchronous;

use ClassLoader;
use Closure;
use DogeDev\DogeWorlds\asynchronous\task\AsyncCallbackTask;
use pocketmine\scheduler\AsyncPool;
use pocketmine\snooze\SleeperHandler;
use ThreadedLogger;

class DogeWorldsAsyncPool extends AsyncPool
{
    protected static DogeWorldsAsyncPool $instance;
    /**
     * @var Closure[]
     */
    protected static array $callbacks;

    public function __construct(int $size, int $workerMemoryLimit, ClassLoader $classLoader, ThreadedLogger $logger, SleeperHandler $eventLoop)
    {
        parent::__construct($size, $workerMemoryLimit, $classLoader, $logger, $eventLoop);
        DogeWorldsAsyncPool::$instance = $this;
        DogeWorldsAsyncPool::$callbacks = [];
    }

    /**
     * @return Closure[]
     */
    public static function getCallbacks(): array
    {
        return DogeWorldsAsyncPool::$callbacks;
    }

    public static function processAsyncCallback(AsyncCallbackTask $asyncCallback): void
    {
        if (isset(DogeWorldsAsyncPool::$callbacks[spl_object_hash($asyncCallback)])) {
            $callback = DogeWorldsAsyncPool::$callbacks[spl_object_hash($asyncCallback)];
            $callback($asyncCallback);
        }
    }

    public static function getInstance(): DogeWorldsAsyncPool
    {
        return DogeWorldsAsyncPool::$instance;
    }

    public function queueAsyncCallback(AsyncCallbackTask $asyncCallback, ?Closure $callback): void
    {
        $this->submitTask($asyncCallback);
        if ($callback !== null) {
            DogeWorldsAsyncPool::$callbacks[spl_object_hash($asyncCallback)] = $callback;
        }
    }
}
