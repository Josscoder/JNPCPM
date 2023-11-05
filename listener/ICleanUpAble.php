<?php

namespace JNPC\listener;

use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;

interface ICleanUpAble
{
    public function onJoin(PlayerJoinEvent $event): void;
    public function onQuit(PlayerQuitEvent $event): void;
    public function onLevelChange(EntityTeleportEvent $event): void;
}