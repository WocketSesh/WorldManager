<?php

namespace sesh\worldmanager\commands;

use pocketmine\form\Form;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use sesh\worldmanager\utils\ACommand;
use sesh\worldmanager\utils\CreateWorldHelper;

class CloneWorld extends ACommand
{


    public function __construct()
    {
        parent::__construct("clone", "Clones a world.", "/wm clone <name> <world> [autoload]", ["cl"]);
    }


    public function run(\pocketmine\command\CommandSender $sender, string $commandLabel, array $args)
    {


        if (count($args) < 2) {
            $sender->sendMessage("Expected usage: /wm clone <name> <world> [autoload]");
            return;
        }

        $name = $args[0];
        $world = $args[1];
        $autoload = $args[2] ?? false;

        $cloned = CreateWorldHelper::CloneWorld($world, $name, $autoload);

        if ($cloned->didError()) {
            $sender->sendMessage(TextFormat::RED . $cloned->error);
            return;
        }

        $sender->sendMessage(TextFormat::GREEN . "Successfully cloned world $world to $name");
    }


    public function execute(\pocketmine\command\CommandSender $sender, string $commandLabel, array $args)
    {
        $this->runOrSubCommand($sender, $commandLabel, $args);
    }
}