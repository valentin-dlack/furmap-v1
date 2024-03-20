<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    public function __construct(
        private MailerInterface $mailer,
        private EntityManagerInterface $em
    ) {
    }

    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request): Response
    {
        //get the form data
        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $email = $request->request->get('email');
            $message = $request->request->get('message');
            $subject = $request->request->get('subject');

            $date = new \DateTimeImmutable();
            $formatedDate = $date->format('d/m/Y H:i:s');

            //send the email
            $message = (new TemplatedEmail())
                ->from($email)
                ->to('support@lack-fr.com')
                ->subject($subject)
                ->htmlTemplate('emails/contact.html.twig')
                ->context([
                    'name' => $name,
                    'sender' => $email,
                    'message' => $message,
                    'subject' => $subject,
                    'formatedDate' => $formatedDate,
                ]);

            $this->mailer->send($message);

            //add flash message
            $this->addFlash('success', 'Your message has been sent successfully');
        } else {
            $name = '';
            $email = '';
            $message = '';
        }


        return $this->render('contact/index.html.twig', [
            'controller_name' => 'ContactController',
        ]);
    }
}
