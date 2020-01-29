<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Movie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     * @IsGranted("ROLE_USER")
     */
    public function index()
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/movie/add/favorite/{id}", name="add_favorite_movie", options={"expose"=true})
     */
    public function addFavoriteMovie(Movie $movie): Response
    {
        dd($movie);
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if (!$user->movies->contains($movie)) {
            $user->addMovie($movie);
            $message = 'added';
        } else {

            $user->removeMovie($movie);
            $message = 'removed';
        }


        $message = "déconnecté";
        if ($this->getUser()) {
            $message = "connecté";
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->persist($movie);
        $em->flush();

        return new JsonResponse(['data' => $message]);
    }
}
