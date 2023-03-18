<?php

namespace JNPC\listener;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;

class DefaultNPCListener implements NPCListener
{

    public function onJoin(PlayerJoinEvent $event): void
    {
        // TODO: Implement onJoin() method.
    }

    public function onQuit(PlayerQuitEvent $event): void
    {
        // TODO: Implement onQuit() method.
    }

    public function onLevelChange(EntityTeleportEvent $event): void
    {
        // TODO: Implement onLevelChange() method.
    }

    public function onLeftClickEntity(EntityDamageByEntityEvent $event): void
    {
        // TODO: Implement onLeftClickEntity() method.
    }

    public function onRightClickEntity(DataPacketReceiveEvent $event): void
    {
        // TODO: Implement onRightClickEntity() method.
    }

    public function onKeepLookingEntity(PlayerMoveEvent $event): void
    {
        // TODO: Implement onKeepLookingEntity() method.
    }
}