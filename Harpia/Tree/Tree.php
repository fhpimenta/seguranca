<?php

namespace Harpia\Tree;

use Harpia\Tree\Node;

class Tree
{
    private $root;

    private $nodes;


    public function __construct()
    {
        $this->nodes = 0;
        $this->root = new Node(null, false);
    }

    public function addLeaf($data = null)
    {
        $this->root->addChild(new Node($data, true));
    }

    public function addValue($data = null)
    {
        if($this->root == null){
            $node = new Node($data, false);

            $this->root = $node;
            $this->nodes++;
            return;
        }

        $this->root->addChild(new Node($data));
        $this->nodes++;
    }


    public function addTree(Tree $tree)
    {
        if($tree->getRoot()){
            $root = $tree->getRoot();
            $this->addValue($root);
            $this->nodes += $tree->getNodes();
            $this->nodes--;
        }
    }

    /**
     * @return null
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @return int
     */
    public function getNodes()
    {
        return $this->nodes;
    }
}