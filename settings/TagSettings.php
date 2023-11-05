<?php

namespace JNPC\settings;

use JNPC\entity\line\Line;
use JNPC\entity\npc\NPC;
use pocketmine\entity\Location;

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

        $i = 0;
        foreach ($this->lines as $line) {
            $lineLoc = null;

            if ($i == 0) {
                $lineLoc = $location->add(0, $this->height, 0);
            } else {
                $lineLoc = $this->getLine($i - 1)->getAttributeSettings()->getLocation()->add(0, (self::ONE_BREAK_LINE * $line->getSeparator()), 0);
            }

            $line->move(Location::fromObject($lineLoc, $attributeSettings->getLocation()->getWorld()));

            ++$i;
        }
    }

    public function getLine(int $index): Line
    {
        return array_values($this->lines)[max(count($this->lines) - $index - 1, 0)];
    }

    public function adjust(): void
    {
        $this->lines = array_reverse($this->lines);

        $attributeSettings = $this->linkedNPC->getAttributeSettings();

        foreach ($this->lines as $index => $line) {
            $line->setLinkedNPC($this->linkedNPC);

            $location = null;

            if ($index === 0) {
                $location = $attributeSettings->getLocation()->add(0, $this->height, 0);
            } else {
                $previousLine = $this->lines[$index - 1];
                $location = $previousLine->getAttributeSettings()->getLocation()->add(0, (self::ONE_BREAK_LINE * $line->getSeparator()), 0);
            }

            $line->getAttributeSettings()->location(Location::fromObject($location, $attributeSettings->getLocation()->getWorld()));
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