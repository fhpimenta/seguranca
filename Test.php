<?php

require_once 'vendor/autoload.php';

use Harpia\Tree\Tree;

$tree = new Tree();
$tree->addValue('Acadêmico');
$tree->addLeaf('Relatórios');
$tree->addLeaf('Documentos');
$tree->addLeaf('Processos');

$otherTree = new Tree();

$cadastro = new Tree();

$cadastro->addLeaf('Educação');
$cadastro->addLeaf('Pessoas');

$institucional = new Tree();

$institucional->addLeaf('Polos');
$institucional->addLeaf('Centros');
$institucional->addLeaf('Departamentos');

$cadastro->addTree($institucional);

$tree->addTree($cadastro);

echo '<pre>' . var_export($tree, true) . '</pre>';