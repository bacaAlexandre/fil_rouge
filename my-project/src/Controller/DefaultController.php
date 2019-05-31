<?php

namespace App\Controller;

use App\Entity\Favoris;
use App\Entity\Films;
use App\Entity\Notes;
use App\Entity\Commentaires;


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

        if ($message != null) {
            $this->get('session')->getFlashBag()->set('contact', $message);
        }

        return $this->render('default/accueil.html.twig', array('data' => $data));
    }

    /**
     * @Route ("/film/{id}", name="page_film")
     */
    public function film(int $id)
    {
        return $this->render('default/film.html.twig', array('data' => $this->dataFilm($id)));
    }

    private function dataFilm(int $id)
    {
        $em = $this->getDoctrine()->getManager();
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
        $data['id'] = $reponse->body->id;
        $data['path_video'] = "";
        foreach ($reponse->body->videos->results as $result) {

            if ($result->site == "YouTube") {
                $data['path_video'] = "https://www.youtube.com/embed/" . $result->key;
                break;
            }
        }
        $noteMoyenne = $em->getRepository(Notes::class)->findAverageById($id);
        $data['noteMoyenne'] = $noteMoyenne['Moyenne'];
        $film = $this->checkFilm($data['id'], $data['title']);
        $data['commentaires'] = $film->getCommentaires();
        if ($this->getUser() != null) {
            if ($film != null) {
                $data['note'] = $em->getRepository(Notes::class)->findOneBy(array("user" => $this->getUser()->getId(), "film" => $film->getId()));
                $data['favoris'] = $em->getRepository(Favoris::class)->findOneBy(array("user" => $this->getUser()->getId(), "film" => $film->getId()));
            } else {
                $data['note'] = null;
            }
        }
        return $data;
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
    public function sendContact(Request $request, \Swift_Mailer $mailer)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("App\\Application\\Sonata\\UserBundle\\Entity\\User")->find($this->getUser()->getId());

        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('netflex.contact@mail.fr')
            ->setTo('alexandre.baca@viacesi.fr')
            ->setBody(
                $this->renderView('default/contacter.mail.html.twig', ['commentaire' => $request->request->get('commentaire'), 'user' => $user]),
                'text/html'
            );
        $mailer->send($message);

        return $this->index('Le message a bien été envoyer.');
    }

    /**
     * @Route("/addNote", name="add_note")
     */
    public function addNote(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = null;
        if ($this->getUser() != null) {
            $user = $em->getRepository("App\\Application\\Sonata\\UserBundle\\Entity\\User")->find($this->getUser()->getId());
        }

        $entityNote = null;
        if ($user != null) {
            $film = $this->checkFilm($request->request->get("idFilm"), $request->request->get("titre"));
            $entityNote = $em->getRepository(Notes::class)->findOneBy(array("user" => $this->getUser()->getId(), "film" => $film->getId()));
        }
        if ($entityNote == null) {
            $entityNote = new Notes();
            $entityNote->setFilm($this->checkFilm($request->request->get("idFilm"), $request->request->get("titre")))
                ->setUser($user);
        }


        $entityNote->setValeur($request->request->get('note'));


        $em->persist($entityNote);
        $em->flush();


        return $this->json(['result' => 'ok']);
    }


    private function checkFilm($idApi, $nom)
    {
        $em = $this->getDoctrine()->getManager();
        $film = $em->getRepository(Films::class)->findOneBy(array("idApi" => $idApi));

        if ($film == null) {
            $entityFilm = new Films();
            $entityFilm->setIdApi($idApi)
                ->setNom($nom);
            $em->persist($entityFilm);
            $em->flush();

            $film = $entityFilm;
        }

        return $film;

    }

    /**
     * @Route("/addFavoris", name="add_favoris")
     */
    public function addFavoris(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("App\\Application\\Sonata\\UserBundle\\Entity\\User")->find($this->getUser()->getId());

        $film = $this->checkFilm($request->request->get("idFilm"), $request->request->get("titre"));
        $entityFav = $em->getRepository(Favoris::class)->findOneBy(array("user" => $this->getUser()->getId(), "film" => $film->getId()));

        if ($entityFav == null) {
            $entityFav = new Favoris();
            $entityFav->setFilm($this->checkFilm($request->request->get("idFilm"), $request->request->get("titre")))
                ->setUser($user);
        }

        $entityFav->setFavoris($request->request->get('favoris'));

        $em->persist($entityFav);
        $em->flush();


        return $this->json(['result' => 'ok']);
    }

    /**
     * @Route("/addVue", name="add_vue")
     */
    public function addVue(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("App\\Application\\Sonata\\UserBundle\\Entity\\User")->find($this->getUser()->getId());

        $film = $this->checkFilm($request->request->get("idFilm"), $request->request->get("titre"));
        $entityFav = $em->getRepository(Favoris::class)->findOneBy(array("user" => $this->getUser()->getId(), "film" => $film->getId()));

        if ($entityFav == null) {
            $entityFav = new Favoris();
            $entityFav->setFilm($this->checkFilm($request->request->get("idFilm"), $request->request->get("titre")))
                ->setUser($user);
        }

        $entityFav->setVue($request->request->get('vue'));

        $em->persist($entityFav);
        $em->flush();


        return $this->json(['result' => 'ok']);
    }

    /**
     * @Route("/recherche", name="index_recherche")
     */
    public function indexRecherche()
    {

        Unirest\Request::verifyPeer(false);
        $header = array('Accept' => 'application/json');
        $body = array(
            'api_key' => getenv('API_KEY'),
            'language' => getenv('API_LANG'),
            'query' => "action",
            'page' => 1

        );
        $reponse = Unirest\Request::get('https://api.themoviedb.org/3/genre/movie/list', $header, $body);

        return $this->render('default/recherche.film.html.twig', array('genres' => $reponse->body->genres));
    }

    /**
     * @Route("/recherche/liste", name="recherche_film")
     */
    public function recherche(Request $request)
    {
        Unirest\Request::verifyPeer(false);
        $header = array('Accept' => 'application/json');

        if ($request->request->get('titre') == "" && $request->request->get('genres') == null) {
            $return['etat'] = "ko";
            $return['data'] = "Pour une recherche vous devez renseigner un titre ou au moins un genre.";
        } else {
            if ($request->request->get('titre') != "") {
                $body = array(
                    'api_key' => getenv('API_KEY'),
                    'language' => getenv('API_LANG'),
                    'query' => $request->request->get('titre'),
                    'page' => 1
                );
                $reponse = Unirest\Request::get('https://api.themoviedb.org/3/search/movie', $header, $body);

                for ($i = 0; $i < count($reponse->body->results); $i++) {
                    if ($request->request->get('genres') == null || count(array_intersect($request->request->get('genres'), $reponse->body->results[$i]->genre_ids)) > 0) {
                        $data[$i]['poster_path'] = $reponse->body->results[$i]->poster_path;
                        $data[$i]['title'] = $reponse->body->results[$i]->title;
                        $data[$i]['overview'] = $reponse->body->results[$i]->overview;
                        $data[$i]['id'] = $reponse->body->results[$i]->id;
                    }
                }
            } else {
                $body = array(
                    'api_key' => getenv('API_KEY'),
                    'language' => getenv('API_LANG'),
                    'with_genres' => implode("|", $request->request->get('genres')),
                    'page' => 1
                );
                $reponse = Unirest\Request::get('https://api.themoviedb.org/3/discover/movie', $header, $body);

                for ($i = 0; $i < count($reponse->body->results); $i++) {
                    $data[$i]['poster_path'] = $reponse->body->results[$i]->poster_path;
                    $data[$i]['title'] = $reponse->body->results[$i]->title;
                    $data[$i]['overview'] = $reponse->body->results[$i]->overview;
                    $data[$i]['id'] = $reponse->body->results[$i]->id;

                }
            }
            $return['etat'] = "ok";
            $return['data'] = $data;
        }

        return $this->json(['result' => $return]);
    }

    /**
     * @Route("/addCommentaire", name="add_commentaire")
     */
    public function addCommentaire(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("App\\Application\\Sonata\\UserBundle\\Entity\\User")->find($this->getUser()->getId());
        $film = $this->checkFilm($request->request->get("idFilm"), $request->request->get("titre"));
        $entityCommentaire = new Commentaires();
        $entityCommentaire->setUser($user)
            ->setFilm($film)
            ->setContenu($request->request->get('commentaire'));

        $em->persist($entityCommentaire);
        $em->flush();

        return $this->film($request->request->get("idFilm"));
    }

    /**
     * @Route("/deleteCommentaire", name="delete_commentaire")
     */
    public function deleteCommentaire(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $commentaire = $em->getRepository(Commentaires::class)->find($request->request->get('id'));

        $em->remove($commentaire);

        $em->flush();

        return $this->render('default/film.html.twig', array('data' => $this->dataFilm($request->request->get("idFilm"))));
    }
}