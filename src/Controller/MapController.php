<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MapController extends AbstractController
{
    #[Route('/map', name: 'app_map')]
    public function index(EntityManagerInterface $em): Response
    {
        //get all users that have latitude and longitude not null
        $qb = $em->createQueryBuilder();
        $qb->select('u')
            ->from(User::class, 'u')
            ->where('u.latitude IS NOT NULL')
            ->andWhere('u.longitude IS NOT NULL');

        $users = $qb->getQuery()->getResult();

        return $this->render('map/index.html.twig', [
            'controller_name' => 'MapController',
            'users' => $users,
        ]);
    }
}
