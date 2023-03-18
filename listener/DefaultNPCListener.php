<?php

namespace JNPC\listener;

use JNPC\factory\NPCFactory;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\player\PlayerEntityInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;
use pocketmine\player\Player;

class DefaultNPCListener implements NPCListener
{

    public function onJoin(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();
        NPCFactory::getInstance()->showWorldNPCS($player->getWorld(), $player);
    }

    public function onQuit(PlayerQuitEvent $event): void
    {
        $player = $event->getPlayer();
        NPCFactory::getInstance()->hideWorldNPCS($player->getWorld(), $player);
    }

    public function onLevelChange(EntityTeleportEvent $event): void
    {
        $from = $event->getFrom();
        $to = $event->getTo();

        if ($from->getWorld() === $to->getWorld()) {
            return;
        }

        $entity = $event->getEntity();
        if (!($entity instanceof Player)) {
            return;
        }

        NPCFactory::getInstance()->hideWorldNPCS($from->getWorld(), $entity);
        NPCFactory::getInstance()->showWorldNPCS($to->getWorld(), $entity);
    }

    public function onClickEntity(DataPacketReceiveEvent $event): void
    {
        $packet = $event->getPacket();
        if (!($packet instanceof InventoryTransactionPacket)) {
            return;
        }

        if ($packet->requestId != InventoryTransactionPacket::TYPE_NORMAL) {
            return;
        }

        $data = $packet->trData;
        if (!($data instanceof UseItemOnEntityTransactionData)) {
            return;
        }

        NPCFactory::getInstance()->handleNPCController($data->getActorRuntimeId(), $event->getOrigin()->getPlayer());
    }

    public function onKeepLookingEntity(PlayerMoveEvent $event): void
    {
        $player = $event->getPlayer();
        $location = $event->getTo();

        $worldNPCS = NPCFactory::getInstance()->filterByWorld($location->getWorld());

        foreach ($worldNPCS as $npc) {
            if (!$npc->getAttributeSettings()->isKeepLooking()) {
                continue;
            }

            $npc->lookAt($player->getLocation()->asVector3(), true);
        }
    }
}