<?php
 
namespace App\Controller;
 
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
 
class TestController extends AbstractController
{
 
    #[Route('/index')]
    public function index(){
        return new Response("hello word");
    }
    #[Route('/nom/{name}')]
    public function test(string $name){
        return $this->Render("test.html.twig",["name"=>$name]);
    }
}