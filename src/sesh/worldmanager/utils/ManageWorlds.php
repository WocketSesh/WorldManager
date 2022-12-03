<?php

namespace sesh\worldmanager\utils;



use pocketmine\world\generator\GeneratorManager;
use pocketmine\world\WorldCreationOptions;
use sesh\worldmanager\WorldManager;

class ManageWorldsReturn
{
    public function __construct(public bool $success, public ?string $error = null)
    {

    }

    public function didError(): bool
    {
        return !$this->success;
    }
}

class ManageWorlds
{
    static function CreateWorld(string $name, string $type = "normal", bool $autoload = false): ManageWorldsReturn
    {
        $worlds = WorldManager::getWorldNames();

        if (in_array($name, $worlds)) {
            return new ManageWorldsReturn(false, ManageWorldsError::Error(ManageWorldsError::NAME_EXISTS, $name));
        }

        $server = WorldManager::getInstance()->getServer();
        $opts = new WorldCreationOptions();
        $gen = GeneratorManager::getInstance()->getGenerator($type);
        if ($gen == null)
            return new ManageWorldsReturn(false, ManageWorldsError::Error(ManageWorldsError::INVALID_GENERATOR, $type));

        $opts->setGeneratorClass($gen->getGeneratorClass());


        $server->getWorldManager()->generateWorld($name, $opts, true);

        $w = ["name" => $name, "loaded" => $autoload];

        if (!$autoload && $server->getWorldManager()->isWorldLoaded($name))
            $server->getWorldManager()->unloadWorld($server->getWorldManager()->getWorldByName($name));
        if ($autoload)
            $w["world"] = $server->getWorldManager()->getWorldByName($name);

        WorldManager::$Worlds[$name] = $w;

        return new ManageWorldsReturn(true, null);
    }

    static function UnloadWorld(string $name): ManageWorldsReturn
    {
        if (!in_array($name, WorldManager::getWorldNames())) {
            return new ManageWorldsReturn(false, ManageWorldsError::Error(ManageWorldsError::INVALID_WORLD, $name));
        }

        if (!WorldManager::getWorlds()[$name]["loaded"]) {
            return new ManageWorldsReturn(false, ManageWorldsError::Error(ManageWorldsError::NOT_LOADED, $name));
        }

        if (WorldManager::getInstance()->getServer()->getWorldManager()->getDefaultWorld()->getFolderName() == $name) {
            return new ManageWorldsReturn(false, ManageWorldsError::UNLOAD_DEFAULT);
        }

        WorldManager::getInstance()->getServer()->getWorldManager()->unloadWorld(WorldManager::getWorlds()[$name]["world"]);

        return new ManageWorldsReturn(true);
    }

    static function LoadWorld(string $name): ManageWorldsReturn
    {
        if (!in_array($name, WorldManager::getWorldNames())) {
            return new ManageWorldsReturn(false, ManageWorldsError::Error(ManageWorldsError::INVALID_WORLD, $name));
        }

        if (WorldManager::getWorlds()[$name]["loaded"]) {
            return new ManageWorldsReturn(false, ManageWorldsError::Error(ManageWorldsError::ALREADY_LOADED, $name));
        }

        WorldManager::getInstance()->getServer()->getWorldManager()->loadWorld($name);

        return new ManageWorldsReturn(true);
    }

    static function CloneWorld(string $name, string $newName, bool $autoload = false): ManageWorldsReturn
    {
        $server = WorldManager::getInstance()->getServer();
        $worlds = WorldManager::getWorldNames();
        $worldsDir = $server->getDataPath() . "worlds";



        if (!in_array($name, $worlds)) {
            return new ManageWorldsReturn(false, ManageWorldsError::Error(ManageWorldsError::INVALID_WORLD, $name));
        }

        if (in_array($newName, $worlds)) {
            return new ManageWorldsReturn(false, ManageWorldsError::Error(ManageWorldsError::NAME_EXISTS, $name));
        }

        if (file_exists($worldsDir . "/" . $newName) && is_dir($worldsDir . "/" . $newName)) {
            return new ManageWorldsReturn(false, ManageWorldsError::Error(ManageWorldsError::DIR_EXISTS, $newName));
        }


        mkdir($worldsDir . "/" . $newName);
        self::RecursiveCopy($worldsDir . "/" . $name, $worldsDir . "/" . $newName);

        if ($autoload) {
            $server->getWorldManager()->loadWorld($newName);
        } else {
            $w = ["name" => $newName, "loaded" => false];
            WorldManager::$Worlds[$newName] = $w;
        }
        return new ManageWorldsReturn(true);
    }

    static function DeleteWorld(string $world): ManageWorldsReturn
    {
        $worlds = WorldManager::getWorldNames();
        $worldDir = WorldManager::getInstance()->getServer()->getDataPath() . "worlds";

        if (!in_array($world, $worlds)) {
            return new ManageWorldsReturn(false, ManageWorldsError::Error(ManageWorldsError::INVALID_WORLD, $world));
        }
        //😱
        shell_exec("rm -rf '$worldDir/$world'");


        unset(WorldManager::$Worlds[$world]);

        return new ManageWorldsReturn(true);
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


    static function TextureFromGenerator(string $gen)
    {
        $texture = "";
        switch ($gen) {
            case "void":
                $texture = "textures/blocks/concrete_black.png";
                break;
            case "nether":
            case "hell":
                $texture = "textures/blocks/netherrack.png";
                break;
            case "normal":
            case "flat":
                $texture = "textures/blocks/grass_side_carried.png";
                break;
            default:
                break;
        }
        return $texture;
    }
}