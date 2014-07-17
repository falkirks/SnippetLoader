<?php

namespace snippetloader;


use pocketmine\event\plugin\PluginDisableEvent;
use pocketmine\event\plugin\PluginEnableEvent;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginDescription;
use pocketmine\plugin\PluginLoader;
use pocketmine\Server;
use pocketmine\utils\MainLogger;

class SnippetInterface implements PluginLoader {
    private $server;
    public function __construct(Server $server){
        $this->server = $server;
    }
    /**
     * Loads the plugin contained in $file
     *
     * @param string $file
     *
     * @return Plugin
     */
    public function loadPlugin($file){
        if(($description = $this->getPluginDescription($file)) instanceof PluginDescription){
            MainLogger::getLogger()->info("Loading snippet plugin" . $description->getFullName());
            $dataFolder = dirname($file) . DIRECTORY_SEPARATOR . $description->getName();
            if(file_exists($dataFolder) and !is_dir($dataFolder)){
                throw new \Exception("Projected dataFolder '" . $dataFolder . "' for " . $description->getName() . " exists and is not a directory");
            }
            $className = $description->getMain();
            require($file);
            if(class_exists($className, true)){
                $plugin = new $className();
                $this->initPlugin($plugin, $description, $dataFolder, $file);
                return $plugin;
            }
            else{
                throw new \Exception("Couldn't load plugin " . $description->getName() . ": main class not found");
            }
        }

        return null;
    }

    /**
     * Gets the PluginDescription from the file
     *
     * @param string $file
     *
     * @return PluginDescription
     */
    public function getPluginDescription($file){
        $file = file_get_contents($file);
        preg_match_all("/((?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:\/\/.*))/", $file, $m);
        if(isset($m[0][0])){
            var_dump(\yaml_parse(substr($m[0][0], 2, -2)));
            return new PluginDescription(substr($m[0][0], 2, -2));
        }
        return null;
    }

    /**
     * Returns the filename patterns that this loader accepts
     *
     * @return string[]
     */
    public function getPluginFilters(){
        return "/\\.php$/i";
    }

    /**
     * @param PluginBase        $plugin
     * @param PluginDescription $description
     * @param string            $dataFolder
     * @param string            $file
     */
    private function initPlugin(PluginBase $plugin, PluginDescription $description, $dataFolder, $file){
        $plugin->init($this, $this->server, $description, $dataFolder, $file);
        $plugin->onLoad();
    }
    /**
     * @param Plugin $plugin
     *
     * @return void
     */
    public function enablePlugin(Plugin $plugin){
        if($plugin instanceof PluginBase and !$plugin->isEnabled()){
            MainLogger::getLogger()->info("Enabling " . $plugin->getDescription()->getFullName());
            $plugin->setEnabled(true);
            Server::getInstance()->getPluginManager()->callEvent(new PluginEnableEvent($plugin));
        }
    }

    /**
     * @param Plugin $plugin
     *
     * @return void
     */
    public function disablePlugin(Plugin $plugin){
        if($plugin instanceof PluginBase and $plugin->isEnabled()){
            MainLogger::getLogger()->info("Disabling " . $plugin->getDescription()->getFullName());
            Server::getInstance()->getPluginManager()->callEvent(new PluginDisableEvent($plugin));
            $plugin->setEnabled(false);
        }
    }
}