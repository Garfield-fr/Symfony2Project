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

        if (!is_array($path)) {
            $path = (array) $path;
        }

        $this->path = $path;
    }
    
    public function getNamespace()
    {
        return $this->ns;
    }
    
    public function get($maxspace = 0)
    {
        $space = $maxspace - mb_strlen($this->ns) + 2;
        $mask = '';

        if (count($this->path) == 1) {
            $mask = sprintf("__DIR__.'/../%s'", $this->path[0]);
        } else
        {
            foreach ($this->path as $path) {
                $mask .= sprintf("__DIR__.'/../%s', ", $path);
            }
            $mask = sprintf('array(%s)', substr($mask, 0, -2));
        }

        return sprintf("'%s'%s => %s", $this->ns, str_repeat(' ',$space),  $mask);
    }
}