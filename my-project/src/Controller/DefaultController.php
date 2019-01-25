<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 21/01/2019
 * Time: 11:18
 */

namespace App\Controller;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class DefaultController extends AbstractController
{

    /**
     * @Route("/accueil", name="homepage")
     */
    public function indexAction(){
        return $this->render('default/accueil.html.twig');
    }
}