<?php

namespace App\Controller;

use App\Entity\Player;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class PlayerController extends AbstractController
{
    #[Route('/player/create', name: 'app_player_create')]
    public function save(EntityManagerInterface $entityManager, Request $request, Security $security): Response
    {

        $nom = $request->request->get("nom");
        $pv = $request->request->get("pv");
        $mana = $request->request->get("mana");
        $ap = $request->request->get("ap");
        $ad = $request->request->get("ad");
        $status = $request->request->get("status");
        $players = $entityManager->getRepository(Player::class)->findAll();
        $user = $security->getUser();
        $player = new Player();
        $player
            ->setNom($nom)
            ->setPv($pv)
            ->setMana($mana)
            ->setAp($ap)
            ->setAd($ad)
            ->setStatus($status)
            ->setOwner($user);

        $entityManager->persist($player);
        $entityManager->flush();
        return $this->render('player/index.html.twig', ["player" => $player, "players" => $players]);
    }

    #[Route('/player/show/{id}', name: 'app_player_show')]
    public function show(Player $player, EntityManagerInterface $entityManager): Response
    {
        $players = $entityManager->getRepository(Player::class)->findAll();
        return $this->render('player/index.html.twig', ["player" => $player, "players" => $players,]);
    }

    #[Route('/player/delete/{id}', name:"app_player_delete")]
    public function delete(EntityManagerInterface $entityManager, Player $player){
        $entityManager->remove($player);
        $entityManager->flush();
        return new Response("User supprimé");

    }

    #[Route('/player/show_all', name: "app_player_all")]
    public function showAll(EntityManagerInterface $entityManager, Security $security, ): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $players = $entityManager->getRepository(Player::class)->findAll();
        return $this->render('player/show.html.twig', ["players" => $players]);
    }

    #[Route('/player/formulaire', name: "player_formulaire")]
    public function formulaire(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        return $this->render('player/formulaire.html.twig');
    }

    #[Route('/player/formulaire_update/{id}', name: "player_formulaire_update")]
    public function formulaire_update(int $id, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $player = $entityManager->getRepository(Player::class)->find($id);
        return $this->render('player/formulaire_update.html.twig', [
            'player' => $player,
        ]);
    }

    #[Route('/player/update/{id}', name: "player_update")]
    public function update(EntityManagerInterface $entityManager, int $id, Request $request, $security): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $player = $entityManager->getRepository(Player::class)->find($id);
        $nom = $request->request->get("nom");
        $pv = $request->request->get("pv");
        $mana = $request->request->get("mana");
        $ap = $request->request->get("ap");
        $ad = $request->request->get("ad");
        $status = $request->request->get("status");

        if (!$player) {
            throw $this->createNotFoundException(
                'No player found for id '.$id
            );
        }

        $player
            ->setNom($nom)
            ->setPv($pv)
            ->setMana($mana)
            ->setAp($ap)
            ->setAd($ad)
            ->setStatus($status);






        $entityManager->flush();

        $players = $entityManager->getRepository(Player::class)->findAll();
        return $this->render('player/show.html.twig', ["players" => $players]);
    }

    #[Route('/player/attack/{id}', name: 'app_player_attack')]
        public function attack(EntityManagerInterface $entityManager, Request $request, int $id): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $selectedPlayerId = $request->request->get("selected_player");
        $selectedAttackType = $request->request->get("selected_attack_type");

        $player = $entityManager->getRepository(Player::class)->find($id);
        $Physical_attack = $player -> getAp();
        $Magical_attack = $player -> getAd();
        $targetPlayer = $entityManager->getRepository(Player::class)->find($selectedPlayerId);



        if ($selectedAttackType === "Attaque Physique") {
            $targetPlayer->setPv($targetPlayer->getPv() - $Physical_attack);
        } elseif ($selectedAttackType === "Attaque Magique") {
            if ($targetPlayer->getMana()<=0){
                return $this->redirectToRoute('app_player_all');
            }else{
                $targetPlayer->setPv($targetPlayer->getPv() - $Magical_attack);
                $targetPlayer->setMana($targetPlayer->getMana() - 10);
            }

        }

        $entityManager->flush();

        if ($targetPlayer->getPv() <= 0){
            $targetPlayer->setStatus("Dead");
            $targetPlayer->setPv(0);
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_player_all');
    }
    #[Route('/player/create/form', name: 'app_player_create_form    ')]
    public function creation_form(Request $request,EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $player = new Player();
        $form = $this->createFormBuilder($player)
            ->add('nom')
            ->add('pv')
            ->add('mana')
            ->add('ap')
            ->add('ad')
            ->add('status')
            ->add('save', SubmitType::class, ['label' => 'Create Player'])

            ->getForm();

        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()) {
            $player=$form->getData();
            $entityManager->persist($player);
            $entityManager->flush();

            $players = $entityManager->getRepository(Player::class)->findAll();
            return $this->render('player/index.html.twig', ["player" => $player, "players" => $players]);

        }
        $players = $entityManager->getRepository(Player::class)->findAll();
        return $this->render('player/formulairenouveau.html.twig', [
            'form' => $form->createView(),
            "player" => $player,
            "players" => $players
        ]);
    }
    #[Route('/player/update_form/{id}', name: "player_update_form")]
    public function updateform(EntityManagerInterface $entityManager, int $id, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $player = $entityManager->getRepository(Player::class)->find($id);

        if (!$player) {
            throw $this->createNotFoundException(
                'No player found for id '.$id
            );
        }

        $form = $this->createFormBuilder($player)
            ->add('nom')
            ->add('pv')
            ->add('mana')
            ->add('ap')
            ->add('ad')
            ->add('status')
            ->add('save', SubmitType::class, ['label' => 'Update Player'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $players = $entityManager->getRepository(Player::class)->findAll();
            return $this->render('player/show.html.twig', ["players" => $players]);
        }

        return $this->render('player/formulairenouveau_update.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

