<?php

namespace App\Controller;

use App\Entity\Marque;
use App\Entity\Voiture;
use App\Entity\Utilisateur;
use App\Repository\CovoiturageRepository;
use App\Repository\MarqueRepository;
use App\Repository\AvisRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProfileController extends AbstractController
{
    #[Route('/mon-espace', name: 'legacy_my_space')]
    public function mySpace(
        Request $request,
        EntityManagerInterface $entityManager,
        UtilisateurRepository $utilisateurRepository,
        MarqueRepository $marqueRepository,
        SluggerInterface $slugger,
        UserPasswordHasherInterface $passwordHasher,
        CovoiturageRepository $covoiturageRepository,
        AvisRepository $avisRepository
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof Utilisateur) {
            return $this->redirectToRoute('legacy_sign_in');
        }
        /** @var Utilisateur $user */

        $allowedTabs = ['infos', 'cars', 'trips'];
        $activeTab = in_array($request->query->get('tab'), $allowedTabs, true) ? $request->query->get('tab') : 'infos';

        if ($request->isMethod('POST') && $request->request->has('profile_form')) {
            $nom = trim((string) $request->request->get('nom'));
            $prenom = trim((string) $request->request->get('prenom'));
            $pseudo = trim((string) $request->request->get('pseudo'));
            $email = strtolower(trim((string) $request->request->get('email')));
            $telephone = trim((string) $request->request->get('telephone'));
            $adresse = trim((string) $request->request->get('adresse'));
            $dateNaissanceInput = (string) $request->request->get('date_naissance', '');
            $newPassword = (string) $request->request->get('new_password');
            $newPasswordConfirm = (string) $request->request->get('new_password_confirm');

            if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->addFlash('error', 'Adresse email invalide.');
                return $this->redirectToRoute('legacy_my_space');
            }

            $existingEmail = $utilisateurRepository->findOneBy(['email' => $email]);
            if ($existingEmail && $existingEmail->getId() !== $user->getId()) {
                $this->addFlash('error', 'Cet email est déjà utilisé.');
                return $this->redirectToRoute('legacy_my_space');
            }

            $existingPseudo = $utilisateurRepository->findOneBy(['pseudo' => $pseudo]);
            if ($pseudo !== '' && $existingPseudo && $existingPseudo->getId() !== $user->getId()) {
                $this->addFlash('error', 'Ce pseudo est déjà pris.');
                return $this->redirectToRoute('legacy_my_space');
            }

            if ($newPassword !== '' && $newPassword !== $newPasswordConfirm) {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
                return $this->redirectToRoute('legacy_my_space');
            }

            if ($newPassword !== '' && !$this->isPasswordStrong($newPassword)) {
                $this->addFlash('error', 'Mot de passe trop faible (8+ caractères, majuscules, minuscules, chiffres et caractère spécial).');
                return $this->redirectToRoute('legacy_my_space');
            }

            $user->setNom($nom)
                ->setPrenom($prenom)
                ->setPseudo($pseudo)
                ->setEmail($email)
                ->setTelephone($telephone ?: null)
                ->setAdresse($adresse ?: null);

            if ($dateNaissanceInput !== '') {
                try {
                    $dateN = new \DateTime($dateNaissanceInput);
                    $user->setDateNaissance($dateN);
                } catch (\Exception) {
                    $this->addFlash('error', 'Date de naissance invalide.');
                    return $this->redirectToRoute('legacy_my_space');
                }
            } else {
                $user->setDateNaissance(null);
            }

            if ($newPassword !== '') {
                $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
            }

            $baseSlug = $pseudo !== '' ? strtolower($slugger->slug($pseudo)) : strtolower($slugger->slug($prenom . '-' . $nom));
            $slug = $baseSlug;
            $index = 1;
            while ($utilisateurRepository->findOneBy(['slug' => $slug]) && $utilisateurRepository->findOneBy(['slug' => $slug])->getId() !== $user->getId()) {
                $slug = $baseSlug . '-' . $index;
                ++$index;
            }
            $user->setSlug($slug);

            /** @var \Symfony\Component\HttpFoundation\File\UploadedFile|null $photo */
            $photo = $request->files->get('photo');
            if ($photo instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/profile';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $filename = $slug . '.' . $photo->guessExtension();
                $photo->move($uploadDir, $filename);
                $user->setPhoto('uploads/profile/' . $filename);
            }

            $entityManager->flush();
            $this->addFlash('success', 'Profil mis à jour.');
            return $this->redirectToRoute('legacy_my_space');
        }

        if ($request->isMethod('POST') && $request->request->has('car_form')) {
            $marqueLibelle = trim((string) $request->request->get('marque'));
            $modele = trim((string) $request->request->get('modele'));
            $immatriculation = trim((string) $request->request->get('immatriculation'));
            $energie = trim((string) $request->request->get('energie'));
            $couleur = trim((string) $request->request->get('couleur'));
            $datePremiereImmatriculation = trim((string) $request->request->get('datePremiereImmatriculation'));

            // Vérifie l'unicité de la plaque (insensible à la casse)
            $existingCar = $entityManager->getRepository(Voiture::class)
                ->createQueryBuilder('v')
                ->where('LOWER(v.immatriculation) = :imm')
                ->setParameter('imm', strtolower($immatriculation))
                ->getQuery()
                ->getOneOrNullResult();

            if ($existingCar) {
                if ($existingCar->getProprietaire() && $existingCar->getProprietaire()->getId() === $user->getId()) {
                    $this->addFlash('error', 'Ce véhicule est déjà enregistré dans votre profil.');
                } else {
                    $this->addFlash('error', 'Cette plaque est déjà associée à un autre conducteur.');
                }
                return $this->redirectToRoute('legacy_my_space', ['tab' => 'cars']);
            }

            $marque = $marqueRepository->findOneBy(['libelle' => $marqueLibelle]);
            if (!$marque) {
                $marque = new Marque();
                $marque->setLibelle($marqueLibelle);
                $entityManager->persist($marque);
            }

            $voiture = new Voiture();
            $voiture
                ->setMarque($marque)
                ->setModele($modele)
                ->setImmatriculation($immatriculation)
                ->setEnergie($energie)
                ->setCouleur($couleur)
                ->setDatePremiereImmatriculation($datePremiereImmatriculation)
                ->setProprietaire($user)
                ->setProprietaireNom($user->getNom())
                ->setProprietairePrenom($user->getPrenom())
                ->setProprietairePseudo($user->getPseudo());

            $entityManager->persist($voiture);
            $entityManager->flush();

            $this->addFlash('success', 'Véhicule ajouté à votre profil.');
            return $this->redirectToRoute('legacy_my_space');
        }

        $trips = $covoiturageRepository->createQueryBuilder('c')
            ->leftJoin('c.voiture', 'v')->addSelect('v')
            ->leftJoin('v.proprietaire', 'p')->addSelect('p')
            ->leftJoin('c.participants', 'part')->addSelect('part')
            ->leftJoin('c.avis', 'av')->addSelect('av')
            ->where('p = :user OR part = :user')
            ->setParameter('user', $user)
            ->orderBy('c.dateDepart', 'DESC')
            ->addOrderBy('c.heureDepart', 'DESC')
            ->getQuery()
            ->getResult();

        $tripsPast = [];
        $tripsOngoing = [];
        $tripsFuture = [];
        $pendingEvalTrip = null;
        foreach ($trips as $t) {
            $isDriver = $t->getVoiture()->getProprietaire()->getId() === $user->getId();
            if ($t->getStatut() === 'terminé' && !$isDriver) {
                $alreadyRated = false;
                foreach ($t->getAvis() as $avis) {
                    if ($avis->getUtilisateur() && $avis->getUtilisateur()->getId() === $user->getId()) {
                        $alreadyRated = true;
                        break;
                    }
                }
                if (!$alreadyRated && $pendingEvalTrip === null) {
                    $pendingEvalTrip = $t;
                }
            }
            switch ($t->getStatut()) {
                case 'terminé':
                    $tripsPast[] = $t;
                    break;
                case 'en cours':
                    $tripsOngoing[] = $t;
                    break;
                default:
                    $tripsFuture[] = $t;
                    break;
            }
        }

        $profileAvg = $avisRepository->averageForUser($user);

        return $this->render('legacy/my_space.html.twig', [
            'user' => $user,
            'tripsPast' => $tripsPast,
            'tripsOngoing' => $tripsOngoing,
            'tripsFuture' => $tripsFuture,
            'pendingEvalTrip' => $pendingEvalTrip,
            'profileAvg' => $profileAvg,
            'activeTab' => $activeTab,
        ]);
    }

    #[Route('/mon-espace-chauffeur', name: 'legacy_my_space_chauffeur')]
    public function mySpaceChauffeur(): Response
    {
        return $this->file($this->getParameter('kernel.project_dir') . '/public/legacy/my_space_chauffeur.html');
    }

    #[Route('/form-chauffeur', name: 'legacy_form_chauffeur')]
    public function formChauffeur(): Response
    {
        return $this->render('legacy/form_chauffeur.html.twig');
    }

    #[Route('/credits/topup', name: 'legacy_topup', methods: ['POST'])]
    public function topupCredits(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Utilisateur) {
            return $this->redirectToRoute('legacy_sign_in');
        }
        /** @var Utilisateur $user */

        $token = (string) $request->request->get('_csrf_token');
        if (!$this->isCsrfTokenValid('topup', $token)) {
            $this->addFlash('error', 'Session expirée, veuillez réessayer.');
            return $this->redirectToRoute('legacy_home');
        }

        $credits = (int) $request->request->get('credits', 0);
        if ($credits <= 0) {
            $this->addFlash('error', 'Montant de recharge invalide.');
            return $this->redirect($request->headers->get('referer') ?: $this->generateUrl('legacy_home'));
        }

        $user->addCredit($credits);
        $entityManager->flush();

        if ($user->getEmail()) {
            $euros = $credits * 2;
            $emailMessage = (new Email())
                ->from('support@ecoride.test')
                ->to($user->getEmail())
                ->subject('[Support EcoRide] Recharge de crédits confirmée')
                ->html(sprintf(
                    '<p>Support EcoRide</p><p>Bonjour %s,</p><p>Votre recharge a été validée.</p><ul><li>Montant crédité : %d crédits</li><li>Montant payé (fictif) : %.2f €</li><li>Nouveau solde : %d crédits</li></ul>',
                    htmlspecialchars($user->getPrenom() ?? $user->getPseudo() ?? ''),
                    $credits,
                    $euros,
                    $user->getCredit()
                ));
            $mailer->send($emailMessage);
        }

        $this->addFlash('success', sprintf('Recharge simulée : +%d crédits ajoutés.', $credits));
        return $this->redirect($request->headers->get('referer') ?: $this->generateUrl('legacy_home'));
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
