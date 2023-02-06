<?php

namespace RemBog\MixedInventoryStick;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener
{
    private array $config = [];

    private array $cooldown = [];

    protected function onEnable(): void
    {
        @mkdir($this->getDataFolder());
        $config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        $this->config = $config->getAll();

        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    protected function onLoad(): void
    {
        $this->saveConfig();
    }

    public function onEntityDamageByEntity(EntityDamageByEntityEvent $event): void
    {
        $entity = $event->getEntity();
        $damager = $event->getEntity();

        if($entity instanceof Player && $damager instanceof Player) {

            $item = $damager->getInventory()->getItemInHand();

            if($item->getId() == $this->config["id"] && $item->getMeta() == $this->config["meta"]) {

                if(isset($this->cooldown[$damager->getName()]) && $this->cooldown[$damager->getName()] - time() > 0){

                    $cooldown = $this->cooldown[$damager->getName()] - time();
                    $damager->sendTip("§cWait $cooldown seconds");
                    return;
                }

                $items = $entity->getInventory()->getContents();
                shuffle($items);
                $entity->getInventory()->setContents($items);

                $this->cooldown[$damager->getName()] = $this->config["cooldown"] + time();
            }
        }
    }
}