<?php

namespace sesh\worldmanager\events;

use pocketmine\event\Listener;
use pocketmine\event\world\WorldLoadEvent;
use pocketmine\event\world\WorldUnloadEvent;
use sesh\worldmanager\ServerWorld;
use sesh\worldmanager\WorldManager;

class WorldEvents implements Listener
{
    public function onWorldLoad(WorldLoadEvent $event): void
    {
        $worldName = $event->getWorld()->getFolderName();

        if (array_key_exists($worldName, WorldManager::$Worlds)) {
            WorldManager::$Worlds[$worldName]["loaded"] = true;
            WorldManager::$Worlds[$worldName]["world"] = $event->getWorld();
        } else {
            $sw = ["name" => $worldName, "loaded" => true, "world" => $event->getWorld()];
            WorldManager::$Worlds[$worldName] = $sw;
        }
    }

    public function onWorldUnload(WorldUnloadEvent $event): void
    {
        $worldName = $event->getWorld()->getFolderName();

        if (array_key_exists($worldName, WorldManager::$Worlds)) {
            WorldManager::$Worlds[$worldName]["loaded"] = false;
            WorldManager::$Worlds[$worldName]["world"] = null;
        } else {
            $sw = ["name" => $worldName];
            WorldManager::$Worlds[$worldName] = $sw;
        }

    }
}