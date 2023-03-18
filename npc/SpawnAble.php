<?php

namespace JNPC\npc;

use JNPC\settings\AttributeSettings;
use JNPC\settings\HumanAttributes;
use JNPC\utils\CustomEntityUtils;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\network\mcpe\convert\SkinAdapterSingleton;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\AddPlayerPacket;
use pocketmine\network\mcpe\protocol\MoveActorAbsolutePacket;
use pocketmine\network\mcpe\protocol\PlayerListPacket;
use pocketmine\network\mcpe\protocol\RemoveActorPacket;
use pocketmine\network\mcpe\protocol\SetActorDataPacket;
use pocketmine\network\mcpe\protocol\types\AbilitiesData;
use pocketmine\network\mcpe\protocol\types\command\CommandPermissions;
use pocketmine\network\mcpe\protocol\types\DeviceOS;
use pocketmine\network\mcpe\protocol\types\entity\ByteMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\network\mcpe\protocol\types\entity\FloatMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\IntMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\MetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;
use pocketmine\network\mcpe\protocol\types\entity\StringMetadataProperty;
use pocketmine\network\mcpe\protocol\types\GameMode;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStack;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStackWrapper;
use pocketmine\network\mcpe\protocol\types\PlayerListEntry;
use pocketmine\network\mcpe\protocol\types\PlayerPermissions;
use pocketmine\network\mcpe\protocol\UpdateAbilitiesPacket;
use pocketmine\player\Player;
use Ramsey\Uuid\Uuid;

abstract class SpawnAble implements ISpawnAble
{

    protected AttributeSettings $attributeSettings;
    protected ?HumanAttributes $humanSettings;

    /**
     * @var Player[]
     */
    protected array $viewerList = [];

    protected int $actorRID;

    /**
     * @var MetadataProperty[]
     * @phpstan-var array<int, MetadataProperty>
     */
    protected array $mergeMetadataList = [];

    public function __construct(AttributeSettings $attributeSettings, ?HumanAttributes $humanAttributes)
    {
        $this->attributeSettings = $attributeSettings;

        if (!$this->isHuman() && $attributeSettings->isCustomEntity()) {
            CustomEntityUtils::updateStaticPacketCache($attributeSettings->getNetworkId(), $attributeSettings->getBehaviorId());
        }

        $this->humanSettings = $humanAttributes;
        $this->actorRID = Entity::nextRuntimeId();
    }

    public function isHuman(): bool
    {
        return $this->attributeSettings->getNetworkId() === Human::getNetworkTypeId();
    }

    /**
     * @param MetadataProperty[] $metadata
     * @phpstan-param array<int, MetadataProperty> $metadata
     */
    public function mergeMetadata(array $metadata): void
    {
        $this->mergeMetadataList = $metadata;
    }

    /**
     * @param MetadataProperty[] $metadata
     * @phpstan-param array<int, MetadataProperty> $metadata
     */
    public function updateMetadata(array $metadata): void
    {
        foreach ($this->viewerList as $viewer) {
            if (!is_null($viewer)) {
                $packet = SetActorDataPacket::create($this->actorRID, $metadata, new PropertySyncData([], []), 0);
                $viewer->getNetworkSession()->sendDataPacket($packet);
            }
        }
    }

    public function show(Player $player): void
    {
        $metadata = [
            EntityMetadataProperties::NAMETAG => new StringMetadataProperty(''),
            EntityMetadataProperties::ALWAYS_SHOW_NAMETAG => new ByteMetadataProperty(1),
            EntityMetadataProperties::LEAD_HOLDER_EID => new IntMetadataProperty(-1),
            EntityMetadataProperties::SCALE => new FloatMetadataProperty($this->attributeSettings->getScale())
        ];
        foreach ($this->mergeMetadataList as $key => $value) {
            $metadata[$key] = $value;
        }

        $location = $this->attributeSettings->getLocation();

        if ($this->isHuman()) {
            $uuid = Uuid::uuid4();

            $playerListAddPacket = PlayerListPacket::add([
                PlayerListEntry::createAdditionEntry(
                    $uuid,
                    $this->actorRID,
                    $uuid->toString(),
                    SkinAdapterSingleton::get()->toSkinData($this->humanSettings->getSkin())
                )
            ]);
            $player->getNetworkSession()->sendDataPacket($playerListAddPacket);

            $packet = AddPlayerPacket::create(
                $uuid,
                '',
                $this->actorRID,
                '',
                $location->asVector3(),
                null,
                $location->getPitch(),
                $location->getYaw(),
                $location->getYaw(),
                new ItemStackWrapper(0, ItemStack::null()),
                GameMode::SURVIVAL,
                $metadata,
                new PropertySyncData([], []),
                UpdateAbilitiesPacket::create(new AbilitiesData(
                    CommandPermissions::NORMAL,
                    PlayerPermissions::MEMBER,
                    $this->actorRID,
                    []
                )),
                [],
                "",
                DeviceOS::UNKNOWN
            );
            $player->getNetworkSession()->sendDataPacket($packet);

            $playerListRemovePacket = PlayerListPacket::remove([
                PlayerListEntry::createRemovalEntry($uuid)
            ]);
            $player->getNetworkSession()->sendDataPacket($playerListRemovePacket);

        } else {
            $packet = AddActorPacket::create(
                $this->actorRID,
                $this->actorRID,
                $this->attributeSettings->getNetworkId(),
                $location->asVector3(),
                null,
                $location->getPitch(),
                $location->getYaw(),
                $location->getYaw(),
                0,
                [],
                $metadata,
                new PropertySyncData([], []),
                []
            );

            $player->getNetworkSession()->sendDataPacket($packet);
        }

        if (!array_keys($this->viewerList, $player, true)) {
            $this->viewerList[] = $player;
        }
    }

    public function move(Location $location): void
    {
        $this->attributeSettings->setLocation($location);

        $packet = MoveActorAbsolutePacket::create($this->actorRID,
            $location->asVector3(),
            $location->getPitch(),
            $location->getYaw(),
            $location->getYaw(),
            (
                MoveActorAbsolutePacket::FLAG_FORCE_MOVE_LOCAL_ENTITY
            )
        );

        foreach ($this->viewerList as $viewer) {
            if (!is_null($viewer)) {
                $viewer->getNetworkSession()->sendDataPacket($packet);
            }
        }
    }

    public function hide(Player $player): void
    {
        $packet = RemoveActorPacket::create($this->actorRID);
        $player->getNetworkSession()->sendDataPacket($packet);

        unset($this->viewerList[array_search($player, $this->viewerList, true)]);
    }

    public function getActorRID(): int
    {
        return $this->actorRID;
    }

    public function getAttributeSettings(): AttributeSettings
    {
        return $this->attributeSettings;
    }

    public function getHumanSettings(): HumanAttributes
    {
        return $this->humanSettings;
    }
}