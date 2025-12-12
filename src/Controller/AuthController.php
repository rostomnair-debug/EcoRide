<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\String\Slugger\SluggerInterface;

class AuthController extends AbstractController
{
    #[Route('/login', name: 'legacy_login', methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UtilisateurRepository $utilisateurRepository,
        SluggerInterface $slugger,
        UserPasswordHasherInterface $passwordHasher,
        MailerInterface $mailer
    ): Response {
        if ($request->isMethod('POST')) {
            $nom = trim((string) $request->request->get('name', ''));
            $prenom = trim((string) $request->request->get('firstname', ''));
            $pseudo = trim((string) $request->request->get('pseudo', ''));
            $email = strtolower(trim((string) $request->request->get('email', '')));
            $password = (string) $request->request->get('password', '');
            $confirm = (string) $request->request->get('confirm_password', '');
            $acceptedCgu = $request->request->getBoolean('accept_cgu');

            if ($nom === '' || $prenom === '' || $pseudo === '' || $email === '' || $password === '') {
                $this->addFlash('error', 'Merci de remplir tous les champs obligatoires.');
                return $this->redirectToRoute('legacy_login');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->addFlash('error', 'Adresse email invalide.');
                return $this->redirectToRoute('legacy_login');
            }

            if ($password !== $confirm) {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
                return $this->redirectToRoute('legacy_login');
            }

            if (!$this->isPasswordStrong($password)) {
                $this->addFlash('error', 'Mot de passe trop faible (8+ caractères, majuscules, minuscules, chiffres et caractère spécial).');
                return $this->redirectToRoute('legacy_login');
            }

            if ($utilisateurRepository->findOneBy(['pseudo' => $pseudo])) {
                $this->addFlash('error', 'Ce pseudo est déjà utilisé.');
                return $this->redirectToRoute('legacy_login');
            }

            if ($utilisateurRepository->findOneBy(['email' => $email])) {
                $this->addFlash('error', 'Un compte existe déjà avec cet email.');
                return $this->redirectToRoute('legacy_login');
            }

            if (!$acceptedCgu) {
                $this->addFlash('error', "Merci d'accepter les conditions generales d'utilisation.");
                return $this->redirectToRoute('legacy_login');
            }

            $baseSlug = strtolower($slugger->slug($pseudo));
            $slug = $baseSlug;
            $index = 1;
            while ($utilisateurRepository->findOneBy(['slug' => $slug]) !== null) {
                $slug = $baseSlug . '-' . $index;
                ++$index;
            }

            $utilisateur = new Utilisateur();
            $utilisateur
                ->setNom($nom)
                ->setPrenom($prenom)
                ->setPseudo($pseudo)
                ->setRole('user')
                ->setEmail($email)
                ->setPassword($passwordHasher->hashPassword($utilisateur, $password))
                ->setSlug($slug)
                ->setVerifie(false)
                ->setVerificationToken(bin2hex(random_bytes(32)));

            $entityManager->persist($utilisateur);
            $entityManager->flush();

            $verifyUrl = $this->generateUrl('legacy_verify_email', ['token' => $utilisateur->getVerificationToken()], UrlGeneratorInterface::ABSOLUTE_URL);
            $emailMessage = (new Email())
                ->from('support@ecoride.test')
                ->to($utilisateur->getEmail())
                ->subject('[Support EcoRide] Vérifiez votre adresse email')
                ->html(sprintf(
                    '<p>Support EcoRide</p><p>Bonjour %s,</p><p>Merci de rejoindre EcoRide. Cliquez pour vérifier votre adresse :</p><p><a href="%s">%s</a></p>',
                    htmlspecialchars($utilisateur->getPrenom() ?? ''),
                    $verifyUrl,
                    $verifyUrl
                ));
            $mailer->send($emailMessage);

            $this->addFlash('success', 'Votre compte a été créé. Vérifiez votre boîte mail pour valider votre adresse, puis connectez-vous.');

            return $this->redirectToRoute('legacy_sign_in');
        }

        return $this->render('legacy/login.html.twig');
    }

    #[Route('/sign-in', name: 'legacy_sign_in', methods: ['GET', 'POST'])]
    public function signIn(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('legacy/sign_in.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'legacy_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method is intercepted by the firewall logout.');
    }

    #[Route('/cgu', name: 'legacy_cgu', methods: ['GET'])]
    public function cgu(): Response
    {
        return $this->render('legacy/cgu.html.twig');
    }

    #[Route('/confidentialite', name: 'legacy_privacy', methods: ['GET'])]
    public function privacy(): Response
    {
        return $this->render('legacy/privacy.html.twig');
    }

    #[Route('/verify-email', name: 'legacy_verify_email')]
    public function verifyEmail(Request $request, EntityManagerInterface $entityManager, UtilisateurRepository $utilisateurRepository): Response
    {
        $token = (string) $request->query->get('token', '');
        if ($token === '') {
            $this->addFlash('error', 'Lien de vérification invalide.');
            return $this->redirectToRoute('legacy_sign_in');
        }

        $user = $utilisateurRepository->findOneBy(['verificationToken' => $token]);
        if (!$user) {
            $this->addFlash('error', 'Lien de vérification invalide ou expiré.');
            return $this->redirectToRoute('legacy_sign_in');
        }

        $user->setVerifie(true)->setVerificationToken(null);
        $entityManager->flush();

        $this->addFlash('success', 'Adresse email vérifiée. Bienvenue !');
        return $this->redirectToRoute('legacy_my_space');
    }

    #[Route('/reset-password', name: 'legacy_reset_password', methods: ['GET', 'POST'])]
    public function resetPassword(Request $request, UtilisateurRepository $utilisateurRepository, EntityManagerInterface $entityManager, MailerInterface $mailer, SluggerInterface $slugger): Response
    {
        if ($request->isMethod('POST')) {
            $email = strtolower(trim((string) $request->request->get('email', '')));
            if ($email === '') {
                $this->addFlash('error', 'Merci de renseigner votre email.');
                return $this->redirectToRoute('legacy_reset_password');
            }

            $user = $utilisateurRepository->findOneBy(['email' => $email]);
            if ($user) {
                $token = bin2hex(random_bytes(32));
                $user->setResetToken($token);
                $user->setResetTokenExpiresAt((new \DateTime())->modify('+1 hour'));
                $entityManager->flush();

                $resetUrl = $this->generateUrl('legacy_reset_password_confirm', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
                $emailMessage = (new Email())
                    ->from('support@ecoride.test')
                    ->to($user->getEmail())
                    ->subject('[Support EcoRide] Réinitialisation de votre mot de passe')
                    ->html(sprintf(
                        '<p>Support EcoRide</p><p>Bonjour %s,</p><p>Vous avez demandé à réinitialiser votre mot de passe. Cliquez sur le lien :</p><p><a href="%s">%s</a></p>',
                        htmlspecialchars($user->getPrenom() ?? $user->getPseudo() ?? ''),
                        $resetUrl,
                        $resetUrl
                    ));
                $mailer->send($emailMessage);
            }

            $this->addFlash('success', 'Si un compte existe, un email de réinitialisation a été envoyé.');
            return $this->redirectToRoute('legacy_reset_password');
        }

        return $this->render('legacy/reset_password_request.html.twig');
    }

    #[Route('/reset-password/{token}', name: 'legacy_reset_password_confirm', methods: ['GET', 'POST'])]
    public function resetPasswordConfirm(string $token, Request $request, UtilisateurRepository $utilisateurRepository, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $utilisateurRepository->findOneBy(['resetToken' => $token]);
        if (!$user || !$user->getResetTokenExpiresAt() || $user->getResetTokenExpiresAt() < new \DateTime()) {
            $this->addFlash('error', 'Lien invalide ou expiré.');
            return $this->redirectToRoute('legacy_reset_password');
        }

        if ($request->isMethod('POST')) {
            $password = (string) $request->request->get('password', '');
            $confirm = (string) $request->request->get('confirm_password', '');
            if ($password === '' || $confirm === '' || $password !== $confirm) {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
                return $this->redirectToRoute('legacy_reset_password_confirm', ['token' => $token]);
            }
            if (!$this->isPasswordStrong($password)) {
                $this->addFlash('error', 'Mot de passe trop faible (8+ caractères, majuscules, minuscules, chiffres et caractère spécial).');
                return $this->redirectToRoute('legacy_reset_password_confirm', ['token' => $token]);
            }

            $user->setPassword($passwordHasher->hashPassword($user, $password));
            $user->setResetToken(null);
            $user->setResetTokenExpiresAt(null);
            $entityManager->flush();

            $this->addFlash('success', 'Mot de passe mis à jour. Vous pouvez vous connecter.');
            return $this->redirectToRoute('legacy_sign_in');
        }

        return $this->render('legacy/reset_password_confirm.html.twig', ['token' => $token]);
    }

    private function isPasswordStrong(string $password): bool
    {
        return strlen($password) >= 8
            && preg_match('/[A-Z]/', $password)
            && preg_match('/[a-z]/', $password)
            && preg_match('/[0-9]/', $password)
            && preg_match('/[^A-Za-z0-9]/', $password);
    }
}
