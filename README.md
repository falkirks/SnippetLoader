SnippetLoader
=============

This is a super simple plugin interface which will load "snippets" (single file plugins). Simple example below.

```php
<?php
/*name: TestSnip
main: snip\MainClass
version: 0.1
author: Falk
api: [1.0.0]
load: POSTWORLD*/
namespace snip;
use pocketmine\plugin\PluginBase;
class MainClass extends PluginBase
{	
	public function onEnable(){
		$this->getLogger()->info("Loaded.");
	}
}
```
