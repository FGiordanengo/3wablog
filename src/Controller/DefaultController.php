<?php

namespace App\Controller;

use App\Service\Calculator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Routing\RouterInterface;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        return $this->render("article/home.html.twig");
    }

    /**
     * @Route("/news/{slug}", name="article_show")
     */
    public function show($slug = "toto") //defaults={"slug": "toto"}
    {
        $comments = [
            "Ho ce nicolas il en rate pas une",
            "Je le savais, tous pourris !",
            "Touchez pas a mon nico"
        ];

        return $this->render("article/show.html.twig", [
            "title" => "Nicolas .....",
            "comments" => $comments
        ]);
    }

    /**
     *
     * @Route("/facture/{totalHT}/{taux}")
     */
    public function facture(Calculator $calculator, $totalHT, $taux = 20)
    {
        $total = $calculator->getTotal($totalHT, $taux);

        return new Response("Le total est $total");
    }
}
