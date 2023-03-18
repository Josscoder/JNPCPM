<?php

namespace JNPC\listener;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerEntityInteractEvent;
use pocketmine\event\server\DataPacketReceiveEvent;

interface IClickAble
{
    public function onLeftClickEntity(PlayerEntityInteractEvent $event): void;

    public function onRightClickEntity(DataPacketReceiveEvent $event): void;
}