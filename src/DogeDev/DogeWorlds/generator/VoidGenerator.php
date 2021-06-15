<?php

declare(strict_types=1);

namespace DogeDev\DogeWorlds\generator;

use pocketmine\world\ChunkManager;

class VoidGenerator extends DogeWorldGenerator
{
    public function generateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void
    {
        $chunk = $world->getChunk($chunkX, $chunkZ);
        for ($x = 0; $x < 16; ++$x) {
            for ($z = 0; $z < 16; ++$z) {
                for ($y = 0; $y < 168; ++$y) {
                    if ($spawn->getX() >> 4 === $chunkX && $spawn->getZ() >> 4 === $chunkZ) {
                        $chunk->setBlockId(0, 64, 0, BlockIds::GRASS);
                    } else {
                        $chunk->setBlockId($x, $y, $z, BlockIds::AIR);
                    }
                }
            }
        }

        $chunk->setGenerated(true);
    }

    public function populateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void
    {
        // TODO: Implement populateChunk() method.
    }
}
