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
        $this->prefix = (string) $prefix;
        $this->path = (string) $path;
    }
    
    public function getPrefix()
    {
        return $this->prefix;
    }
    
    public function get($maxspace)
    {
        $space = $maxspace - mb_strlen($this->prefix) + 2;
        return sprintf("'%s_'%s => __DIR__.'/../%s'", $this->prefix, str_repeat(' ', $space), $this->path);
    }
}