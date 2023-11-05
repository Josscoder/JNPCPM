<?php

namespace JNPC;

use JNPC\factory\NPCFactory;
use JNPC\listener\DefaultNPCListener;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;

class JNPC
{
    public static function init(PluginBase $pluginBase = null, Listener $NPCListener = new DefaultNPCListener()): void
    {
        NPCFactory::make();

        if (is_null($pluginBase)) {
            return;
        }

        $pluginBase->getServer()->getPluginManager()->registerEvents($NPCListener, $pluginBase);
    }
}