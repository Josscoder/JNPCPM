<?php

namespace JNPC\settings;

use JNPC\npc\Line;
use JNPC\npc\NPC;
use pocketmine\entity\Location;

class TagSettings
{
    const ONE_BREAK_LINE = 0.32;

    /**
     * @var Line[]
     */
    private array $lines = [];

    private NPC $linkedNPC;

    public function addLine(Line $line): self
    {
        $this->lines[] = $line;
        return $this;
    }

    public function readjust(Location $location): void
    {
        $attributeSettings = $this->linkedNPC->getAttributeSettings();

        $i = 0;
        foreach ($this->lines as $line) {
            $lineLoc = null;

            if ($i == 0) {
                $lineLoc = $location->add(0, $attributeSettings->getBoundingBoxHeight(), 0);
            } else {
                $lineLoc = $this->getLine($i - 1)->getAttributeSettings()->getLocation()->add(0, (self::ONE_BREAK_LINE * $line->getSeparator()), 0);
            }

            $line->move(Location::fromObject($lineLoc, $attributeSettings->getLocation()->getWorld()));

            ++$i;
        }
    }

    public function getLine(int $index): Line
    {
        return array_values($this->lines)[$index];
    }

    public function adjust(): void
    {
        $this->lines = array_reverse($this->lines);

        $attributeSettings = $this->linkedNPC->getAttributeSettings();

        $i = 0;
        foreach ($this->lines as $line) {
            $line->setLinkedNPC($this->linkedNPC);

            $location = null;

            if ($i == 0) {
                $location = $attributeSettings->getLocation()->add(0, $this->linkedNPC->getAttributeSettings()->getBoundingBoxHeight(), 0);
            } else {
                $location = $this->getLine($i - 1)->getAttributeSettings()->getLocation()->add(0, (self::ONE_BREAK_LINE * $line->getSeparator()), 0);
            }

            $line->getAttributeSettings()->setLocation(Location::fromObject($location, $attributeSettings->getLocation()->getWorld()));

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
}