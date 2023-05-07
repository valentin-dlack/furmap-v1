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
    public function index(EntityManagerInterface $em): Response
    {
        if (isset($_POST['search']) || isset($_POST['continent']) || isset($_POST['order_by'])) {
            $search = $_POST['search'];
            $continent = $_POST['continent'];
            $order_by = $_POST['order_by'];
            $qb = $em->createQueryBuilder();
            $qb->select('c')
                ->from(Convention::class, 'c')
                ->where('c.name LIKE :search')
                ->orWhere('c.description LIKE :search')
                ->setParameter('search', '%' . $search . '%');

            switch ($continent) {
                case 'africa':
                    $qb->andWhere('c.longitude > :longitudeMin')
                        ->andWhere('c.longitude < :longitudeMax')
                        ->andWhere('c.latitude > :latitudeMin')
                        ->andWhere('c.latitude < :latitudeMax')
                        ->setParameter('longitudeMin', -20)
                        ->setParameter('longitudeMax', 60)
                        ->setParameter('latitudeMin', -40)
                        ->setParameter('latitudeMax', 40);
                    break;
                case 'asia':
                    $qb->andWhere('c.longitude > :longitudeMin')
                        ->andWhere('c.longitude < :longitudeMax')
                        ->andWhere('c.latitude > :latitudeMin')
                        ->andWhere('c.latitude < :latitudeMax')
                        ->setParameter('longitudeMin', 60)
                        ->setParameter('longitudeMax', 180)
                        ->setParameter('latitudeMin', -10)
                        ->setParameter('latitudeMax', 60);
                    break;
                case 'europe':
                    $qb->andWhere('c.longitude > :longitudeMin')
                        ->andWhere('c.longitude < :longitudeMax')
                        ->andWhere('c.latitude > :latitudeMin')
                        ->andWhere('c.latitude < :latitudeMax')
                        ->setParameter('longitudeMin', -20)
                        ->setParameter('longitudeMax', 60)
                        ->setParameter('latitudeMin', 40)
                        ->setParameter('latitudeMax', 80);
                    break;
                case 'america':
                    $qb->andWhere('c.longitude > :longitudeMin')
                        ->andWhere('c.longitude < :longitudeMax')
                        ->andWhere('c.latitude > :latitudeMin')
                        ->andWhere('c.latitude < :latitudeMax')
                        ->setParameter('longitudeMin', -180)
                        ->setParameter('longitudeMax', -20)
                        ->setParameter('latitudeMin', -60)
                        ->setParameter('latitudeMax', 80);
                    break;
                case 'oceania':
                    $qb->andWhere('c.longitude > :longitudeMin')
                        ->andWhere('c.longitude < :longitudeMax')
                        ->andWhere('c.latitude > :latitudeMin')
                        ->andWhere('c.latitude < :latitudeMax')
                        ->setParameter('longitudeMin', 100)
                        ->setParameter('longitudeMax', 180)
                        ->setParameter('latitudeMin', -60)
                        ->setParameter('latitudeMax', 0);
                    break;
                case 'all':
                    break;
            }

            switch ($order_by) {
                case 'most_attendees':
                    $qb->orderBy('c.attendance', 'DESC');
                    break;
                case 'least_attendees':
                    $qb->orderBy('c.attendance', 'ASC');
                    break;
                case 'closest':
                    $qb->orderBy('c.start_date', 'ASC');
                    break;
                case 'farthest':
                    $qb->orderBy('c.start_date', 'DESC');
                    break;
                default:
                    $qb->orderBy('c.attendance', 'DESC');
                    break;
            }

            $conventions = $qb->getQuery()->getResult();
        } else {
            $conventions = $em->getRepository(Convention::class)->findBy([], ['attendance' => 'DESC']);
        }

        return $this->render('convention/index.html.twig', [
            'conventions' => $conventions,
            'continent' => $continent ?? 'all',
            'search' => $search ?? '',
            'order_by' => $order_by ?? '',
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
