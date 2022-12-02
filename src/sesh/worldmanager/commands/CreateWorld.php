<?php

namespace sesh\worldmanager\commands;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\world\generator\Generator;
use pocketmine\world\generator\GeneratorManager;
use sesh\worldmanager\utils\ACommand;
use sesh\worldmanager\utils\CreateWorldHelper;

class CreateWorld extends ACommand
{

    public function __construct()
    {
        parent::__construct("create", "Creates world", "/wm create <name> [type]", ["c"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, $args)
    {
        $this->runOrSubCommand($sender, $commandLabel, $args);
    }


    public function run(CommandSender $sender, string $commandLabel, array $args)
    {
        //show createworld form
        if (!count($args)) {
            return;
        }

        $name = $args[0];
        $type = $args[1] ?? "normal";
        $autoload = $args[2] ?? false;

        if (GeneratorManager::getInstance()->getGenerator($type) == null) {
            $sender->sendMessage(TextFormat::RED . "Could not find generator with name $type");
            return;
        }

        $created = CreateWorldHelper::CreateWorld($name, $type, $autoload);

        if ($created->didError()) {
            $sender->sendMessage(TextFormat::RED . $created->error);
            return;
        }


        $sender->sendMessage(TextFormat::GREEN . "Successfully created world $name, with type $type.");
    }
}