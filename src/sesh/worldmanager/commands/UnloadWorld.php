<?php

namespace sesh\worldmanager\commands;

use pocketmine\utils\TextFormat;
use sesh\worldmanager\utils\ACommand;
use sesh\worldmanager\utils\ManageWorlds;


class UnloadWorld extends ACommand
{

    public function __construct()
    {
        parent::__construct("unload", "unloads a world.", "/wm unload <world>", ["u"]);
    }


    public function run(\pocketmine\command\CommandSender $sender, string $commandLabel, array $args)
    {
        if (!count($args)) {
            //show list form
            return;
        }

        $world = $args[0];

        $unloaded = ManageWorlds::UnloadWorld($world);

        if ($unloaded->didError()) {
            $sender->sendMessage(TextFormat::RED . $unloaded->error);
            return;
        }

        $sender->sendMessage(TextFormat::GREEN . "Unloaded $world successfully.");
    }

    public function execute(\pocketmine\command\CommandSender $sender, string $commandLabel, array $args)
    {
        $this->runOrSubCommand($sender, $commandLabel, $args);
    }
}