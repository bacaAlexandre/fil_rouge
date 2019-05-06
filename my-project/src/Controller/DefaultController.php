<?php

namespace App\Controller;


use App\Service\Convertisseur;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

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
    public function index(string $message = null)
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

        if($message != null){
            $this->get('session')->getFlashBag()->set('contact', $message);
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

        $reponse = Unirest\Request::get('https://api.themoviedb.org/3/movie/' . $id, $header, $body);

        $data['poster_path'] = $reponse->body->poster_path;
        $data['title'] = $reponse->body->title;
        $data['release_date'] = $this->convertisseur->jourENtoFR($reponse->body->release_date);
        $data['runtime'] = $this->convertisseur->decimalToHoursMin($reponse->body->runtime);
        $data['tagline'] = $reponse->body->tagline;
        $data['overview'] = $reponse->body->overview;
        $data['genres'] = $reponse->body->genres;
        $data['path_video'] = "";
        foreach ($reponse->body->videos->results as $result) {

            if ($result->site == "YouTube") {
                $data['path_video'] = "https://www.youtube.com/embed/" . $result->key;
                break;
            }
        }
        return $this->render('default/film.html.twig', array('data' => $data));
    }

    /**
     * @Route("/contact", name="contact_admin")
     */
    public function contact()
    {
        return $this->render('default/contact.html.twig');
    }

    /**
     * @Route("/contact/envoyer", name="envoyer_contact_admin")
     */
    public function sendContact(Request $request,\Swift_Mailer $mailer)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("App\\Application\\Sonata\\UserBundle\\Entity\\User")->find($this->getUser()->getId());

        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('netflex.contact@mail.fr')
            ->setTo('alexandre.baca@viacesi.fr')
            ->setBody(
                $this->renderView('default/contacter.mail.html.twig',['commentaire' => $request->request->get('commentaire'),'user' => $user]),
                'text/html'
            );
        $mailer->send($message);

        return $this->index('Le message a bien été envoyer.');
    }
}