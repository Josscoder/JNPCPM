<?php

namespace JNPC\settings;

use JNPC\entity\line\Line;
use JNPC\entity\npc\NPC;
use pocketmine\entity\Location;
use pocketmine\math\Vector3;

class TagSettings
{
    const ONE_BREAK_LINE = 0.32;

    /**
     * @var Line[]
     */
    private array $lines = [];
    private float $height = 1.8;
    private NPC $linkedNPC;

    public function addLine(Line $line): self
    {
        $this->lines[] = $line;
        return $this;
    }

    public function getHeight(): float
    {
        return $this->height;
    }

    public function height(float $height): self
    {
        $this->height = $height;
        return $this;
    }

    public function readjust(Location $location): void
    {
        $attributeSettings = $this->linkedNPC->getAttributeSettings();
        $npcLocation = $attributeSettings->getLocation();
        if (is_null($npcLocation)) {
            return;
        }

        $i = 0;
        foreach ($this->lines as $line) {
            $lineLocation = $this->iterateLocation($i, $location, $line);

            $line->move(Location::fromObject($lineLocation, $npcLocation->getWorld()));

            ++$i;
        }
    }

    public function getLine(int $index): Line
    {
        return array_values($this->lines)[max(count($this->lines) - $index - 1, 0)];
    }

    public function adjust(): void
    {
        $attributeSettings = $this->linkedNPC->getAttributeSettings();
        $npcLocation = $attributeSettings->getLocation();
        if (is_null($npcLocation)) {
            return;
        }

        $this->lines = array_reverse($this->lines);

        $i = 0;
        foreach ($this->lines as $line) {
            $line->setLinkedNPC($this->linkedNPC);

            $location = $this->iterateLocation($i, $npcLocation, $line);

            $line->getAttributeSettings()->location(Location::fromObject($location, $npcLocation->getWorld()));

            ++$i;
        }
    }

    public function setLinkedNPC(NPC $linkedNPC): void
    {
        $this->linkedNPC = $linkedNPC;
    }

    /**
     * @return Line[]
     */
    public function getLines(): array
    {
        return $this->lines;
    }

    /**
     * @param int $i
     * @param Location $npcLocation
     * @param Line $line
     * @return Vector3|null
     */
    private function iterateLocation(int $i, Location $npcLocation, Line $line): ?Vector3
    {
        $location = null;

        if ($i == 0) {
            $location = $npcLocation->add(0, $this->height, 0);
        } else {
            $lastLineLocation = $this->getLine($i - 1)->getAttributeSettings()->getLocation();
            if (!is_null($lastLineLocation)) {
                $location = $lastLineLocation->add(0, (self::ONE_BREAK_LINE * $line->getSeparator()), 0);
            }
        }
        return $location;
    }
}