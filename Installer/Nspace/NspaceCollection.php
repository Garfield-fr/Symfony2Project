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
    
    public function add($namespace)
    {
        $this->collection[] = $namespace;
    }
    
    public function getFormatted($space = 4)
    {
        $namespaces = '';
        foreach ($this->collection as $namespace)
        {
            $namespaces .= $namespace->get().",\n".str_repeat(' ', $space);
        }
        
        return trim($namespaces);
    }
}