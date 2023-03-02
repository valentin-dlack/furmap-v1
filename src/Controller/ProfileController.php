<?php

namespace App\Controller;

use App\Entity\Report;
use App\Entity\User;
use App\Form\ProfileEditType;
use App\Form\SocialNetworksType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {

        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
            'user' => $this->getUser(),
            'socials' => $this->getUser()->getSocialNetworks(),
        ]);
    }

    #[Route('/profile/edit', name: 'app_profile_edit')]
    public function edit(EntityManagerInterface $em, Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $avatar */
            $avatar = $form->get('avatar')->getData();

            // file upload
            if ($avatar) {
                $originalFilename = pathinfo($avatar->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $avatar->guessExtension();

                try {
                    $avatar->move(
                        $this->getParameter('avatar_dir'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Error uploading avatar');
                }

                $user->setAvatar($newFilename);
            }


            $user->setUpdatedAt(new \DateTimeImmutable());

            // upate position
            if ($form->get('latitude')->getData() && $form->get('longitude')->getData()) {
                $user->setLatitude($form->get('latitude')->getData());
                $user->setLongitude($form->get('longitude')->getData());
            }
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Profile updated successfully');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/profile/edit/socials', name: 'app_profile_edit_socials')]
    public function editSocials(EntityManagerInterface $em, Request $request): Response
    {
        $user = $this->getUser();
        $userSocials = $user->getSocialNetworks();

        $form = $this->createForm(SocialNetworksType::class, $userSocials);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setUpdatedAt(new \DateTimeImmutable());
            $em->persist($userSocials);
            $em->flush();
            $this->addFlash('success', 'Profile updated successfully');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/edit_socials.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'userSocials' => $userSocials,
        ]);
    }

    #[Route('/profile/not-found', name: 'app_profile_not_found')]
    public function notFound(): Response
    {
        return $this->render('profile/not_found.html.twig');
    }


    #[Route('/profile/{id}', name: 'app_profile_show')]
    public function show($id, EntityManagerInterface $em): Response
    {
        $user = $em->getRepository(User::class)->findOneBy(['id' => $id]);

        if (!$user) {
            return $this->redirectToRoute('app_profile_not_found');
        }

        if ($this->getUser()) {
            if ($user->getId() === $this->getUser()->getId()) {
                return $this->redirectToRoute('app_profile');
            }
        }

        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
            'user' => $user,
            'socials' => $user->getSocialNetworks(),
        ]);
    }

    // /report route, to report a user, only accepts POST requests
    #[Route('/report', name: 'app_profile_report', methods: ['POST'])]
    public function report(EntityManagerInterface $em, Request $request): Response
    {
        $reported_user = $em->getRepository(User::class)->findOneBy(['id' => $request->request->get('id')]);
        $reporting_user = $this->getUser();

        if (!$reporting_user) {
            $this->addFlash('error', 'You must be logged in to report a user');
            return $this->redirectToRoute('app_login');
        }

        if (!$reported_user) {
            $this->addFlash('error', 'User not found');
            return $this->redirectToRoute('app_profile_show', ['id' => $request->request->get('id')]);
        }

        if ($reported_user->getId() === $reporting_user->getId()) {
            $this->addFlash('error', 'You cannot report yourself');
            return $this->redirectToRoute('app_profile_show', ['id' => $request->request->get('id')]);
        }

        //create report
        $report = new Report();
        $report->setUserReporting($reporting_user);
        $report->setUserReported($reported_user);
        // reason support
        $reason = $request->request->get('reason');
        if ($reason === 'Other') {
            $reason = $request->request->get('other_reason');
        }
        //print_r($reason);die;
        $report->setReason($reason);
        // message support
        $message = $request->request->get('message');
        if ($message) {
            $report->setMessage($message);
        } else {
            $report->setMessage("[No message provided]");
        }

        $report->setState('Pending');
        $report->setCreatedAt(new \DateTimeImmutable());
        $report->setUpdatedAt(new \DateTimeImmutable());

        $em->persist($report);
        $em->flush();

        $this->addFlash('success', 'Report sent successfully, we will review it as soon as possible');
        return $this->redirectToRoute('app_profile_show', ['id' => $request->request->get('id')]);
    }
}
