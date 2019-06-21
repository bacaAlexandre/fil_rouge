<?php


namespace App\Controller;

use Sonata\UserBundle\Entity\BaseUser;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class CompteController
 * @Route("monCompte")
 */
class CompteController extends AbstractController
{
    /**
     * @Route("/information", name="monCompte_information")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository("App\\Application\\Sonata\\UserBundle\\Entity\\User")->find($this->getUser()->getId());

        return $this->render('monCompte/information.html.twig', array('user' => $user));
    }
}