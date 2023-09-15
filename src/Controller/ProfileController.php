<?php

namespace App\Controller;

use App\Entity\Report;
use App\Entity\User;
use App\Form\PasswordEditType;
use App\Form\ProfileEditType;
use App\Form\SocialNetworksType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Intervention\Image\ImageManagerStatic as Image;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {

        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();
        $cleanDescription = str_replace('`', "\`", $user->getDescription());
        $cleanDescription = preg_replace('/\\\\([0-7]{1,3})/', '', $cleanDescription);

        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
            'user' => $this->getUser(),
            'socials' => $this->getUser()->getSocialNetworks(),
            'cleanDescription' => $cleanDescription,
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
                $newFilename = $safeFilename . '-' . uniqid() . '.';

                // if user already has an avatar, delete the old one
                if ($user->getAvatar()) {
                    $oldAvatar = $this->getParameter('avatar_dir') . '/' . $user->getAvatar();
                    if (file_exists($oldAvatar)) {
                        unlink($oldAvatar);
                    }
                }

                try {
                    // $avatar->move(
                    //     $this->getParameter('avatar_dir'),
                    //     $newFilename
                    // );
                    // compress image with intervention/image
                    $image = Image::make($avatar)->resize(300, null, function ($constraint) {
                        $constraint->aspectRatio();
                    })->encode('webp', 80);
                    $newFilename .= 'webp';

                    $image->save($this->getParameter('avatar_dir') . '/' . $newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Error uploading avatar');
                }

                $user->setAvatar($newFilename);
            }


            $user->setUpdatedAt(new \DateTimeImmutable());

            // upate position
            $randomize = $request->request->get('randomize');
            if ($form->get('latitude')->getData() && $form->get('longitude')->getData()) {
                if ($randomize) {
                    $user->setLatitude($form->get('latitude')->getData() + rand(-100, 100) / 10000);
                    $user->setLongitude($form->get('longitude')->getData() + rand(-100, 100) / 10000);
                } else {
                    $user->setLatitude($form->get('latitude')->getData());
                    $user->setLongitude($form->get('longitude')->getData());
                }
            }

            //clean description, remove octal escape sequences and prevent XSS
            $cleanDescription = preg_replace('/\\\\([0-7]{1,3})/', '', $form->get('description')->getData());
            $cleanDescription = htmlspecialchars($cleanDescription, ENT_QUOTES, 'UTF-8');
            $user->setDescription($cleanDescription);


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

    #[Route('/profile/edit/change-password', name: 'app_profile_change_password')]
    public function editChangePassword(EntityManagerInterface $em, Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(PasswordEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $oldPassword = $form->get('oldPassword')->getData();
            if ($userPasswordHasher->isPasswordValid($user, $oldPassword)) {
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
                $user->setUpdatedAt(new \DateTimeImmutable());
                $em->persist($user);
                $em->flush();
                $this->addFlash('success', 'Password updated successfully');
                return $this->redirectToRoute('app_profile');
            } else {
                $this->addFlash('error', 'Old password is not correct');
            }

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/edit_password.html.twig', [
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


    #[Route('/profile/{slug}', name: 'app_profile_show')]
    public function show($slug, EntityManagerInterface $em): Response
    {
        $user = $em->getRepository(User::class)->findOneBy(['slug' => $slug]);

        if (!$user) {
            return $this->redirectToRoute('app_profile_not_found');
        }

        if ($this->getUser()) {
            if ($user->getSlug() === $this->getUser()->getSlug()) {
                return $this->redirectToRoute('app_profile');
            }
        }

        $cleanDescription = str_replace('`', "\`", $user->getDescription());
        //remove octal escape sequences
        $cleanDescription = preg_replace('/\\\\([0-7]{1,3})/', '', $cleanDescription);

        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
            'user' => $user,
            'socials' => $user->getSocialNetworks(),
            'cleanDescription' => $cleanDescription,
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

