<?php

namespace Installer\Bundle;

/**
 * BundleCollection
 *
 * @author      Bertrand Zuchuat <bertrand.zuchuat@gmail.com>
 * @author      Clément Jobeili <clement.jobeili@gmail.com>
 */
class BundleCollection
{
    protected $collection;
    
    public function add($bundle)
    {
        $this->collection[] = $bundle;
    }

    public function getCollection()
    {
        return $this->collection;
    }

    public function getFormatted($space = 4)
    {
        $bundles = '';
        foreach ($this->collection as $bundle)
        {
            $bundles .= $bundle->get().",\n".str_repeat(' ', $space);
        }
        
        return trim($bundles);
    }
}