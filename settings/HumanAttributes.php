<?php

namespace JNPC\settings;

use pocketmine\entity\Skin;
use pocketmine\item\Item;

class HumanAttributes
{

    private Skin $skin;

    private Item $handItem;

    public function getSkin(): Skin
    {
        return $this->skin;
    }

    public function skin(Skin $skin): self
    {
        $this->skin = $skin;
        return $this;
    }

    public function getHandItem(): Item
    {
        return $this->handItem;
    }

    public function handItem(Item $handItem): self
    {
        $this->handItem = $handItem;
        return $this;
    }
}