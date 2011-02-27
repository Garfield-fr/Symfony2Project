<?php

namespace Installer\Nspace;

/**
 * Namespace
 *
 * @author      Bertrand Zuchuat <bertrand.zuchuat@gmail.com>
 * @author      Clément Jobeili <clement.jobeili@gmail.com>
 */
class Nspace
{
    protected $ns;
    
    protected $path;
    
    public function __construct($ns, $path)
    {
        $this->ns = (string) str_replace('\\', '\\\\', $ns);
        $this->path = (string) $path;
    }
    
    public function getNamespace()
    {
        return $this->ns;
    }
    
    public function get($maxspace = 0)
    {
        $space = $maxspace - mb_strlen($this->ns) + 2;
        return sprintf("'%s'%s => __DIR__.'/../%s'", $this->ns, str_repeat(' ',$space),  $this->path);
    }
}