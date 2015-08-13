<?php

namespace Sudoux\EagleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('SudouxEagleBundle:Default:index.html.twig', array('name' => $name));
    }
}
