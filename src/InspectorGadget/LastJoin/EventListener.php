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

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;

class EventListener implements Listener {

    public $plugin;

    public function __construct(Loader $plugin) {
        $this->plugin = $plugin;
    }

    public function getPlugin(): Loader {
        return $this->plugin;
    }

    public function onJoin(PlayerJoinEvent $event) {
        $data = array(
          "username" => strtolower($event->getPlayer()->getName()),
          "date" => date("F j, Y, g:i a")
        );
        $this->getPlugin()->setData((array) $data);
    }

}