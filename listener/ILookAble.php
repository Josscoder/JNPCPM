<?php

namespace JNPC\listener;

use pocketmine\event\player\PlayerMoveEvent;

interface ILookAble
{
    public function onKeepLookingEntity(PlayerMoveEvent $event): void;
}