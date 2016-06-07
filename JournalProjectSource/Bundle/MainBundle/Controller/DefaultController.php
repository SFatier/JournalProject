<?php

namespace Tribuca\Bundle\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('TribucaMainBundle:Default:index.html.twig', array('name' => $name));
    }
}
