<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AnnonceType;
use App\Repository\AdRepository;
use App\Service\PaginationService;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAdController extends AbstractController
{
    /**
     * @Route("/admin/ads/{page<\d+>?1}", name="admin_ads_index")
     */
    public function index(AdRepository $repo, $page, PaginationService $pagination)
    {

        //méthode find : permet de retrouver un enregistrement par son identifiant
        //$ad=find(332);
        //ou
        //$ad=$repo->findOneBy([
            //'title'=>"Libero quo explicabo unde."
        //]);
        //ou
        //$ads=$repo->findBy([],[],5,0);

        //dump($ads);

        $pagination->setEntityClass(Ad::class)
                    ->setPage($page);

        return $this->render('admin/ad/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * permet d'afficher le formulaire d'édition pour la modération
     * @Route("/admin/ads/{id}/edit", name="admin_ads_edit")
     */
    public function edit(Ad $ad, Request $request, ObjectManager $manager) {
        $form= $this->createForm(AnnonceType::class, $ad);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()){

            $manager->persist($ad);
            $manager->flush();
            
            $this->addFlash(
                    'success',
                    "l'annonce <strong>{$ad->getTitle()}</strong>"
            . " a bien été modifiée");
        }
        return $this->render('admin/ad/edit.html.twig',[
            'ad' => $ad,
            'form' => $form->createView()
        ]);

    }
    /**
     * Permet à l'administrateur de supprimer une annonce
     * @Route("/admin/ads/{id}/delete", name="admin_ads_delete")
     * @return Response
     */
    public function delete(Ad $ad, ObjectManager $manager){

        if(count($ad->getBookings())>0){
            $this->addFlash(
                'warning',
                "Vous ne pouvez pas supprimer l'annonce <strong>{$ad->getTitle()}</strong> car elle possède déjà une ou plusieurs réservations"
            );
        } else{

        $manager->remove($ad);
        $manager->flush();

        $this->addFlash(
            'success',
            "L'annonce <strong>{$ad->getTitle()}</strong> a bien été supprimée !"
        );
    }
                return $this->redirectToRoute('admin_ads_index');
            }
        }
