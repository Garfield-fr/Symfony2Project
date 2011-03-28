<?php

namespace {{ namespace }}\{{ appname }}Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class {{ controller }}Controller extends Controller
{
    public function indexAction()
    {
        return $this->render('{{ appname }}:{{ controller }}:index.html.{{ template_engine }}');
    }
    
    public function welcomeAction()
    {
        return $this->render('{{ appname }}:{{ controller }}:welcome.html.{{ template_engine }}');
    }
}
