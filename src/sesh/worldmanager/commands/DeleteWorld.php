<?php

namespace sesh\worldmanager\commands;

use pocketmine\utils\TextFormat;
use sesh\worldmanager\utils\ACommand;
use sesh\worldmanager\utils\ManageWorlds;


class DeleteWorld extends ACommand
{

    public function __construct()
    {
        parent::__construct("delete", "Deletes a world", "/wm delete <world>", ["d"]);
    }


    public function run(\pocketmine\command\CommandSender $sender, string $commandLabel, array $args)
    {
        if (!count($args)) {
            //show list form
            return;
        }

        $world = $args[0];

        $deleted = ManageWorlds::DeleteWorld($world);

        if ($deleted->didError()) {
            $sender->sendMessage(TextFormat::RED . $deleted->error);
            return;
        }

        $sender->sendMessage(TextFormat::GREEN . "Deleted world $world successfully.");
    }


    public function execute(\pocketmine\command\CommandSender $sender, string $commandLabel, array $args)
    {
        $this->runOrSubCommand($sender, $commandLabel, $args);
    }
}