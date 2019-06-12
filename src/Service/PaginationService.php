<?php

namespace App\Service;

use Twig\Environment;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RequestStack;

class PaginationService{

    private $entityClass;
    private $limit=10;
    private $currentPage = 1;
    private $manager;
    private $twig;
    private $route;
    private $user;

    public function __construct(ObjectManager $manager, Environment $twig, RequestStack $request){
        //dump($request);
        //die(;)

        $this->route=$request->getCurrentRequest()->attributes->get('_route');

        $this->manager= $manager;
        $this->twig=$twig;
    }

    public function setUser($user){
        $this->user=$user;
        return $this;
    }

    public function getUser(){
        return $user;
    }
    public function setRoute($route){
        $this->route=$route;
        return $this;
    }

    public function getRoute(){
        return $route;
    }
    public function display(){
        $this->twig->display('admin/partials/pagination.html.twig',[
            'page'=>$this->currentPage,
            'pages'=>$this->getPages(),
            'route'=>$this->route
        ]);
    }

    public function getPages(){
        if(empty($this->entityClass)){
            throw new \Exception("Vous n'avez pas spécifié l'entité sur laquelle paginer !
             Utilisez la méthode setEntityClass() de votre objet PaginationService.");
        }
        // connaitre le total des enregistrements de  la table
            $repo=$this->manager->getRepository($this->entityClass);
            $total=count($repo->findAll());

        // faire la division, l'arrondi et le renvoyer
            $pages=ceil($total/$this->limit); //ceil: arrondir à l'entier supérieur : 3,4 = 4
            return $pages;
    }
    public function getData(){
        if(empty($this->entityClass)){
            throw new \Exception("Vous n'avez pas spécifié l'entité sur laquelle paginer !
             Utilisez la méthode setEntityClass() de votre objet PaginationService.");
        }
        // calculer l'offset
            $offset=$this->currentPage * $this->limit - $this->limit;
        // demander au repository de trouver les éléments
            $repo = $this->manager->getRepository($this->entityClass);
            $data= $repo->findBy([],[], $this->limit,$offset);
        // renvoyer les éléments en question
            return $data;
    }

    public function setPage($page){
        $this->currentPage= $page;
        return $this;
    }

    public function getPage(){
        return $this->currentPage;
    }

    public function setLimit($limit){
        $this->limit= $limit;
        return $this;
    }

    public function getLimit($limit){
        return $this->limit;
    }

    public function setEntityClass($entityClass){
        $this->entityClass = $entityClass;
        return $this;
    }

    public function getEntityClass(){
        return $this->entityClass;
    }

}