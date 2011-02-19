<?php

namespace Installer\Prefix;

/**
 * PrefixCollection
 *
 * @author      Bertrand Zuchuat <bertrand.zuchuat@gmail.com>
 * @author      Clément Jobeili <clement.jobeili@gmail.com>
 */
class PrefixCollection
{
    protected $collection;
    
    public function add($prefix)
    {
        $this->collection[] = $prefix;
    }
    
    public function getFormatted($space = 4)
    {
        $prefixes = '';
        foreach ($this->collection as $prefix)
        {
            $prefixes .= $prefix->get().",\n".str_repeat(' ', $space);
        }
        
        return trim($prefixes);
    }
}