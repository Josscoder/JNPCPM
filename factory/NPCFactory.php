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

    public function hideWorldNPCS(World $world, Player $player): void
    {
        foreach ($this->filterByWorld($world) as $npc) {
            $npc->hide($player);
        }
    }

    public function filterByWorld(World $world): array
    {
        return $this->filter(function (int $index, NPC $npc) use ($world) {
            return $npc->getAttributeSettings()->getLocation()->getWorld() === $world;
        });
    }

    /**
     * @param callable $callable
     * @return NPC[]
     */
    public function filter(callable $callable): array
    {
        return array_filter($this->npcList, $callable, ARRAY_FILTER_USE_BOTH);
    }

    public function showWorldNPCS(World $world, Player $player): void
    {
        foreach ($this->filterByWorld($world) as $npc) {
            $npc->show($player);
        }
    }

    public function handleNPCController(int $actorRID, Player $player): void
    {
        $npcs = $this->filter(function (int $index, NPC $npc) use ($actorRID) {
            return $npc->getActorRID() === $actorRID;
        });

        if (empty($npcs)) {
            return;
        }

        /** @var NPC $npcToHandle */
        $npcToHandle = array_key_first($npcs);
        if (is_null($npcToHandle)) {
            return;
        }

        $controller = $npcToHandle->getAttributeSettings()->getController();
        if (is_null($controller)) {
            return;
        }

        $controller($npcToHandle, $player);
    }
}