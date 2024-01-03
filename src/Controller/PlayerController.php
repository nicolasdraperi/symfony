<?php

namespace App\Controller;

use App\Entity\Player;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class PlayerController extends AbstractController
{
    #[Route('/player/create', name: 'app_player_create')]
    public function save(EntityManagerInterface $entityManager, Request $request): Response
    {
        $nom = $request->request->get("nom");
        $pv = $request->request->get("pv");
        $mana = $request->request->get("mana");
        $ap = $request->request->get("ap");
        $ad = $request->request->get("ad");

        $player = new Player();
        $player
            ->setNom($nom)
            ->setPv($pv)
            ->setMana($mana)
            ->setAp($ap)
            ->setAd($ad);

        $entityManager->persist($player);
        $entityManager->flush();

        return $this->render('player/index.html.twig', ["player" => $player]);
    }

    #[Route('/player/show/{id}', name: 'app_player_show')]
    public function show(Player $player): Response
    {
        return $this->render('player/index.html.twig', ["player" => $player]);
    }

    #[Route('/player/delete/{id}', name:"app_player_delete")]
    public function delete(EntityManagerInterface $entityManager, Player $player){
        $entityManager->remove($player);
        $entityManager->flush();
        return new Response("User supprimé");

    }

    #[Route('/player/show_all', name: "app_player_all")]
    public function showAll(EntityManagerInterface $entityManager): Response
    {
        $players = $entityManager->getRepository(Player::class)->findAll();
        return $this->render('player/show.html.twig', ["players" => $players]);
    }

    #[Route('/player/formulaire', name: "player_formulaire")]
    public function formulaire(): Response
    {
        return $this->render('player/formulaire.html.twig');
    }

    #[Route('/player/formulaire_update/{id}', name: "player_formulaire_update")]
    public function formulaire_update(int $id, EntityManagerInterface $entityManager): Response
    {
        $player = $entityManager->getRepository(Player::class)->find($id);
        return $this->render('player/formulaire_update.html.twig', [
            'player' => $player,
        ]);
    }

    #[Route('/player/update/{id}', name: "player_update")]
    public function update(EntityManagerInterface $entityManager, int $id, Request $request): Response
    {
        $player = $entityManager->getRepository(Player::class)->find($id);
        $nom = $request->request->get("nom");
        $pv = $request->request->get("pv");
        $mana = $request->request->get("mana");
        $ap = $request->request->get("ap");
        $ad = $request->request->get("ad");

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
            ->setAd($ad);

        $entityManager->flush();

        $players = $entityManager->getRepository(Player::class)->findAll();
        return $this->render('player/show.html.twig', ["players" => $players]);
    }
}

