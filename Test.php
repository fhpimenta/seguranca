<?php

require_once 'vendor/autoload.php';

use Harpia\Menu\MenuTree;

$menu = new MenuTree();

$menu->addValue(new \Harpia\Menu\MenuItem('Acadêmico'));

echo '<pre>' . var_export($menu, true) . '</pre>';