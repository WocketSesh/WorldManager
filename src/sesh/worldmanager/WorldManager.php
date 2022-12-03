<?php

namespace sesh\worldmanager;

use Error;
use pocketmine\plugin\PluginBase;
use pocketmine\world\World;
use pocketmine\utils\TextFormat;
use pocketmine\world\generator\GeneratorManager;



use sesh\worldmanager\commands\RootCommand;
use sesh\worldmanager\events\WorldEvents;
use sesh\worldmanager\utils\CreateWorldError;
use sesh\worldmanager\utils\CreateWorldHelper;
use sesh\worldmanager\utils\VoidGenerator;





class WorldManager extends PluginBase
{

    public static WorldManager $instance;

    /**
     * 
     * @var array{name: array{name: string, loaded: bool, world: World}}
     */
    public static $Worlds = [];


    public static function getInstance(): PluginBase
    {
        return self::$instance;
    }


    /**
     * 
     * @return array{name: array{name: string, loaded: bool, world: World}}
     */
    public static function getWorlds(): array
    {
        return self::$Worlds;
    }

    public static function getWorldNames(): array
    {
        return array_map(function ($x) {
            return $x["name"];
        }, self::$Worlds);
    }


    public function fetchWorlds()
    {
        $worldDir = $this->getServer()->getDataPath() . "/worlds";
        $worlds = array_diff(scandir($worldDir), array(".", ".."));

        if (!$worlds) {
            throw new Error("Invalid worlds directory");
        }

        foreach ($worlds as $worldName) {
            $this->getLogger()->info($worldName);
            if (is_dir($worldDir . "/" . $worldName) && in_array("level.dat", array_diff(scandir($worldDir . "/" . $worldName), array(".", "..")))) {
                $sw = ["name" => $worldName];

                if ($this->getServer()->getWorldManager()->isWorldLoaded($worldName)) {
                    $sw["loaded"] = true;
                    $sw["world"] = $this->getServer()->getWorldManager()->getWorldByName($worldName);
                } else
                    $sw["loaded"] = false;

                self::$Worlds[$worldName] = $sw;
            }
        }

        self::getLogger()->info("Fetched all worlds");
    }

    public function onLoad(): void
    {
        $this->getLogger()->info(TextFormat::DARK_GREEN . "World Manager Plugin Loaded.");
    }

    public function onEnable(): void
    {


        self::$instance = $this;
        $this->fetchWorlds();

        $this->getServer()->getPluginManager()->registerEvents(new WorldEvents, $this);
        $this->getServer()->getCommandMap()->register("wm", new RootCommand);

        GeneratorManager::getInstance()->addGenerator(VoidGenerator::class, "void", fn() => null);

        $this->getLogger()->info(TextFormat::DARK_GREEN . "World Manager Plugin Enabled.");
    }

    public function onDisable(): void
    {
        $this->getLogger()->info(TextFormat::RED . "world Manager Disabled.");
    }


}