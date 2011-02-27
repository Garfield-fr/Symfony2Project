<?php

namespace Installer\Bundle;

/**
 * Bundle
 *
 * @author      Bertrand Zuchuat <bertrand.zuchuat@gmail.com>
 * @author      Clément Jobeili <clement.jobeili@gmail.com>
 */
class Bundle
{
    protected $name;
    
    protected $ns;
    
    public function __construct($name, $ns = null)
    {
        $this->name = (string) $name;
        $this->ns = (string) $ns;
    }

    public function get()
    {
        $namespace = $this->generateNamespace($this->name);
        
        return sprintf('new %s\%sBundle()', $namespace, $this->name);
    }

    private function generateNamespace($name)
    {
        return (!$this->ns) ? sprintf('Symfony\Bundle\%sBundle', $name) : $this->ns;
    }
}