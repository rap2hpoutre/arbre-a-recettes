<?php

namespace Rap2h\CookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('Rap2hCookingBundle:Default:index.html.twig', array('name' => $name));
    }
}
