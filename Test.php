<?php

require_once 'vendor/autoload.php';

use Harpia\Tree\Tree;

$tree = new Tree();
$tree->addValue(10);
$tree->addValue(45);

$otherTree = new Tree();
$otherTree->addValue(50);
$otherTree->addValue(65);

$tree->addTree($otherTree);

$otherTree->addValue(100);

echo '<pre>' . var_export($tree, true) . '</pre>';