<?php

namespace JNPC\listener;

use pocketmine\event\server\DataPacketReceiveEvent;

interface IClickAble
{
    public function onClickEntity(DataPacketReceiveEvent $event): void;
}