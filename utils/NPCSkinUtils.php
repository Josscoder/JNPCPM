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
        ob_start();
        imagepng($image);
        $skinData = ob_get_clean();
        imagedestroy($image);

        $allowedSizes = Skin::ACCEPTED_SKIN_SIZES;
        $dataSize = strlen($skinData);
        if (!in_array($dataSize, $allowedSizes)) {
            $newSize = 0;
            foreach ($allowedSizes as $size) {
                if ($dataSize > $size && $newSize < $size) {
                    $newSize = $size;
                }
            }
            if ($newSize == 0) {
                throw new NPCException('Invalid Skin Data');
            } else {
                $skinData = str_pad(substr($skinData, 0, $newSize), $newSize, "\x00");
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