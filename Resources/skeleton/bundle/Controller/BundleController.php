<?php

namespace {{ namespace }}\{{ appname }}Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class {{ controller }}Controller extends Controller
{
    public function indexAction()
    {
        return $this->render('{{ appname }}Bundle:{{ controller }}:index.html.{{ template_engine }}');
    }
}
