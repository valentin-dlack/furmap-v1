<?php

namespace App\Controller;

use App\Entity\Convention;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

        //same with conventions
        $qb = $em->createQueryBuilder();
        $qb->select('c')
            ->from(Convention::class, 'c')
            ->where('c.latitude IS NOT NULL')
            ->andWhere('c.longitude IS NOT NULL');

        $conventions = $qb->getQuery()->getResult();

        return $this->render('map/index.html.twig', [
            'controller_name' => 'MapController',
            'users' => $users,
            'conventions' => $conventions,
        ]);
    }

    #[Route('/api/map', name: 'app_map_api')]
    public function getMarkers(EntityManagerInterface $em, Request $req): Response
    {
        //check the http referer, use the absolute url to avoid problems with the port
        
        if ($req->headers->get('referer') !== $this->generateUrl('app_map', [], 0)) {
            return new JsonResponse([
                'error' => 'Invalid',
            ], 403);
        }
        
        //get all users that have latitude and longitude not null
        $qb = $em->createQueryBuilder();
        $qb->select('u')
            ->from(User::class, 'u')
            ->where('u.latitude IS NOT NULL')
            ->andWhere('u.longitude IS NOT NULL');

        $users = $qb->getQuery()->getResult();

        //same with conventions
        $qb = $em->createQueryBuilder();
        $qb->select('c')
            ->from(Convention::class, 'c')
            ->where('c.latitude IS NOT NULL')
            ->andWhere('c.longitude IS NOT NULL');

        $conventions = $qb->getQuery()->getResult();

        $markers = [];

        foreach ($users as $user) {
            $userData[] = [
                'id' => $user->getId(),
                'lat' => $user->getLatitude(),
                'lng' => $user->getLongitude(),
                'popup' => $this->renderView('map/_user_popup.html.twig', [
                    'user' => $user,
                ]),
            ];
        }

        foreach ($conventions as $convention) {
            $conventionData[] = [
                'id' => $convention->getId(),
                'lat' => $convention->getLatitude(),
                'lng' => $convention->getLongitude(),
                'popup' => $this->renderView('map/_convention_popup.html.twig', [
                    'convention' => $convention,
                ]),
            ];
        }

        return new JsonResponse([
            'users' => $userData ?? [],
            'conventions' => $conventionData ?? [],
        ]);
    }
}
