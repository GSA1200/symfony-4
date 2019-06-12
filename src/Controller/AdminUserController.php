<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Service\PaginationService;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminUserController extends AbstractController
{
    /**
     * @Route("/admin/users/{page<\d+>?1}", name="admin_users_index")
     */
    public function index(CommentRepository $repo, $page, PaginationService $pagination)
    {

        $pagination->setEntityClass(User::class)
                    ->setPage($page)
                    ->setLimit(5);

        return $this->render('admin/user/index.html.twig', [
            'pagination'=>$pagination
        ]);
    }

    /**
     * permet d'afficher le formulaire d'édition pour la modération des utlisateurs
     * @Route("/admin/users/{id}/edit", name="admin_users_edit")
     */
    public function edit(User $user, Request $request, ObjectManager $manager) {
        $form= $this->createForm(AccountType::class, $user);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()){

            $manager->persist($user);
            $manager->flush();
            
            $this->addFlash(
                    'success',
                    "les données de l'utilisateur <strong>{$user->getId()}</strong>"
            . " ont bien été modifiées");
        }
        return $this->render('admin/user/edit.html.twig',[
            'user' => $user,
            'form' => $form->createView()
        ]);

    }

        /**
     * Permet à l'administrateur de supprimer un utilisateur
     * @Route("/admin/users/{id}/delete", name="admin_users_delete")
     * @return Response
     */
    public function delete(User $user, ObjectManager $manager){

        $manager->remove($user);
        $manager->flush();

        $this->addFlash(
            'success',
            "l'utilisateur <strong>{$user->getFullName()}</strong>"
    . " a bien été supprimé !");

                return $this->redirectToRoute('admin_users_index');
        }
}
