<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{
    /**
     * @Route("/hello/{prenom}/{age}", name="hello")
     */
    public function hello($prenom="anonyme",$age=0){
        return $this->render(
                'home/hello.html.twig',
                [
                    'prenom'=>$prenom,
                    'age'=>$age
                ]
                );
    }

    /**
     * @Route("/", name="homepage")
     */
    public function home()
    {
        $prenoms=["Yvan"=>51,"Ingrid"=>50,"Lili"=>3];
        
        return $this->render(
                'home/home.html.twig',[
                    'title'=>"Bonjour à tous",
                    'content'=>"Contenu de mon article",
                    'age'=>17,
                    'prenoms'=>$prenoms
                ]
                );
    }
}
