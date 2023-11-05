<?php

namespace JNPC\entity\spawnable;

use pocketmine\entity\Location;
use pocketmine\player\Player;

interface ISpawnAble
{
    public function show(Player $player): void;
    public function move(Location $location): void;
    public function hide(Player $player): void;
}