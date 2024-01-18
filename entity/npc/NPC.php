<?php

namespace JNPC\entity\npc;

use JNPC\entity\spawnable\SpawnAble;
use JNPC\factory\NPCFactory;
use JNPC\settings\AttributeSettings;
use JNPC\settings\HumanAttributes;
use JNPC\settings\TagSettings;
use pocketmine\entity\Location;
use pocketmine\math\Vector2;
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

    public function showToWorldPlayers(): void
    {
        $location = $this->attributeSettings->getLocation();
        if (!$location->isValid()) {
            return;
        }

        foreach ($location->getWorld()->getPlayers() as $player) {
            $this->show($player);
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

    public function lookAt(Vector3 $vector, true $update): void
    {
        $location = $this->lookVector($vector);

        if ($update) {
            $this->move($location);
        } else {
            $this->attributeSettings->location($location);
        }
    }

    public function lookVector(Vector3 $vector): Location
    {
        $location = $this->attributeSettings->getLocation();
        if (!$location->isValid()) {
            return $location;
        }

        $angle = atan2($vector->getZ() - $location->getZ(), $vector->getX() - $location->getX());
        $yaw = (($angle * 180) / M_PI) - 90;
        $angle = atan2((new Vector2($location->getX(), $location->getZ()))->distance(new Vector2($vector->getX(), $vector->getZ())), $vector->getY() - $location->getY());
        $pitch = (($angle * 180) / M_PI) - 90;

        $location->yaw = $yaw;
        $location->pitch = $pitch;

        return $location;
    }

    public function move(Location $location): void
    {
        $oldLocation = $this->attributeSettings->getLocation();
        if (!$oldLocation->isValid() || !$location->isValid()) {
            return;
        }

        parent::move($location);

        if ($location->getX() != $oldLocation->getX() || $location->getY() != $oldLocation->getY() || $location->getZ() != $oldLocation->getZ()) {
            $this->tagSettings->readjust($location);
        }
    }

    public function keepLooking(Player $player): void
    {
        $location = $this->lookVector($player->getLocation());
        $packet = $this->getMovePacket($location);
        $player->getNetworkSession()->sendDataPacket($packet);
    }

    public function reloadLines(): void
    {
        foreach ($this->getViewerList() as $viewer) {
            $this->hideLines($viewer);
            $this->spawnLines($viewer);
        }
    }

    public function hideLines(Player $player): void
    {
        foreach ($this->tagSettings->getLines() as $line) {
            $line->hide($player);
        }
    }

    public function hide(Player $player): void
    {
        parent::hide($player);
        $this->hideLines($player);
    }

    public function remove(): void
    {
        foreach ($this->getViewerList() as $viewer) {
            $this->hide($viewer);
        }

        NPCFactory::getInstance()->removeNPC($this->actorRID);
    }

    public function getTagSettings(): TagSettings
    {
        return $this->tagSettings;
    }
}