<?php

namespace JNPC\entity\line;

use Closure;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\network\mcpe\protocol\types\entity\StringMetadataProperty;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class PlaceholderLine extends Line
{
    private Closure $placeholder;

    public function __construct(Closure $placeholder, int $separator = 1)
    {
        parent::__construct($separator);
        $this->placeholder = $placeholder;
    }

    public function rename(Closure $placeholder): void
    {
        $this->placeholder = $placeholder;

        foreach ($this->getViewerList() as $viewer) {
            $this->render($viewer);
        }
    }

    public function render(Player $player): void
    {
        $placeholder = $this->placeholder;
        $output = $placeholder($player);

        $this->updateMetadataForPlayer([
            EntityMetadataProperties::NAMETAG => new StringMetadataProperty(TextFormat::colorize($output))
        ], $player);
    }

    public function show(Player $player): void
    {
        parent::show($player);
        $this->render($player);
    }
}