<?php

namespace JNPC;

use JNPC\factory\NPCFactory;
use JNPC\listener\DefaultNPCListener;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;

class JNPC
{

    public static function init(PluginBase $pluginBase, Listener $NPCListener = new DefaultNPCListener()): void
    {
        NPCFactory::make();
        $pluginBase->getServer()->getPluginManager()->registerEvents($NPCListener, $pluginBase);
    }
}