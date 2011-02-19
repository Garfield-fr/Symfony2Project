<?php

namespace Installer\Repository;
/**
 * Repository
 *
 * @author      Bertrand Zuchuat <bertrand.zuchuat@gmail.com>
 * @author      Clément Jobeili <clement.jobeili@gmail.com>
 */
class Repository
{
    protected $source;
    
    protected $target;
    
    public function __construct($source, $target)
    {
        $this->source = $source;
        $this->target = $target;
    }
    
    public function getSource()
    {
        return $this->source;
    }
    
    public function getTarget()
    {
        return $this->target;
    }
}