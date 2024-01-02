<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Product;

class UserController extends AbstractController
{
    #[Route('/user/create', name: 'app_user')]
    public function save(EntityManagerInterface $entityManager, Request $request): Response
    {
        $identifiant = $request->request->get("identifiant"); 
        $password = $request->request->get("password");
        $description = $request->request->get("description"); 
        $age = $request->request->get("age");

        $User = new User();
        $User
            ->setIdentifiant($identifiant)
            ->setPassword($password)
            ->setDescription($description)
            ->setAge($age);

        $entityManager->persist($User);
        $entityManager->flush();
        return $this->render('user/index.html.twig',["user" => $User,]);
        //return $this->redirectToRoute('app_user_show', ["id"=> $User->getId()]);
        //return $this->render('user/formulaire.html.twig');
    }
    #[Route('/user/show/{id}', name: 'app_user_show')]
    public function show(User $User){

        return $this->render('user/index.html.twig',["user" => $User,]);
    }
    #[Route('/user/delete/{id}', name:"app_player_delete")]
    public function delete(EntityManagerInterface $entityManager, User $user){
        $entityManager->remove($user);
        $entityManager->flush();
        return new Response("User supprimé");

    }
    #[Route('/user/show_all', name:"app_player_all")]
    public function ShowAll(EntityManagerInterface $entityManager){
        $users = $entityManager->getRepository(User::class)->findAll();
        return $this->render('user/show.html.twig', ["users" => $users]);
        

    }
    #[Route('/formulaire', name:"formulaire")]
    public function formumaire(EntityManagerInterface $entityManager){
        return $this->render('user/formulaire.html.twig');
    }


    #[Route('/update/{id}', name:"update")]
    public function update(EntityManagerInterface $entityManager, int $id): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException(
                'No user found for id '.$id
            );
        }

        $user->setIdentifiant('New user name!');
        $user->setPassword('New user name!');
        $user->setDescription('New user name!');
        $user->setAge('New user name!');
        $entityManager->flush();

        return $this->redirectToRoute('app_user_show', [
            'id' => $user->getId()
        ]);
        

    }
}
