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
        $this->ns = $ns;
        $this->path = $path;
    }
    
    public function get()
    {
        return sprintf("'%s' => __DIR__.'/../%s'", str_replace('\\', '\\\\', $this->ns),  $this->path);
    }
}