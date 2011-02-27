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
    
    protected $type;
    
    protected $revision;
    
    public function __construct($source, $target, $type, $revision)
    {
        $this->source = (string) $source;
        $this->target = (string) $target;
        $this->type = (string) $type;
        $this->revision = (string) $revision;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function getTarget()
    {
        return $this->target;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getRevision()
    {
        return $this->revision;
    }
}