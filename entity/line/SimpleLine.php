<?php

namespace JNPC\entity\line;

use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\network\mcpe\protocol\types\entity\StringMetadataProperty;
use pocketmine\utils\TextFormat;

class SimpleLine extends Line
{
    private string $name;

    public function __construct(string $name, int $separator = 1)
    {
        parent::__construct($separator);

        $this->name = $name;
        $this->mergeMetadata([
            EntityMetadataProperties::NAMETAG => new StringMetadataProperty(TextFormat::colorize($name)),
        ]);
    }

    public function rename(string $name): void
    {
        $this->updateMetadata([
            EntityMetadataProperties::NAMETAG => new StringMetadataProperty(TextFormat::colorize($name)),
        ]);
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}