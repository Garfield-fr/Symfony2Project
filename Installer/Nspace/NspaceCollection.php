<?php

namespace Installer\Nspace;

/**
 * NamespaceCollection
 *
 * @author      Bertrand Zuchuat <bertrand.zuchuat@gmail.com>
 * @author      Clément Jobeili <clement.jobeili@gmail.com>
 */
class NspaceCollection
{
    protected $collection;
    
    protected $size;
    
    public function add($namespace)
    {
        $this->collection[] = $namespace;
        $this->calculateSize($namespace->getNamespace());
    }
    
    public function getFormatted($space = 4)
    {
        $namespaces = '';
        foreach ($this->collection as $namespace)
        {
            $namespaces .= $namespace->get($this->size).",\n".str_repeat(' ', $space);
        }
        
        return trim($namespaces);
    }
    
    private function calculateSize($name)
    {
        if ($this->size < mb_strlen($name)) {
            $this->size = mb_strlen($name);
        }
    }
}