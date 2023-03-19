<?php

namespace JNPC\utils;

use JNPC\exception\NPCException;
use JsonException;
use pocketmine\entity\Skin;

class NPCSkinUtils
{

    /**
     * @throws NPCException
     */
    public static function fromSkinPath(string $skinPath): Skin
    {
        $image = imagecreatefrompng($skinPath);
        $skinData = '';
        for ($y = 0; $y < imagesy($image); $y++) {
            for ($x = 0; $x < imagesx($image); $x++) {
                $argb = imagecolorat($image, $x, $y);
                $a = (~($argb >> 24)) & 0xff; // Alpha channel
                $r = ($argb >> 16) & 0xff; // Red channel
                $g = ($argb >> 8) & 0xff; // Green channel
                $b = $argb & 0xff; // Blue channel
                $skinData .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }

        try {
            $skin = new Skin("Standard_Custom", $skinData);
        } catch (JsonException $e) {
            throw new NPCException('Invalid Skin Data: ', $e->getMessage());
        }

        return $skin;
    }

    /**
     * @throws NPCException
     * @throws JsonException
     */
    public static function fromSkinPathAndGeometry(string $skinPath, string $geometryPath, string $geometryName): Skin
    {
        try {
            $skin = self::fromSkinPath($skinPath);
        } catch (NPCException $e) {
            throw new NPCException('Invalid Skin Data: ', $e->getMessage());
        }

        return new Skin(
            $skin->getSkinId(),
            $skin->getSkinData(),
            "",
            $geometryName,
            file_get_contents($geometryPath)
        );
    }

    /**
     * @throws NPCException
     */
    public static function invisibleSkin(): Skin
    {
        try {
            $skin = new Skin(
                'Standard_Custom',
                str_repeat("\x00", 8192),
                '',
                'geometry.humanoid.custom'
            );
        } catch (JsonException $e) {
            throw new NPCException('Invalid Skin Data: ', $e->getMessage());
        }

        return $skin;
    }
}