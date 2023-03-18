<?php

namespace JNPC\npc;

use JNPC\settings\AttributeSettings;
use pocketmine\network\mcpe\protocol\types\entity\ByteMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\network\mcpe\protocol\types\entity\FloatMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\IntMetadataProperty;
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
        $attributeSettings = new AttributeSettings();
        $attributeSettings->setNetworkId(EntityIds::CREEPER);

        parent::__construct($attributeSettings, null);

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
            EntityMetadataProperties::ALWAYS_SHOW_NAMETAG => new ByteMetadataProperty(1),
            EntityMetadataProperties::LEAD_HOLDER_EID => new IntMetadataProperty(-1),
            EntityMetadataProperties::BOUNDING_BOX_WIDTH => new IntMetadataProperty(0),
            EntityMetadataProperties::SCALE => new FloatMetadataProperty(0.004)
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