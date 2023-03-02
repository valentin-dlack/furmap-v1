<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Util\Json;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManager;

#[Route('/control-admin')]
class AdminController extends AbstractController
{
    private CsrfTokenManager $csrfTokenManager;
    public function __construct() {
        $this->csrfTokenManager = new CsrfTokenManager();
    }

    #[Route('/', name: 'app_admin')]
    public function index(EntityManagerInterface $em): Response
    {

        //Stat data
        /* Total users */
        $totalUsers = $em->createQuery('SELECT COUNT(u.id) FROM App\Entity\User u')->getSingleScalarResult();
        /* Total users compared to last month */
        $totalUsersLastMonth = $em->createQuery('SELECT COUNT(u.id) FROM App\Entity\User u WHERE u.created_at > :lastMonth')->setParameter('lastMonth', new \DateTime('first day of last month'))->getSingleScalarResult();
        /* Compare total users to last month */
        $totalUsersCompare = $totalUsers - $totalUsersLastMonth;
        /* Total users compared to last month in percentage */
        $totalUsersComparePercentage = ($totalUsersCompare / $totalUsersLastMonth) * 100;
        
        /* Total reports */
        $totalReports = $em->createQuery('SELECT COUNT(r.id) FROM App\Entity\Report r')->getSingleScalarResult();
        /* Total reports compared to last month */
        $totalReportsLastMonth = $em->createQuery('SELECT COUNT(r.id) FROM App\Entity\Report r WHERE r.createdAt > :lastMonth')->setParameter('lastMonth', new \DateTime('first day of last month'))->getSingleScalarResult();
        /* Compare total reports to last month */
        $totalReportsCompare = $totalReports - $totalReportsLastMonth;
        /* Total reports compared to last month in percentage */
        $totalReportsComparePercentage = ($totalReportsCompare / ($totalReportsLastMonth == 0 ? 1 : $totalReportsLastMonth)) * 100;

        //graph data
        /* Line graph of total users per month */
        //get all users that have created_at not null
        $qb = $em->createQueryBuilder();
        $qb->select('u')
            ->from('App\Entity\User', 'u')
            ->where('u.created_at IS NOT NULL');
        $users = $qb->getQuery()->getResult();

        $usersPerMonthAndYear = [];
        foreach ($users as $user) {
            $month = $user->getCreatedAt()->format('m');
            $year = $user->getCreatedAt()->format('Y');
            $usersPerMonthAndYear[$year][$month] = $usersPerMonthAndYear[$year][$month] ?? 0;
            $usersPerMonthAndYear[$year][$month]++;
        }

        $usersPerMonthAndYear = array_reverse($usersPerMonthAndYear, true);

        //format data for chart.js  
        $labels = [];
        $data = [];
        foreach ($usersPerMonthAndYear as $year => $months) {
            foreach ($months as $month => $count) {
                $labels[] = $month . '/' . $year;
                $data[] = $count;
            }
        }

        $usersPerMonthAndYear = [
            'labels' => $labels,
            'data' => $data,
        ];

        //tables data
        /* Users table (5 most recent) */
        $qb = $em->createQueryBuilder();
        $qb->select('u')
            ->from('App\Entity\User', 'u')
            ->orderBy('u.created_at', 'DESC')
            ->setMaxResults(5);
        $usersTable = $qb->getQuery()->getResult();

        /* Reports table (5 most recent) */
        $qb = $em->createQueryBuilder();
        $qb->select('r')
            ->from('App\Entity\Report', 'r')
            ->orderBy('r.createdAt', 'DESC')
            ->setMaxResults(5);
        $reportsTable = $qb->getQuery()->getResult();

        return $this->render('admin/index.html.twig', [
            'totalUsers' => $totalUsers,
            'totalUsersCompare' => $totalUsersCompare,
            'totalUsersComparePercent' => $totalUsersComparePercentage,
            'totalReports' => $totalReports,
            'totalReportsCompare' => $totalReportsCompare,
            'totalReportsComparePercent' => $totalReportsComparePercentage,
            'usersPerMonthAndYear' => $usersPerMonthAndYear,
            // TABLES
            'usersTable' => $usersTable,
            'reportsTable' => $reportsTable,
        ]);
    }

    /* ------------------ USERS ------------------ */

    #[Route('/users', name: 'app_admin_users')]
    public function users(EntityManagerInterface $em): Response
    {
        /* Users table (All) */
        $qb = $em->createQueryBuilder();
        $qb->select('u')
            ->from('App\Entity\User', 'u')
            ->orderBy('u.created_at', 'DESC');
        $usersTable = $qb->getQuery()->getResult();

        return $this->render('admin/users/users.html.twig', [
            'usersTable' => $usersTable,
        ]);
    }

    #[Route('/user/{id}', name: 'app_admin_user')]
    public function user($id, EntityManagerInterface $em): Response
    {
        if (!is_numeric($id)) {
            throw $this->createNotFoundException('The user does not exist');
        }

        /* User */
        $qb = $em->createQueryBuilder();
        $qb->select('u')
            ->from('App\Entity\User', 'u')
            ->where('u.id = :id')
            ->setParameter('id', $id);
        $user = $qb->getQuery()->getOneOrNullResult();

        if (!$user) {
            throw $this->createNotFoundException('The user does not exist');
        }

        /* Reports table (As user reported) */
        $qb = $em->createQueryBuilder();
        $qb->select('r')
            ->from('App\Entity\Report', 'r')
            ->where('r.user_reported = :user')
            ->setParameter('user', $user)
            ->orderBy('r.createdAt', 'DESC');
        $reportsTable1 = $qb->getQuery()->getResult();
        
        /* Reports table (As user reporting) */
        $qb = $em->createQueryBuilder();
        $qb->select('r')
            ->from('App\Entity\Report', 'r')
            ->where('r.user_reporting = :user')
            ->setParameter('user', $user)
            ->orderBy('r.createdAt', 'DESC');
        $reportsTable = array_merge($reportsTable1, $qb->getQuery()->getResult());

        /* get all socials and format them for textareas */
        $socials = $user->getSocialNetworks()->getAllSocials();
        $socialsFormatted = '';
        foreach ($socials as $key => $value) {
            //remove if value is null
            if ($value == null) {
                unset($socials[$key]);
                continue;
            }
            $socialsFormatted .= $key . ':' . $value . '\n ';
        }

        return $this->render('admin/users/show_user.html.twig', [
            'user' => $user,
            'reportsTable' => $reportsTable,
            'socials' => $socialsFormatted,
        ]);
    }

    #[Route('/user/{id}/edit', name: 'app_admin_user_edit')]
    public function userEdit($id, Request $request, EntityManagerInterface $em): Response
    {
        if (!is_numeric($id)) {
            throw $this->createNotFoundException('The user does not exist');
        }

        /* User */
        $qb = $em->createQueryBuilder();
        $qb->select('u')
            ->from('App\Entity\User', 'u')
            ->where('u.id = :id')
            ->setParameter('id', $id);
        $user = $qb->getQuery()->getOneOrNullResult();

        if (!$user) {
            throw $this->createNotFoundException('The user does not exist');
        }

        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'User edited successfully!');

            return $this->redirectToRoute('app_admin_user', ['id' => $user->getId()]);
        }

        return $this->render('admin/users/edit_user.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/user/{id}/delete', name: 'app_admin_user_delete')]
    public function userDelete($id, EntityManagerInterface $em, Request $request): Response
    {
        if (!is_numeric($id)) {
            throw $this->createNotFoundException('The user does not exist');
        }

        /* check csrf token */
        // get token from request
        $token = $request->request->get('_token');
        // check if token is valid
        if (!$this->isCsrfTokenValid(('delete-user' . $id), $token)) {
            throw $this->createAccessDeniedException('Invalid CSRF token');
        }

        /* User */
        $qb = $em->createQueryBuilder();
        $qb->select('u')
            ->from('App\Entity\User', 'u')
            ->where('u.id = :id')
            ->setParameter('id', $id);
        $user = $qb->getQuery()->getOneOrNullResult();

        if (!$user) {
            throw $this->createNotFoundException('The user does not exist');
        }

        //remove socials
        $socials = $user->getSocialNetworks();
        $em->remove($socials);

        //get all reports (reported and reporting)
        $qb = $em->createQueryBuilder();
        $qb->select('r')
            ->from('App\Entity\Report', 'r')
            ->where('r.user_reported = :user')
            ->orWhere('r.user_reporting = :user')
            ->setParameter('user', $user);
        $reports = $qb->getQuery()->getResult();

        //remove all reports
        foreach ($reports as $report) {
            $em->remove($report);
        }

        $em->remove($user);
        $em->flush();

        $this->addFlash('success', 'User deleted successfully!');

        return $this->redirectToRoute('app_admin_users');
    }

    /* ------------------ REPORTS ------------------ */

    #[Route('/reports', name: 'app_admin_reports')]
    public function reports(EntityManagerInterface $em): Response
    {
        /* Get all reports */
        $qb = $em->createQueryBuilder();
        $qb->select('r')
            ->from('App\Entity\Report', 'r')
            ->orderBy('r.createdAt', 'DESC');

        $reports = $qb->getQuery()->getResult();

        return $this->render('admin/reports/reports.html.twig', [
            'reports' => $reports,
        ]);
    }

    #[Route('/report/{id}', name: 'app_admin_report')]
    public function report($id, EntityManagerInterface $em): Response
    {
        if (!is_numeric($id)) {
            throw $this->createNotFoundException('The report does not exist');
        }

        /* Report */
        $qb = $em->createQueryBuilder();
        $qb->select('r')
            ->from('App\Entity\Report', 'r')
            ->where('r.id = :id')
            ->setParameter('id', $id);
        $report = $qb->getQuery()->getOneOrNullResult();

        if (!$report) {
            throw $this->createNotFoundException('The report does not exist');
        }

        return $this->render('admin/reports/show_report.html.twig', [
            'report' => $report,
        ]);
    }

    #[Route('/report/{id}/delete', name: 'app_admin_report_delete')]
    public function reportDelete($id, EntityManagerInterface $em, Request $request): Response
    {
        if (!is_numeric($id)) {
            throw $this->createNotFoundException('The report does not exist');
        }

        /* check csrf token */
        // get token from request
        $token = $request->request->get('_token');
        // check if token is valid
        if (!$this->isCsrfTokenValid(('delete-report' . $id), $token)) {
            throw $this->createAccessDeniedException('Invalid CSRF token');
        }

        /* Report */
        $qb = $em->createQueryBuilder();
        $qb->select('r')
            ->from('App\Entity\Report', 'r')
            ->where('r.id = :id')
            ->setParameter('id', $id);
        $report = $qb->getQuery()->getOneOrNullResult();

        if (!$report) {
            throw $this->createNotFoundException('The report does not exist');
        }

        $em->remove($report);
        $em->flush();

        $this->addFlash('success', 'Report deleted successfully!');

        return $this->redirectToRoute('app_admin_reports');
    }

    #[Route('/report/{id}/edit', name: 'admin_report_update')]
    public function reportEdit($id, EntityManagerInterface $em, Request $request): Response
    {
        if (!is_numeric($id)) {
            throw $this->createNotFoundException('The report does not exist');
        }

        /* Report status update */
        $qb = $em->createQueryBuilder();
        $qb->select('r')
            ->from('App\Entity\Report', 'r')
            ->where('r.id = :id')
            ->setParameter('id', $id);

        $report = $qb->getQuery()->getOneOrNullResult();

        if (!$report) {
            throw $this->createNotFoundException('The report does not exist');
        }

        /* check csrf token */
        $token = $request->request->get('_csrf_token');
        if (!$this->isCsrfTokenValid(('admin_report_update' . $id), $token)) {
            throw $this->createAccessDeniedException('Invalid CSRF token');
        }

        $report->setState($request->request->get('state'));
        $report->setUpdatedAt(new \DateTimeImmutable());
        $em->persist($report);
        $em->flush();

        $this->addFlash('success', 'Report updated successfully!');

        return $this->redirectToRoute('app_admin_report', ['id' => $id]);
    }

    /* ------------------ AUTO ------------------ */

    #[Route('/auto-detect', name: 'app_admin_auto_detect')]
    public function autoDetect(EntityManagerInterface $em): Response
    {
         

        return $this->render('admin/auto/auto_detect.html.twig', [
        ]);
    }

    #[Route('/maps', name: 'app_admin_maps')]
    public function maps(EntityManagerInterface $em): Response
    {
        //get all users that have latitude and longitude not null
        $qb = $em->createQueryBuilder();
        $qb->select('u')
            ->from(User::class, 'u')
            ->where('u.latitude IS NOT NULL')
            ->andWhere('u.longitude IS NOT NULL');

        $users = $qb->getQuery()->getResult();

        return $this->render('admin/maps/maps.html.twig', [
            'users' => $users,
        ]);
    }

}
