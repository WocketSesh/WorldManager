<?php

namespace sesh\worldmanager\commands;

use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use sesh\worldmanager\utils\ACommand;
use sesh\worldmanager\WorldManager;


class TeleportWorld extends ACommand
{

    public function __construct()
    {
        parent::__construct("teleport", "Teleports to specified world", "/wm teleport <world>", ["t"]);
    }

    public function run(\pocketmine\command\CommandSender $sender, string $commandLabel, array $args)
    {
        if (!count($args)) {
            $sender->sendMessage(TextFormat::RED . "Invalid usage, expected: " . $this->usageMessage);
            return;
        }

        $w = $args[0];

        if (!array_key_exists($w, WorldManager::getWorlds())) {
            $sender->sendMessage(TextFormat::RED . "World $w does not exist.");
            return;
        }

        $world = WorldManager::getWorlds()[$w];

        if (!$world["loaded"]) {
            $sender->sendMessage(TextFormat::RED . "World $w is not loaded, load it before attempting to teleport.");
            return;
        }

        if ($sender instanceof Player) {
            $sender->teleport($world["world"]->getSafeSpawn());
            $sender->sendMessage(TextFormat::GREEN . "Teleported to world " . $world["name"]);
        }

    }


    public function execute(\pocketmine\command\CommandSender $sender, string $commandLabel, array $args)
    {
        $this->runOrSubCommand($sender, $commandLabel, $args);
    }
}