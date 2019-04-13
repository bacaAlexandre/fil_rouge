<?php

namespace App\Controller;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Unirest;

class DefaultController extends AbstractController
{

    /**
     * @Route("/accueil", name="homepage")
     */
    public function indexAction()
    {
        Unirest\Request::verifyPeer(false);
        $header = array('Accept' => 'application/json');
        $body = array(
            'api_key' => getenv('API_KEY'),
            'language' => getenv('API_LANG'));

        $reponse = Unirest\Request::get('https://api.themoviedb.org/3/trending/movie/day', $header, $body);
        dump($reponse->body->results);
        $data = array();
        for ($i = 0; $i < count($reponse->body->results); $i++) {
            $data[$i]['poster_path'] = $reponse->body->results[$i]->poster_path;
            $data[$i]['title'] = $reponse->body->results[$i]->title;
            $data[$i]['overview'] = $reponse->body->results[$i]->overview;
        }
        dump($data);
        return $this->render('default/accueil.html.twig', array('data' => $data));
    }
}