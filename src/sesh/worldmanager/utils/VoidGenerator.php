<?php

namespace sesh\worldmanager\utils;

use pocketmine\world\generator\Generator;

class VoidGenerator extends Generator
{

    /**
     * @param \pocketmine\world\ChunkManager $world
     * @param int $chunkX
     * @param int $chunkZ
     */
    public function generateChunk(\pocketmine\world\ChunkManager $world, int $chunkX, int $chunkZ): void
    {
    }

    /**
     *
     * @param \pocketmine\world\ChunkManager $world
     * @param int $chunkX
     * @param int $chunkZ
     */
    public function populateChunk(\pocketmine\world\ChunkManager $world, int $chunkX, int $chunkZ): void
    {
    }
}