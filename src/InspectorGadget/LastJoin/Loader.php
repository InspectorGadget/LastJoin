<?php
/**
 * Created by PhpStorm.
 * User: RTG
 * Date: 2/15/2019
 * Time: 9:56 AM
 *
 * .___   ________
 * |   | /  _____/
 * |   |/   \  ___
 * |   |\    \_\  \
 * |___| \______  /
 *              \/
 *
 * You can edit the plugin, just don't remove my ASCII and author.
 * Standard Copyright License applied.
 * All rights reserved InspectorGadget (c) 2019
 */

namespace InspectorGadget\LastJoin;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;

use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;

class Loader extends PluginBase {

    const PREFIX = "[LastJoin] ";

    public function onEnable(): void {
        if (!is_dir($this->getDataFolder())) { @mkdir($this->getDataFolder(), 0777, true); }
        if (!is_dir($this->getDataFolder() . "players/")) { @mkdir($this->getDataFolder() . "players/", 077, true); }

        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        switch(strtolower($command->getName())) {
            case "last":
                if (!isset($args[0])) {
                    $sender->sendMessage(TF::GREEN . "[Usage] /last <list | reset (only admin) | all (only admin)>");
                    return true;
                }

                switch(strtolower($args[0])) {
                    case "list":
                        if (!$sender instanceof Player) {
                            $sender->sendMessage(TF::RED . self::PREFIX . "Only in-game bud");
                            return true;
                        }

                        if (!isset($args[1])) {
                            $this->getData(strtolower($sender->getName()), $sender);
                            return true;
                        }

                        if ($sender->hasPermission("lastjoin.admin") || $sender->isOp()) {
                            $this->getData(strtolower($args[1]), $sender);
                            return true;
                        } else {
                            $sender->sendMessage(TF::RED . "This command only works for Users with Perm and/or OP!");
                        }
                        return true;
                    break;

                    case "reset":
                        if (!$sender->hasPermission("lastjoin.admin") || !$sender->isOp()) {
                            $sender->sendMessage(TF::RED . "This command only works for Users with Perm and/or OP!");
                            return true;
                        }

                        if (!isset($args[1])) {
                            $sender->sendMessage(TF::GREEN . self::PREFIX . "[Usage] /last reset [player]");
                            return true;
                        }

                        $this->resetData(strtolower($args[1]), $sender);
                        return true;
                    break;

                    case "all":
                        if (!$sender->hasPermission("lastjoin.admin") || !$sender->isOp()) {
                            $sender->sendMessage(TF::RED . "This command only works for Users with Perm and/or OP!");
                            return true;
                        }

                        $this->getAllPlayers($sender);
                        return true;
                    break;

                }
                return true;
            break;
        }
    }

    public function setData(array $data) {
        $username = $data['username'];
        if (!is_file($this->getDataFolder() . "players/{$username}.txt")) {
            $config = new Config($this->getDataFolder() . "players/{$username}.txt", Config::ENUM, array());
            $config->set($data['date'], true);
            $config->save();
        } else {
            $config = new Config($this->getDataFolder() . "players/{$username}.txt", Config::ENUM, array());
            $config->set($data['date'], true);
            $config->save();
        }
    }

    public function getData($username, $sender) {
        if (!is_file($this->getDataFolder() . "players/{$username}.txt")) {
            $sender->sendMessage(TF::YELLOW . self::PREFIX . "Config for player named {$username} does not exist!");
            return true;
        }

        $config = new Config($this->getDataFolder() . "players/{$username}.txt", Config::ENUM);
        $sender->sendMessage("-- Login Data for {$username} --");
        foreach (array_keys($config->getAll()) as $list) {
            $sender->sendMessage("- {$list}");
        }
    }

    public function resetData($username, $sender) {
        if (!is_file($this->getDataFolder() . "players/{$username}.txt")) {
            $sender->sendMessage(TF::YELLOW . self::PREFIX . "Config for player named {$username} does not exist!");
            return true;
        }

        unlink($this->getDataFolder() . "players/{$username}.txt");
        $sender->sendMessage(TF::GREEN . self::PREFIX . "Deleted Config file for Player {$username}");
    }

    public function getAllPlayers($sender) {
        $files = scandir($this->getDataFolder() . "players/");
        $sender->sendMessage("-- All Players --");
        foreach ($files as $file) {
            if ($file === ".." || $file === ".") {
                continue;
            }

            $name = explode(".", $file);
            $sender->sendMessage($name[0]);
        }
    }

    public function onDisable(): void { }

}