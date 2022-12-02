<?php

namespace sesh\worldmanager\commands;

use pocketmine\utils\TextFormat;
use sesh\worldmanager\utils\ACommand;
use sesh\worldmanager\utils\CreateWorldHelper;


class LoadWorld extends ACommand
{

    public function __construct()
    {
        parent::__construct("load", "loads a world", "/wm load <world>", ["l"]);
    }


    public function run(\pocketmine\command\CommandSender $sender, string $commandLabel, array $args)
    {
        if (!count($args)) {
            //show list form
            return;
        }

        $world = $args[0];

        $loaded = CreateWorldHelper::LoadWorld($world);

        if ($loaded->didError()) {
            $sender->sendMessage(TextFormat::RED . $loaded->error);
            return;
        }

        $sender->sendMessage(TextFormat::GREEN . "loaded world $world successfully.");
    }

    public function execute(\pocketmine\command\CommandSender $sender, string $commandLabel, array $args)
    {
        $this->runOrSubCommand($sender, $commandLabel, $args);
    }
}