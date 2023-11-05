<?php

namespace JNPC\listener;

use pocketmine\event\Listener;

interface NPCListener extends Listener, ICleanUpAble, IClickAble, ILookAble
{

}