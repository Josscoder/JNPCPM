<?php

namespace JNPC\factory;

use JNPC\npc\NPC;
use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\World;

class NPCFactory
{
    use SingletonTrait;

    /**
     * @var NPC[]
     */
    private array $npcList = [];

    public function __construct()
    {
        self::setInstance($this);
    }

    public static function make(): void
    {
        new NPCFactory();
    }

    /**
     * @return NPC[]
     */
    public function getNpcList(): array
    {
        return $this->npcList;
    }

    public function store(NPC $npc): void
    {
        $this->npcList[] = $npc;
    }

    /**
     * @param callable $callable
     * @return NPC[]
     */
    public function filter(callable $callable): array
    {
        return array_filter($this->npcList, $callable, ARRAY_FILTER_USE_KEY);
    }

    public function filterByWorld(World $world): array
    {
        return $this->filter(function ($npc) {
           //TODO: return $npc->getWorld() === $world;
        });
    }

    public function hideWorldNPCS(World $world, Player $player): void
    {
        foreach ($this->filterByWorld($world) as $npc) {
            //TODO: $npc->hide($player);
        }
    }

    public function showWorldNPCS(World $world, Player $player): void
    {
        foreach ($this->filterByWorld($world) as $npc) {
            //TODO: $npc->show($player);
        }
    }

    public function handleNPCController(int $entityId, Player $player): void
    {

    }
}