<?php

namespace JNPC\factory;

use JNPC\entity\npc\NPC;
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
        $this->npcList[$npc->getActorRID()] = $npc;
    }

    public function hideWorldNPCS(World $world, Player $player): void
    {
        foreach ($this->filterByWorld($world) as $npc) {
            $npc->hide($player);
        }
    }

    public function filterByWorld(World $world): array
    {
        return $this->filter(function ($npc) use ($world) {
            return $npc->getAttributeSettings()->getLocation()->getWorld() === $world;
        });
    }

    /**
     * @param callable $callable
     * @return NPC[]
     */
    public function filter(callable $callable): array
    {
        return array_filter($this->npcList, $callable);
    }

    public function showWorldNPCS(World $world, Player $player): void
    {
        foreach ($this->filterByWorld($world) as $npc) {
            $npc->show($player);
        }
    }

    public function handleNPCController(int $actorRID, Player $player): void
    {
        if (!isset($this->npcList[$actorRID])) {
            return;
        }

        $npc = $this->npcList[$actorRID];
        if (is_null($npc)) {
            return;
        }

        $controller = $npc->getAttributeSettings()->getController();
        if (is_null($controller)) {
            return;
        }

        $controller($npc, $player);
    }

    public function removeNPC(int $actorRID): void
    {
        unset($this->npcList[$actorRID]);
    }
}