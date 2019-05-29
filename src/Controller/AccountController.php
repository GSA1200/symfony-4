<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Form\PasswordUpdateType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Form\FormError;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class AccountController extends AbstractController
{
    /**
     * Permet d'afficher et de gérer le formulaire de connexion utilisateur
     * @Route("/login", name="account_login")
     * @return Response
     */
    public function login(AuthenticationUtils $utils){
        $error=$utils->getLastAuthenticationError();
        
        $username=$utils->getLastUsername();
        
        return $this->render('account/login.html.twig',[
            'hasError'=>$error != null,
            'username'=>$username    
        ]);
    }
    
    /**
     * @Route("/logout", name="account_logout")
     * @return void
     * 
     */
        public function logout()
    {
        return $this->render();
    }
    
    /**
     * Permet d'afficher le formulaire d'inscription
     * @Route("/register",name="account_register")
     * @return Response
     */
    public function register(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder){
        $user=new User();
        
        $form=$this->createForm(RegistrationType::class, $user);
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $hash=$encoder->encodePassword($user,$user->getHash());
            $user->setHash($hash);
            $manager->persist($user);
            $manager->flush();
            
            $this->addFlash('success',
                    "Votre compte a bien été créé ! Vous pouvez maintenant vous connecter !");
            
            return $this->redirectToRoute('account_login');
            
        }
        return $this->render('account/registration.html.twig',[
            'form'=>$form->createView()
        ]);
    }
    /**
     * Permet d'afficher et de traiter le formulaire de modification de profil
     * @Route("/account/profile",name="account_profile")
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function profile (Request $request,ObjectManager $manager) {
        $user=$this->getUser();
        
        $form=$this->createForm(AccountType::class, $user);
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($user);
            $manager->flush();
            
            $this->addFlash('success',
                    "Les modifications ont bien été enregistrées !");
            
            return $this->redirectToRoute('account_login');
        }
        
        return $this->render('account/profile.html.twig',[
            'form'=>$form->createView()
        ]);
    }
    /**
     * Permet de modifier le mot de passe
     * @Route("/account/password-update", name="account_password")
     * @IsGranted("ROLE_USER")
     * @return Response
     * 
     */
    public function updatePassword(Request $request, UserPasswordEncoderInterface $encoder,
            ObjectManager $manager){
        
        $passwordUpdate=new PasswordUpdate();
        
        $user=$this->getUser();
        
        $form=$this->createForm(PasswordUpdateType::class,$passwordUpdate);
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            //1. Vérifier que le oldPassword est le même que le password en cours
            if(!password_verify($passwordUpdate->getOldPassword(),$user->getHash())){
                //Gérer l'erreur
                $form->get('oldPassword')->addError(new FormError("Le mot de passe que"
                        . " vous avez saisi n'est pas votre mot de passe actuel"));
            } else {
                
                $newPassword=$passwordUpdate->getNewPassword();
                $hash=$encoder->encodePassword($user,$newPassword);
                $user->setHash($hash);
                
                $manager->persist($user);
                $manager->flush();
                $this->addFlash('success', "Votre mot de passe a bien été modifié !");
                
                return $this->redirectToRoute('homepage');
            } 

        }
        
        return $this->render('account/password.html.twig',[
            'form' => $form->createView()
        ]);
    }
    
    /**
     * Permet d'afficher le profil de l'utilisateur connecté
     * @Route("/account", name="account_index")
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function myAccount(){
        return $this->render('user/index.html.twig',[
            'user'=>$this->getUser()
        ]);
    }

    /**
     * permet d'afficher la liste des réservations faites par l'utilisateur
     * @Route("/account/bookings", name="account_bookings")
     */
    public function booking(){
        return $this->render('account/bookings.html.twig');
    }
}
