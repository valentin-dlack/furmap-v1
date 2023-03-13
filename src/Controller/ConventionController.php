<?php

namespace App\Controller;

use App\Entity\Convention;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConventionController extends AbstractController
{
    #[Route('/conventions', name: 'app_convention')]
    public function index(): Response
    {
        return $this->render('convention/index.html.twig', [
            'controller_name' => 'ConventionController',
        ]);
    }

    #[Route('/convention/{slug}', name: 'app_convention_show')]
    public function show($slug, EntityManagerInterface $em): Response
    {
        $convention = $em->getRepository(Convention::class)->findOneBy(['slug' => $slug]);
        $cleanDescription = str_replace('`', "\`", $convention->getDescription());
        $cleanDescription = preg_replace('/\\\\([0-7]{1,3})/', '', $cleanDescription);

        return $this->render('convention/show.html.twig', [
            'convention' => $convention,
            'cleanDescription' => $cleanDescription,
        ]);
    }
}
