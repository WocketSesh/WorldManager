<?php

namespace sesh\worldmanager\commands;

use pocketmine\command\CommandSender;
use pocketmine\permission\DefaultPermissionNames;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use sesh\worldmanager\forms\AButton;
use sesh\worldmanager\forms\AForm;
use sesh\worldmanager\forms\AFormResponse;
use sesh\worldmanager\forms\ASimpleForm;
use sesh\worldmanager\forms\Button;
use sesh\worldmanager\forms\CustomForm;
use sesh\worldmanager\forms\Dropdown;
use sesh\worldmanager\forms\Input;
use sesh\worldmanager\forms\ModalForm;
use sesh\worldmanager\forms\SimpleForm;
use sesh\worldmanager\forms\Toggle;
use sesh\worldmanager\utils\ACommand;

class RootCommand extends ACommand
{
    public function __construct()
    {
        $this->setPermission(DefaultPermissionNames::GROUP_OPERATOR);

        $this->registerSubCommand(new CreateWorld);
        $this->registerSubCommand(new LoadWorld);
        $this->registerSubCommand(new UnloadWorld);
        $this->registerSubCommand(new DeleteWorld);
        $this->registerSubCommand(new CloneWorld);
        parent::__construct("wm", "Root command for world manager", "/wm <create | delete | list>", ["worldmanager"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, $args)
    {
        $this->runOrSubCommand($sender, $commandLabel, $args);
    }


    public function run(CommandSender $sender, string $commandLabel, array $args)
    {

        //show root form
    }
}