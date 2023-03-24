<?php

namespace JNPC\entity\line;

use JNPC\entity\npc\NPC;
use JNPC\entity\spawnable\SpawnAble;
use JNPC\settings\AttributeSettings;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

abstract class Line extends SpawnAble
{

    private int $separator;
    private NPC $linkedNPC;

    public function __construct(int $separator = 1)
    {
        parent::__construct(AttributeSettings::builder()
            ->networkId(EntityIds::CREEPER)
            ->boundingBoxWidth(0)
            ->scale(0.004),
            null
        );

        $this->separator = $separator;
    }

    public function getSeparator(): int
    {
        return $this->separator;
    }

    public function getLinkedNPC(): NPC
    {
        return $this->linkedNPC;
    }

    public function setLinkedNPC(NPC $linkedNPC): void
    {
        $this->linkedNPC = $linkedNPC;
    }
}