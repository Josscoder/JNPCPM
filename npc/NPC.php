<?php

namespace JNPC\npc;

use JNPC\factory\NPCFactory;
use JNPC\settings\AttributeSettings;
use JNPC\settings\HumanAttributes;
use JNPC\settings\TagSettings;
use pocketmine\entity\Location;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class NPC extends SpawnAble
{

    private TagSettings $tagSettings;

    public function __construct(AttributeSettings $attributeSettings, ?HumanAttributes $humanAttributes)
    {
        parent::__construct($attributeSettings, $humanAttributes);

        $this->tagSettings = new TagSettings();
        $this->tagSettings->setLinkedNPC($this);
    }

    public static function create(AttributeSettings $attributeSettings, ?HumanAttributes $humanAttributes = null, bool $store = true): NPC
    {
        $npc = new NPC($attributeSettings, $humanAttributes);

        if ($store) {
            NPCFactory::getInstance()->store($npc);
        }

        return $npc;
    }

    public function lookAt(Vector3 $vector, true $update): void
    {
        $location = $this->attributeSettings->getLocation();

        $xDist = ($vector->getX() - $location->getX());
        $zDist = ($vector->getZ() - $location->getZ());

        $location->yaw = atan2($zDist, $xDist) / M_PI * 180 - 90;

        if ($location->getYaw() < 0) {
            $location->yaw += 360;
        }

        if ($update) {
            $this->move($location);
        }
    }

    public function move(Location $location): void
    {
        $oldLocation = $this->attributeSettings->getLocation();

        parent::move($location);

        if ($location->getX() != $oldLocation->getX() || $location->getY() != $oldLocation->getY() || $location->getZ() != $oldLocation->getZ()) {
            $this->tagSettings->readjust($location);
        }
    }

    public function show(Player $player): void
    {
        parent::show($player);
        $this->spawnLines($player);
    }

    public function spawnLines(Player $player): void
    {
        foreach ($this->tagSettings->getLines() as $line) {
            $line->show($player);
        }
    }

    public function hide(Player $player): void
    {
        parent::hide($player);
        $this->hideLines($player);
    }

    public function hideLines(Player $player): void
    {
        foreach ($this->tagSettings->getLines() as $line) {
            $line->hide($player);
        }
    }

    public function reloadLines(): void
    {
        foreach ($this->viewerList as $viewer) {
            if (!is_null($viewer)) {
                $this->hideLines($viewer);
                $this->spawnLines($viewer);
            }
        }
    }

    public function getTagSettings(): TagSettings
    {
        return $this->tagSettings;
    }
}