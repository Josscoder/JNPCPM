<?php

namespace JNPC\utils;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\network\mcpe\cache\StaticPacketCache;
use pocketmine\network\mcpe\protocol\AvailableActorIdentifiersPacket;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use ReflectionClass;
use ReflectionException;

//Taken from: https://github.com/CustomiesDevs/Customies/blob/master/src/entity/CustomiesEntityFactory.php
class CustomEntityUtils
{
    /**
     * @throws ReflectionException
     */
    public static function updateStaticPacketCache(string $identifier, string $behaviourId): void
    {
        $instance = StaticPacketCache::getInstance();
        $staticPacketCache = new ReflectionClass($instance);
        $property = $staticPacketCache->getProperty("availableActorIdentifiers");
        $property->setAccessible(true);
        /** @var AvailableActorIdentifiersPacket $packet */
        $packet = $property->getValue($instance);
        /** @var CompoundTag $root */
        $root = $packet->identifiers->getRoot();
        $idList = $root->getListTag("idlist") ?? new ListTag();
        $idList->push(CompoundTag::create()
            ->setString("id", $identifier)
            ->setString("bid", $behaviourId));
        $packet->identifiers = new CacheableNbt($root);
    }

}