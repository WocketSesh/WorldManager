<?php

namespace sesh\worldmanager\utils;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;



abstract class ACommand extends Command
{


    /**
     * @var array<string,ACommand>
     */
    public $subCommands = [];
    /**
     * @var array<string,string>
     */
    public $subCommandAliases = [];
    public ? ACommand $cmdParent = null;

    public function __construct(string $name, $description = "", $usageMessage = null, $aliases = array())
    {
        parent::__construct($name, $description, $usageMessage, $aliases);
    }

    public function runOrSubCommand(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($this->getPermission() != null && !$sender->hasPermission($this->getPermission())) {
            $sender->sendMessage(TextFormat::RED . "You do not have permission to use this command.");
            return;
        }
        if (count($this->subCommands) && count($args)) {

            if (array_key_exists($a = $this->resolveAlias($args[0]), $this->subCommands)) {

                array_shift($args);
                $this->subCommands[$a]->runOrSubCommand($sender, $commandLabel, $args);

                return;
            }
        }

        $this->run($sender, $commandLabel, $args);
    }


    abstract public function run(CommandSender $sender, string $commandLabel, array $args);

    //Sub commands dont need to implement an execute, only the parent command does that will actually be called,
    //All other calls are handled through ACommand::runOrSubCommand
    abstract public function execute(CommandSender $sender, string $commandLabel, array $args);


    public function resolveAlias(string $alias)
    {
        if (array_key_exists($alias, $this->subCommands) || !array_key_exists($alias, $this->subCommandAliases))
            return $alias;
        else
            return $this->subCommandAliases[$alias];
    }


    public function registerSubCommand(ACommand $command)
    {
        $command->cmdParent = $this;
        foreach ($command->getAliases() as $a)
            $this->subCommandAliases[$a] = $command->getName();
        $this->subCommands[$command->getName()] = $command;
    }


    //useless since setting usage message does nothing? :D
    public function getUsageMessage()
    {
        $toReturn = [];

        if (count($this->subCommands)) {
            foreach ($this->subCommands as $subCommand) {

                array_push($toReturn, ...$subCommand->getUsageMessage());
            }
        } else {
            $a = array($this->getName());
            $p = $this->cmdParent;

            while ($p != null) {
                if ($p->cmdParent == null)
                    $a[] = "/" . $p->getName();
                else
                    $a[] = $p->getName();
                $p = $p->cmdParent;
            }
            $toReturn[] = array_reverse($a);

            return $toReturn;
        }

        if ($this->cmdParent == null) {
            $toReturn = join(" OR ", array_map(function ($arr) {
                return join(" ", $arr);
            }, $toReturn));
        }

        return $toReturn;
    }
}