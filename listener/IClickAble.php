<?php

namespace JNPC\listener;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\server\DataPacketReceiveEvent;

interface IClickAble
{
    public function onLeftClickEntity(EntityDamageByEntityEvent $event): void;

    public function onRightClickEntity(DataPacketReceiveEvent $event): void;
}