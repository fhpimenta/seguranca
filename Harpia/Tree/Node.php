<?php

namespace Harpia\Tree;

class Node
{
    protected $childs;

    protected $father;
    protected $isLeaf;

    protected $data;

    public function __construct($data = null, $isLeaf = true)
    {
        $this->data = $data;
        $this->childs = [];
        $this->isLeaf = $isLeaf;
    }

    /**
     * @param Node $node
     * @throws \ErrorException
     */
    public function addChild(Node $node)
    {
        if ($this->isLeaf()) {
            throw new \ErrorException("Trying add child on a leaf node");
        }

        $node->setFather($this);
        $this->childs[] = $node;
    }

    /**
     * @param mixed $father
     */
    public function setFather(Node &$father)
    {
        $this->father = $father;
    }

    /**
     * @return mixed
     */
    public function getFather()
    {
        return $this->father;
    }

    /**
     * @return bool
     */
    public function isLeaf()
    {
        return $this->isLeaf;
    }

    /**
     * @return null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param null $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    public function setAsLeaf()
    {
        $this->isLeaf = true;
    }

    /**
     * @return bool
     */
    public function hasChildren()
    {
        if (count($this->childs) > 0) {
            return true;
        }

        return false;
    }

    /**
     * @return int
     */
    public function howManyChildren()
    {
        return count($this->childs);
    }

    /**
     * @return array
     */
    public function getChilds()
    {
        return $this->childs;
    }
}
