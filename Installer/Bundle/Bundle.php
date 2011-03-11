<?php

namespace Installer\Bundle;

/**
 * Bundle
 *
 * @author      Bertrand Zuchuat <bertrand.zuchuat@gmail.com>
 * @author      Clément Jobeili <clement.jobeili@gmail.com>
 */
class Bundle
{
    protected $name;

    protected $ns;

    protected $config;

    public function __construct($name, $ns = null, $config = null)
    {
        $this->name = (string) $name;
        $this->ns = $ns;
        $this->config = $config;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getNs()
    {
        return $this->ns;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function get()
    {
        $namespace = $this->generateNamespace($this->name);

        return sprintf('new %s\%sBundle()', $namespace, $this->name);
    }

    private function generateNamespace($name)
    {
        return (!$this->ns) ? sprintf('Symfony\Bundle\%sBundle', $name) : $this->ns;
    }
}