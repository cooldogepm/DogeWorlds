<?php

declare(strict_types=1);

namespace DogeDev\DogeWorlds\generator;

use pocketmine\math\Vector3;
use pocketmine\world\ChunkManager;
use pocketmine\world\generator\Generator;

abstract class DogeWorldGenerator extends Generator
{
    public function generateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void
    {
    }

    public function populateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void
    {
    }

    public function getSpawnLocation(): Vector3
    {
        return new Vector3(256, 69, 256);
    }
}
