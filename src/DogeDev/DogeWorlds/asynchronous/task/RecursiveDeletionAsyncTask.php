<?php

declare(strict_types=1);

namespace DogeDev\DogeWorlds\asynchronous\task;

use DogeDev\DogeWorlds\utils\DirectoryUtils;

class RecursiveDeletionAsyncTask extends AsyncCallbackTask
{
    protected string $directories;

    public function __construct(array $directories)
    {
        $this->directories = serialize($directories);
    }

    public function onRun(): void
    {
        foreach (unserialize($this->directories) as $directory) {
            if ($directory === "." || $directory === "..") {
                continue;
            }
            DirectoryUtils::recursiveDelete($directory);
        }
    }
}
