<?php

declare(strict_types=1);

namespace DogeDev\DogeWorlds\generator\void;

use DogeDev\DogeWorlds\generator\DogeWorldGenerator;
use pocketmine\block\VanillaBlocks;
use pocketmine\world\ChunkManager;

class VoidGenerator extends DogeWorldGenerator
{
    public function generateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void
    {
        $chunk = $world->getChunk($chunkX, $chunkZ);
        for ($x = 0; $x < 16; ++$x) {
            for ($z = 0; $z < 16; ++$z) {
                for ($y = 0; $y < 168; ++$y) {
                    $spawn = $this->getSpawnLocation();
                    if ($spawn->getX() >> 4 === $chunkX && $spawn->getZ() >> 4 === $chunkZ) {
                        $chunk->setFullBlock(0, $spawn->getY(), 0, VanillaBlocks::STONE()->getFullId());
                    } else {
                        $chunk->setFullBlock($x, $y, $z, VanillaBlocks::AIR()->getFullId());
                    }
                }
            }
        }
    }
}
