<?php

namespace JNPC\npc;

use JNPC\settings\AttributeSettings;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\network\mcpe\protocol\types\entity\StringMetadataProperty;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class Line extends SpawnAble
{

    private string $name;
    private int $separator;

    private NPC $linkedNPC;

    public function __construct(string $name, int $separator = 1)
    {
        parent::__construct(AttributeSettings::builder()
            ->networkId(EntityIds::CREEPER)
            ->boundingBoxWidth(0)
            ->scale(0.004),
            null
        );

        $this->name = $name;
        $this->separator = $separator;
    }

    public function rename(string $name): void
    {
        $this->updateMetadata([
            EntityMetadataProperties::NAMETAG => new StringMetadataProperty(TextFormat::colorize($name)),
        ]);
    }

    public function show(Player $player): void
    {
        $this->mergeMetadata([
            EntityMetadataProperties::NAMETAG => new StringMetadataProperty(TextFormat::colorize($this->name)),
        ]);

        parent::show($player);
    }

    public function getName(): string
    {
        return $this->name;
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