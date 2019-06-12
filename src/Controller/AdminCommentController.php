<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Service\PaginationService;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCommentController extends AbstractController
{
    /**
     * @Route("/admin/comments{page<\d+>?1}", name="admin_comments_index")
     */
    public function index(CommentRepository $repo, $page, PaginationService $pagination)
    {

        $pagination->setEntityClass(Comment::class)
                    ->setPage($page)
                    ->setLimit(5);

        return $this->render('admin/comment/index.html.twig', [
            'pagination'=>$pagination
        ]);
    }

        /**
     * permet d'afficher le formulaire d'édition pour la modération des commentaires
     * @Route("/admin/comments/{id}/edit", name="admin_comments_edit")
     */
    public function edit(Comment $comment, Request $request, ObjectManager $manager) {
        $form= $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()){

            $manager->persist($comment);
            $manager->flush();
            
            $this->addFlash(
                    'success',
                    "le commentaire <strong>{$comment->getId()}</strong>"
            . " a bien été modifié");
        }
        return $this->render('admin/comment/edit.html.twig',[
            'comment' => $comment,
            'form' => $form->createView()
        ]);

    }

        /**
     * Permet à l'administrateur de supprimer un commentaire
     * @Route("/admin/comments/{id}/delete", name="admin_comments_delete")
     * @return Response
     */
    public function delete(Comment $comment, ObjectManager $manager){

        $manager->remove($comment);
        $manager->flush();

        $this->addFlash(
            'success',
            "le commentaire de <strong>{$comment->getAuthor()->getFullName()}</strong>"
    . " a bien été supprimé !");

                return $this->redirectToRoute('admin_comments_index');
        }
}
