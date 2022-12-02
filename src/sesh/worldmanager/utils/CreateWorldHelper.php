<?php

namespace sesh\worldmanager\utils;



use pocketmine\world\generator\GeneratorManager;
use pocketmine\world\WorldCreationOptions;
use sesh\worldmanager\WorldManager;



class CreateWorldReturn
{
    public function __construct(public bool $success, public ?string $error = null)
    {

    }

    public function didError(): bool
    {
        return !$this->success;
    }
}

class CreateWorldHelper
{
    static function CreateWorld(string $name, string $type = "normal", bool $autoload = false): CreateWorldReturn
    {
        $worlds = WorldManager::getWorldNames();

        if (in_array($name, $worlds)) {
            return new CreateWorldReturn(false, "World already exists with name " . $name);
        }

        $server = WorldManager::getInstance()->getServer();
        $opts = new WorldCreationOptions();
        $gen = GeneratorManager::getInstance()->getGenerator($type);
        if ($gen == null)
            return new CreateWorldReturn(false, "$type is not a valid generator.");

        $opts->setGeneratorClass($gen->getGeneratorClass());


        $server->getWorldManager()->generateWorld($name, $opts, false);

        $w = ["name" => $name, "loaded" => $autoload];

        if (!$autoload && $server->getWorldManager()->isWorldLoaded($name))
            $server->getWorldManager()->unloadWorld($server->getWorldManager()->getWorldByName($name));
        if ($autoload)
            $w["world"] = $server->getWorldManager()->getWorld($name);

        WorldManager::$Worlds[] = $w;

        return new CreateWorldReturn(true, null);
    }

    static function UnloadWorld(string $name): CreateWorldReturn
    {
        if (!in_array($name, WorldManager::getWorldNames())) {
            return new CreateWorldReturn(false, "$name is not a valid world.");
        }

        if (!WorldManager::getWorlds()[$name]["loaded"]) {
            return new CreateWorldReturn(false, "$name is not loaded, cannot unload.");
        }

        WorldManager::getInstance()->getServer()->getWorldManager()->unloadWorld(WorldManager::getWorldNames()[$name]["world"]);

        return new CreateWorldReturn(true);
    }

    static function LoadWorld(string $name): CreateWorldReturn
    {
        if (!in_array($name, WorldManager::getWorldNames())) {
            return new CreateWorldReturn(false, "$name is not a valid world.");
        }

        if (WorldManager::getWorlds()[$name]["loaded"]) {
            return new CreateWorldReturn(false, "$name is already loaded, cannot load.");
        }

        WorldManager::getInstance()->getServer()->getWorldManager()->loadWorld($name);

        return new CreateWorldReturn(true);
    }

    static function CloneWorld(string $name, string $newName, bool $autoload = false): CreateWorldReturn
    {
        $server = WorldManager::getInstance()->getServer();
        $worlds = WorldManager::getWorldNames();
        $worldsDir = $server->getDataPath() . "worlds";



        if (!in_array($name, $worlds)) {
            return new CreateWorldReturn(false, "Attempt to clone world " . $name . " but that world does not exist.");
        }

        if (in_array($newName, $worlds)) {
            return new CreateWorldReturn(false, "Attempt to clone world " . $name . " with name " . $newName . " but a world with that name already exists.");
        }

        if (file_exists($worldsDir . "/" . $newName) && is_dir($worldsDir . "/" . $newName)) {
            return new CreateWorldReturn(false, "Directory already exists with name " . $newName . ", while trying to clone world.");
        }


        mkdir($worldsDir . "/" . $newName);
        self::RecursiveCopy($worldsDir . "/" . $name, $worldsDir . "/" . $newName);

        if ($autoload) {
            $server->getWorldManager()->loadWorld($newName);
        } else {
            $w = ["name" => $newName, "loaded" => false];
            WorldManager::$Worlds[$newName] = $w;
        }
        return new CreateWorldReturn(true);
    }

    static function DeleteWorld(string $world): CreateWorldReturn
    {
        $worlds = WorldManager::getWorldNames();
        $worldDir = WorldManager::getInstance()->getServer()->getDataPath() . "worlds";

        if (!in_array($world, $worlds)) {
            return new CreateWorldReturn(false, "World " . $world . " does not exist.");
        }
        //😱
        shell_exec("rm -rf '$worldDir/$world'");

        return new CreateWorldReturn(true);
    }


    static function RecursiveCopy(string $dir, string $newDir)
    {
        $toCopy = array_diff(scandir($dir), array(".", ".."));

        foreach ($toCopy as $file) {
            if (is_dir($dir . "/" . $file)) {
                mkdir($newDir . "/" . $file);
                self::RecursiveCopy($dir . "/" . $file, $newDir . "/" . $file);
            } else {
                copy($dir . "/" . $file, $newDir . "/" . $file);
            }
        }
    }
}