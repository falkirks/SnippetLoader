<?php
namespace snippetloader;

use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginLoadOrder;
class SnippetLoader extends PluginBase{
    public function onEnable(){
        if($this->getServer()->getPluginManager()->registerInterface("snippetloader\\SnippetInterface") !== false){
            $this->getLogger()->info("Ready!");
            $this->getServer()->getPluginManager()->loadPlugins($this->getServer()->getPluginPath(), array("snippetloader\\SnippetInterface"));
            $this->getServer()->enablePlugins(PluginLoadOrder::STARTUP);
        }
        else $this->getLogger()->info("Could not register.");

    }
}