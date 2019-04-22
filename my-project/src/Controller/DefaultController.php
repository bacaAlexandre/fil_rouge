<?php

namespace App\Controller;


use App\Service\Convertisseur;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Unirest;

class DefaultController extends AbstractController
{

    private $convertisseur;

    public function __construct(Convertisseur $convertisseur)
    {
        $this->convertisseur = $convertisseur;
    }

    /**
     * @Route("", name="homepage")
     */
    public function index()
    {
        Unirest\Request::verifyPeer(false);
        $header = array('Accept' => 'application/json');
        $body = array(
            'api_key' => getenv('API_KEY'),
            'language' => getenv('API_LANG'));

        $reponse = Unirest\Request::get('https://api.themoviedb.org/3/trending/movie/day', $header, $body);
        $data = array();

        for ($i = 0; $i < count($reponse->body->results); $i++) {
            $data[$i]['poster_path'] = $reponse->body->results[$i]->poster_path;
            $data[$i]['title'] = $reponse->body->results[$i]->title;
            $data[$i]['overview'] = $reponse->body->results[$i]->overview;
            $data[$i]['id'] = $reponse->body->results[$i]->id;
        }
        return $this->render('default/accueil.html.twig', array('data' => $data));
    }

    /**
     * @Route ("/film/{id}", name="page_film")
     */
    public function film(int $id)
    {
        Unirest\Request::verifyPeer(false);
        $header = array('Accept' => 'application/json');
        $body = array(
            'api_key' => getenv('API_KEY'),
            'language' => getenv('API_LANG'),
            'append_to_response' => "videos,images",
            'include_image_language' => "fr,null",
            );

        $reponse = Unirest\Request::get('https://api.themoviedb.org/3/movie/'.$id, $header, $body);

        $data['poster_path'] = $reponse->body->poster_path;
        $data['title'] = $reponse->body->title;
        $data['release_date'] = $this->convertisseur->jourENtoFR($reponse->body->release_date);
        $data['runtime'] =  $this->convertisseur->decimalToHoursMin($reponse->body->runtime);
        $data['tagline'] = $reponse->body->tagline;
        $data['overview'] = $reponse->body->overview;
        $data['genres'] = $reponse->body->genres;



        return $this->render('default/film.html.twig', array('data' => $data));
    }
}