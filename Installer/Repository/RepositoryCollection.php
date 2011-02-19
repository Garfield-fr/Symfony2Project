<?php

namespace Installer\Repository;
/**
 * RepositoryCollection
 *
 * @author      Bertrand Zuchuat <bertrand.zuchuat@gmail.com>
 * @author      Clément Jobeili <clement.jobeili@gmail.com>
 */
class RepositoryCollection
{
    protected $collection;
    
    public function add($repository)
    {
        $this->collection[] = $repository;
    }
    
    public function get()
    {
        return $this->collection;
    }
}