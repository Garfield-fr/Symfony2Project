<?php

namespace Installer\Prefix;

/**
 * Prefix
 *
 * @author      Bertrand Zuchuat <bertrand.zuchuat@gmail.com>
 * @author      Clément Jobeili <clement.jobeili@gmail.com>
 */
class Prefix
{
    public function __construct($prefix, $path)
    {
        $this->prefix = $prefix;
        $this->path = $path;
    }
    
    public function get()
    {
        return sprintf("'%s_' => __DIR__.'/../%s'", $this->prefix,  $this->path);
    }
}