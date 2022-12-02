<?php

namespace sesh\worldmanager\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\defaults\TeleportCommand;
use pocketmine\permission\DefaultPermissionNames;

use pocketmine\player\Player;
use sesh\formsapi\elements\Button;
use sesh\formsapi\elements\Image;
use sesh\formsapi\elements\Slider;
use sesh\formsapi\elements\StepSlider;
use sesh\formsapi\forms\CustomForm;
use sesh\formsapi\forms\SimpleForm;
use sesh\worldmanager\forms\Forms;
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
        $this->registerSubCommand(new TeleportWorld);
        parent::__construct("wm", "Root command for world manager", "/wm <create | delete | list>", ["worldmanager"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, $args)
    {
        $this->runOrSubCommand($sender, $commandLabel, $args);
    }


    public function run(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player)
            return;
        Forms::ShowRootForm($sender);
    }
}