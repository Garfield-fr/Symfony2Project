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
    
    protected $size;
    
    public function add($prefix)
    {
        $this->collection[] = $prefix;
        $this->calculateSize($prefix->getPrefix());
    }
    
    public function getFormatted($space = 4)
    {
        $prefixes = '';
        foreach ($this->collection as $prefix)
        {
            $prefixes .= $prefix->get($this->size).",\n".str_repeat(' ', $space);
        }

        return trim($prefixes);
    }

    private function calculateSize($name)
    {
        if ($this->size < mb_strlen($name)) {
            $this->size = mb_strlen($name);
        }
    }
}