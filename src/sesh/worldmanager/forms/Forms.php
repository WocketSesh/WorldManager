<?php

namespace sesh\worldmanager\forms;

use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\world\World;
use sesh\formsapi\elements\Button;
use sesh\formsapi\elements\Dropdown;
use sesh\formsapi\elements\Image;
use sesh\formsapi\elements\Input;
use sesh\formsapi\elements\Toggle;
use sesh\formsapi\forms\CustomForm;
use sesh\formsapi\forms\SimpleForm;
use sesh\worldmanager\commands\CreateWorld;
use sesh\worldmanager\utils\CreateWorldHelper;
use sesh\worldmanager\WorldManager;


/**
 * TODO: Add show player info form.
 */
class Forms
{

    public static function ShowRootForm(Player $player)
    {
        $form = new SimpleForm("World Manager");
        $form->addButton(new Button("List Worlds", function (Player $player, SimpleForm $response) {
            self::ShowWorldListForm($player);
        }));
        $form->addButton(new Button("Create World", function (Player $player, SimpleForm $response) {
            self::ShowCreateWorldForm($player);
        }));
        $form->addButton(new Button("Clone World", function (Player $player, SimpleForm $response) {
            self::ShowCloneWorldForm($player);
        }));

        $player->sendForm($form);

    }


    public static function ShowWorldListForm(Player $player)
    {
        $form = new SimpleForm("World List");


        foreach (WorldManager::getWorlds() as $world) {


            $b = new Button(
                //String interpolation is weird idk 
                $world["loaded"]
                ? $world["name"] . " - " . TextFormat::GREEN . "LOADED"
                : $world["name"] . " - " . TextFormat::RED . "UNLOADED",
                function (Player $player, SimpleForm $response) use ($world) {
                    self::ShowWorldInfoForm($player, $world);
                },
            );

            if ($world["loaded"]) {
                $gen = $world["world"]->getProvider()->getWorldData()->getGenerator();
                $b->setImage(new Image(CreateWorldHelper::TextureFromGenerator($gen), Image::PATH_TYPE));
            }
            $form->addButton($b);
        }
        $player->sendForm($form);

    }
    /**
     * 
     * @param array{name: string, loaded: bool, world: World} $world
     */
    public static function ShowWorldInfoForm(Player $player, array $world)
    {
        $form = new SimpleForm("World: " . $world["name"]);
        $loaded = $world["loaded"];
        $form->addButton(
            new Button(
                $loaded
                ? "Unload {$world["name"]}"
                : "Load  {$world["name"]}",
                function (Player $player, SimpleForm $response) use ($world, $loaded) {
                    WorldManager::getInstance()->getServer()->dispatchCommand(
                        $player,
                        $loaded
                        ? "wm unload {$world['name']}"
                        : "wm load {$world['name']}"
                    );
                }
            )
        );

        $form->addButton(new Button("List Players", function (Player $player, SimpleForm $response) use ($world) {
            self::ShowListPlayersForm($player, $world["world"]);
        }));

        $form->addButton(new Button("Teleport to", function (Player $player, SimpleForm $response) use ($world) {
            WorldManager::getInstance()->getServer()->dispatchCommand($player, "wm teleport " . $world["name"]);
        }));

        $form->addButton(new Button(TextFormat::RED . "Delete World", function (Player $player, SimpleForm $response) use ($world) {
            WorldManager::getInstance()->getServer()->dispatchCommand($player, "wm delete " . $world["name"]);
        }));

        $player->sendForm($form);
    }

    public static function ShowListPlayersForm(Player $player, World $world)
    {
        $form = new SimpleForm("Players in " . $world->getFolderName());
        foreach ($world->getPlayers() as $p) {
            $form->addButton(new Button($p->getName(), function (Player $player, SimpleForm $response) {
                //show player info form;
            }));
        }

        $player->sendForm($form);

    }

    public static function ShowCreateWorldForm(Player $player)
    {
        $form = new CustomForm("Create World");
        $form->addInput(new Input("World Name", "world", "World Name"));
        $form->addDropdown(new Dropdown("World Type", ["flat", "normal", "void", "nether"], 1));
        $form->addToggle(new Toggle("Auto Load", false));

        $form->onClick(function (Player $player, CustomForm $response) {
            $name = $response->getInput(0)->getText();
            $type = $response->getDropdown(1)->getSelected();
            $autoload = $response->getToggle(2)->isToggled();


            WorldManager::getInstance()->getServer()->dispatchCommand($player, "wm create " . $name . " " . $type . " " . $autoload);
        });

        $player->sendForm($form);
    }

    public static function ShowCloneWorldForm(Player $player)
    {

    }
}