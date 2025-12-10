<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\SupportMessage;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'legacy_contact', methods: ['GET', 'POST'])]
    public function __invoke(Request $request, MailerInterface $mailer, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $name = trim((string) $request->request->get('name', ''));
            $email = trim((string) $request->request->get('email', ''));
            $pseudo = trim((string) $request->request->get('pseudo', ''));
            $subject = trim((string) $request->request->get('subject', ''));
            $message = trim((string) $request->request->get('message', ''));

            if ($name === '' || $email === '' || $subject === '' || $message === '') {
                $this->addFlash('error', 'Merci de remplir tous les champs obligatoires.');
                return $this->redirectToRoute('legacy_contact');
            }

            $emailMessage = (new Email())
                ->from('support@ecoride.test')
                ->to('support@ecoride.test')
                ->subject('[EcoRide] Contact : ' . $subject)
                ->replyTo($email)
                ->html(sprintf(
                    '<p>Support EcoRide</p><p><strong>Nom :</strong> %s<br><strong>Pseudo :</strong> %s<br><strong>Email :</strong> %s</p><p><strong>Objet :</strong> %s</p><p><strong>Message :</strong><br>%s</p>',
                    htmlspecialchars($name),
                    htmlspecialchars($pseudo),
                    htmlspecialchars($email),
                    htmlspecialchars($subject),
                    nl2br(htmlspecialchars($message))
                ));

            $mailer->send($emailMessage);
            $this->addFlash('success', 'Votre message a été envoyé au support EcoRide.');

            $supportMsg = (new SupportMessage())
                ->setName($name)
                ->setEmail($email)
                ->setPseudo($pseudo)
                ->setSubject($subject)
                ->setMessage($message)
                ->setCreatedAt(new \DateTime());
            $em->persist($supportMsg);
            $em->flush();

            return $this->redirectToRoute('legacy_contact');
        }

        return $this->render('legacy/contact.html.twig');
    }
}
