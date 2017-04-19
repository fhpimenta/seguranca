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

    /**
     * @param null $data
     * @throws \ErrorException
     */
    public function addLeaf($data = null)
    {
        if($this->root == null){
            throw new \ErrorException("Cannot add leaf node as root node of tree");
        }

        $this->root->addChild(new Node($data, true));
    }

    /**
     * @param null $data
     */
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


    /**
     * @param Tree $tree
     */
    public function addTree(Tree $tree)
    {
        if($tree->getRoot()){
            $root = $tree->getRoot();
            $this->addValue($root);
            $this->nodes += $tree->getNodes();
            return;
        }

        $this->root = $tree->getRoot();
        $this->nodes = $tree->getNodes();
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