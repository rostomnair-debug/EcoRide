<?php

namespace App\Controller;

use App\Repository\AvisRepository;
use App\Repository\CovoiturageRepository;
use App\Repository\SupportMessageRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmployeeController extends AbstractController
{
    public function __construct(private RequestStack $requestStack, private EntityManagerInterface $em)
    {
    }

    #[Route('/avis/{id}/approve', name: 'legacy_avis_approve', methods: ['POST'])]
    public function approveAvis(int $id, AvisRepository $avisRepository, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');
        $avis = $avisRepository->find($id);
        if ($avis) {
            $avis->setStatut('valide');
            $entityManager->flush();
            if ($avis->getRatedUser()) {
                $avg = $avisRepository->averageForUser($avis->getRatedUser());
                $avis->getRatedUser()->setNoteMoyenne($avg);
                $entityManager->flush();
            }
            $this->addFlash('success', 'Avis validé.');
        }
        return $this->redirectToRoute('legacy_employee_space');
    }

    #[Route('/avis/{id}/reject', name: 'legacy_avis_reject', methods: ['POST'])]
    public function rejectAvis(int $id, AvisRepository $avisRepository, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');
        $avis = $avisRepository->find($id);
        if ($avis) {
            $avis->setStatut('refuse');
            $entityManager->flush();
            if ($avis->getRatedUser()) {
                $avg = $avisRepository->averageForUser($avis->getRatedUser());
                $avis->getRatedUser()->setNoteMoyenne($avg);
                $entityManager->flush();
            }
            $this->addFlash('success', 'Avis refusé.');
        }
        return $this->redirectToRoute('legacy_employee_space');
    }

    #[Route('/signalement/avis/{id}/resolve', name: 'legacy_signal_avis_resolve', methods: ['POST'])]
    public function resolveSignalAvis(int $id, Request $request, AvisRepository $avisRepository, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');
        $avis = $avisRepository->find($id);
        if ($avis && $this->isCsrfTokenValid('resolve_avis_' . $id, (string) $request->request->get('_csrf_token'))) {
            $avis->setSignale(false);
            $entityManager->flush();
            $this->addFlash('success', 'Signalement d\'avis traité.');
        }
        return $this->redirectToRoute('legacy_employee_space');
    }

    #[Route('/signalement/covoiturage/{id}/resolve', name: 'legacy_signal_covoit_resolve', methods: ['POST'])]
    public function resolveSignalCovoit(int $id, Request $request, CovoiturageRepository $covoiturageRepository, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');
        $trajet = $covoiturageRepository->find($id);
        if ($trajet && $this->isCsrfTokenValid('resolve_covoit_' . $id, (string) $request->request->get('_csrf_token'))) {
            $trajet->setSignale(false);
            $entityManager->flush();
            $this->addFlash('success', 'Signalement de trajet traité.');
        }
        return $this->redirectToRoute('legacy_employee_space');
    }

    #[Route('/espace-employe', name: 'legacy_employee_space')]
    public function employeeSpace(
        AvisRepository $avisRepository,
        CovoiturageRepository $covoiturageRepository,
        UtilisateurRepository $utilisateurRepository,
        SupportMessageRepository $supportMessageRepository
    ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');
        $pendingPage = max(1, (int) $this->getParameterFromRequest('pagePending'));
        $pendingSize = 6;
        $pendingQb = $avisRepository->createQueryBuilder('a')
            ->leftJoin('a.utilisateur', 'u')->addSelect('u')
            ->leftJoin('a.covoiturage', 'c')->addSelect('c')
            ->where('a.statut = :statut')
            ->setParameter('statut', 'en attente')
            ->orderBy('a.id', 'DESC');
        $pendingTotal = (int) $pendingQb->select('COUNT(a.id)')->getQuery()->getSingleScalarResult();
        $pendingPages = max(1, (int) ceil($pendingTotal / $pendingSize));
        if ($pendingPage > $pendingPages) {
            $pendingPage = $pendingPages;
        }
        $pending = $pendingQb
            ->select('a', 'u', 'c')
            ->setFirstResult(($pendingPage - 1) * $pendingSize)
            ->setMaxResults($pendingSize)
            ->getQuery()
            ->getResult();

        $signaledAvisPage = max(1, (int) $this->getParameterFromRequest('pageAvisSignal'));
        $signaledAvisSize = 6;
        $signaledSort = (string) $this->getParameterFromRequest('signaledAvisSort', 'id');
        $signaledDir = strtolower((string) $this->getParameterFromRequest('signaledAvisDir', 'desc')) === 'asc' ? 'ASC' : 'DESC';
        $signaledAvisQb = $avisRepository->createQueryBuilder('a')
            ->leftJoin('a.utilisateur', 'u')->addSelect('u')
            ->leftJoin('a.covoiturage', 'c')->addSelect('c')
            ->where('a.statut != :pending')
            ->setParameter('pending', 'en attente');
        $orderField = $signaledSort === 'note' ? 'a.note' : 'a.id';
        $signaledAvisQb->orderBy($orderField, $signaledDir);
        $signaledAvisTotal = (int) $signaledAvisQb->select('COUNT(a.id)')->getQuery()->getSingleScalarResult();
        $signaledAvisPages = max(1, (int) ceil($signaledAvisTotal / $signaledAvisSize));
        if ($signaledAvisPage > $signaledAvisPages) {
            $signaledAvisPage = $signaledAvisPages;
        }
        $signaledAvis = $signaledAvisQb
            ->select('a', 'u', 'c')
            ->setFirstResult(($signaledAvisPage - 1) * $signaledAvisSize)
            ->setMaxResults($signaledAvisSize)
            ->getQuery()
            ->getResult();

        $signaledCovoitPage = max(1, (int) $this->getParameterFromRequest('pageCovoitSignal'));
        $signaledCovoitSize = 6;
        $signaledCovoitQb = $covoiturageRepository->createQueryBuilder('c')
            ->leftJoin('c.voiture', 'v')->addSelect('v')
            ->leftJoin('v.proprietaire', 'p')->addSelect('p')
            ->where('c.signale = 1')
            ->orderBy('c.dateDepart', 'DESC')
            ->addOrderBy('c.heureDepart', 'DESC');
        $signaledCovoitTotal = (int) $signaledCovoitQb->select('COUNT(c.id)')->getQuery()->getSingleScalarResult();
        $signaledCovoitPages = max(1, (int) ceil($signaledCovoitTotal / $signaledCovoitSize));
        if ($signaledCovoitPage > $signaledCovoitPages) {
            $signaledCovoitPage = $signaledCovoitPages;
        }
        $signaledCovoits = $signaledCovoitQb
            ->select('c', 'v', 'p')
            ->setFirstResult(($signaledCovoitPage - 1) * $signaledCovoitSize)
            ->setMaxResults($signaledCovoitSize)
            ->getQuery()
            ->getResult();

        $historyAvisPage = max(1, (int) $this->getParameterFromRequest('pageHistoryAvis'));
        $historyAvisSize = 6;
        $historyAvisQb = $avisRepository->createQueryBuilder('a')
            ->leftJoin('a.utilisateur', 'u')->addSelect('u')
            ->leftJoin('a.covoiturage', 'c')->addSelect('c')
            ->where('a.signale = 0 AND a.motifSignalement IS NOT NULL')
            ->orderBy('a.id', 'DESC');
        $historyAvisTotal = (int) $historyAvisQb->select('COUNT(a.id)')->getQuery()->getSingleScalarResult();
        $historyAvisPages = max(1, (int) ceil($historyAvisTotal / $historyAvisSize));
        if ($historyAvisPage > $historyAvisPages) {
            $historyAvisPage = $historyAvisPages;
        }
        $historyAvis = $historyAvisQb
            ->select('a', 'u', 'c')
            ->setFirstResult(($historyAvisPage - 1) * $historyAvisSize)
            ->setMaxResults($historyAvisSize)
            ->getQuery()
            ->getResult();

        $historyCovoitPage = max(1, (int) $this->getParameterFromRequest('pageHistoryCovoit'));
        $historyCovoitSize = 6;
        $historyCovoitQb = $covoiturageRepository->createQueryBuilder('c')
            ->leftJoin('c.voiture', 'v')->addSelect('v')
            ->leftJoin('v.proprietaire', 'p')->addSelect('p')
            ->where('c.signale = 0 AND c.motifSignalement IS NOT NULL')
            ->orderBy('c.dateDepart', 'DESC')
            ->addOrderBy('c.heureDepart', 'DESC');
        $historyCovoitTotal = (int) $historyCovoitQb->select('COUNT(c.id)')->getQuery()->getSingleScalarResult();
        $historyCovoitPages = max(1, (int) ceil($historyCovoitTotal / $historyCovoitSize));
        if ($historyCovoitPage > $historyCovoitPages) {
            $historyCovoitPage = $historyCovoitPages;
        }
        $historyCovoits = $historyCovoitQb
            ->select('c', 'v', 'p')
            ->setFirstResult(($historyCovoitPage - 1) * $historyCovoitSize)
            ->setMaxResults($historyCovoitSize)
            ->getQuery()
            ->getResult();

        $usersPage = max(1, (int) $this->getParameterFromRequest('pageUsers'));
        $usersSize = 6;
        $userSearch = trim((string) $this->getParameterFromRequest('userSearch', ''));
        $usersQb = $utilisateurRepository->createQueryBuilder('u')
            ->where('u.role = :roleUser')
            ->setParameter('roleUser', 'user')
            ->orderBy('u.id', 'DESC');
        if ($userSearch !== '') {
            $usersQb->andWhere('LOWER(u.pseudo) LIKE :uq OR LOWER(u.email) LIKE :uq OR LOWER(u.nom) LIKE :uq OR LOWER(u.prenom) LIKE :uq')
                ->setParameter('uq', '%' . strtolower($userSearch) . '%');
        }
        $usersTotal = (int) $usersQb->select('COUNT(u.id)')->getQuery()->getSingleScalarResult();
        $usersPages = max(1, (int) ceil($usersTotal / $usersSize));
        if ($usersPage > $usersPages) {
            $usersPage = $usersPages;
        }
        $users = $usersQb
            ->select('u')
            ->setFirstResult(($usersPage - 1) * $usersSize)
            ->setMaxResults($usersSize)
            ->getQuery()
            ->getResult();

        $supportPage = max(1, (int) $this->getParameterFromRequest('pageSupport'));
        $supportSize = 6;
        $supportTotal = (int) $supportMessageRepository->createQueryBuilder('s')->select('COUNT(s.id)')->getQuery()->getSingleScalarResult();
        $supportPages = max(1, (int) ceil($supportTotal / $supportSize));
        if ($supportPage > $supportPages) {
            $supportPage = $supportPages;
        }
        $supportMessages = $supportMessageRepository->createQueryBuilder('s')
            ->orderBy('s.createdAt', 'DESC')
            ->setFirstResult(($supportPage - 1) * $supportSize)
            ->setMaxResults($supportSize)
            ->getQuery()
            ->getResult();

        return $this->render('legacy/employee_space.html.twig', [
            'pendingAvis' => $pending,
            'signaledAvis' => $signaledAvis,
            'signaledCovoits' => $signaledCovoits,
            'historyAvis' => $historyAvis,
            'historyCovoits' => $historyCovoits,
            'pendingPage' => $pendingPage,
            'pendingPages' => $pendingPages,
            'signaledAvisPage' => $signaledAvisPage,
            'signaledAvisPages' => $signaledAvisPages,
            'signaledCovoitPage' => $signaledCovoitPage,
            'signaledCovoitPages' => $signaledCovoitPages,
            'historyAvisPage' => $historyAvisPage,
            'historyAvisPages' => $historyAvisPages,
            'historyCovoitPage' => $historyCovoitPage,
            'historyCovoitPages' => $historyCovoitPages,
            'users' => $users,
            'usersPage' => $usersPage,
            'usersPages' => $usersPages,
            'userSearch' => $userSearch,
            'supportMessages' => $supportMessages,
            'supportPage' => $supportPage,
            'supportPages' => $supportPages,
        ]);
    }

    #[Route('/espace-employe/support/{id}/reply', name: 'legacy_employee_reply_support', methods: ['POST'])]
    public function replySupport(
        int $id,
        Request $request,
        SupportMessageRepository $supportMessageRepository,
        MailerInterface $mailer
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');
        $message = $supportMessageRepository->find($id);
        if (!$message) {
            $this->addFlash('error', 'Message introuvable.');
            return $this->redirectToRoute('legacy_employee_space', ['tab' => 'support']);
        }
        if (!$this->isCsrfTokenValid('reply_support_' . $id, (string) $request->request->get('_csrf_token'))) {
            $this->addFlash('error', 'Session expirée.');
            return $this->redirectToRoute('legacy_employee_space', ['tab' => 'support']);
        }
        $reply = trim((string) $request->request->get('reply', ''));
        if ($reply === '') {
            $this->addFlash('error', 'Réponse vide.');
            return $this->redirectToRoute('legacy_employee_space', ['tab' => 'support']);
        }
        $replyHtml = $this->markdownToHtml($reply);
        $email = (new Email())
            ->from('support@ecoride.test')
            ->to($message->getEmail())
            ->subject('[EcoRide] Réponse support : ' . $message->getSubject())
            ->html(sprintf(
                '<p>Bonjour %s,</p>%s<hr><p><strong>Votre message initial :</strong><br>%s</p><p>Support EcoRide</p>',
                htmlspecialchars($message->getName()),
                $replyHtml,
                nl2br(htmlspecialchars($message->getMessage()))
            ));
        $mailer->send($email);

        $message->setReplied(true)
            ->setRepliedAt(new \DateTime())
            ->setReplyContent($reply);
        $this->em->flush();
        $this->addFlash('success', 'Réponse envoyée.');
        return $this->redirectToRoute('legacy_employee_space', ['tab' => 'support']);
    }

    #[Route('/espace-employe/utilisateur/new', name: 'legacy_employee_add_user_form', methods: ['GET'])]
    public function addUserForm(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');
        return $this->render('legacy/employee_user_new.html.twig');
    }

    #[Route('/espace-employe/utilisateur/add', name: 'legacy_employee_add_user', methods: ['POST'])]
    public function addUser(Request $request, UtilisateurRepository $utilisateurRepository, UserPasswordHasherInterface $hasher, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');
        $email = strtolower(trim((string) $request->request->get('email', '')));
        $pseudo = trim((string) $request->request->get('pseudo', ''));
        $nom = trim((string) $request->request->get('nom', ''));
        $prenom = trim((string) $request->request->get('prenom', ''));
        $credit = max(0, (int) $request->request->get('credit', 20));
        $password = (string) $request->request->get('password', '');
        if ($email === '' || $pseudo === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->addFlash('error', 'Email ou pseudo invalide.');
            return $this->redirectToRoute('legacy_employee_add_user_form');
        }
        if (!$this->isCsrfTokenValid('add_user', (string) $request->request->get('_csrf_token'))) {
            $this->addFlash('error', 'Session expirée.');
            return $this->redirectToRoute('legacy_employee_add_user_form');
        }
        if ($utilisateurRepository->findOneBy(['email' => $email])) {
            $this->addFlash('error', 'Un compte existe déjà avec cet email.');
            return $this->redirectToRoute('legacy_employee_add_user_form');
        }
        $user = new \App\Entity\Utilisateur();
        $rawPassword = $password !== '' ? $password : 'User123$';
        $user->setEmail($email)
            ->setPseudo($pseudo)
            ->setNom($nom === '' ? $pseudo : $nom)
            ->setPrenom($prenom === '' ? $pseudo : $prenom)
            ->setRole('user')
            ->setCredit($credit)
            ->setVerifie(true)
            ->setSlug($this->uniqueSlug($slugger, $pseudo))
            ->setPassword($hasher->hashPassword($user, $rawPassword));
        $this->em->persist($user);
        $this->em->flush();
        $this->addFlash('success', 'Utilisateur créé.');
        return $this->redirectToRoute('legacy_employee_space', ['pageUsers' => 1]);
    }

    #[Route('/espace-employe/utilisateur/{id}/edit', name: 'legacy_employee_edit_user', methods: ['GET', 'POST'])]
    public function editUser(int $id, Request $request, UtilisateurRepository $utilisateurRepository, UserPasswordHasherInterface $hasher): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');
        $user = $utilisateurRepository->find($id);
        if (!$user || $user->getRole() !== 'user') {
            $this->addFlash('error', 'Utilisateur introuvable.');
            return $this->redirectToRoute('legacy_employee_space');
        }
        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('edit_user_' . $user->getId(), (string) $request->request->get('_csrf_token'))) {
                $this->addFlash('error', 'Session expirée.');
                return $this->redirectToRoute('legacy_employee_edit_user', ['id' => $id]);
            }
            $email = strtolower(trim((string) $request->request->get('email', '')));
            $pseudo = trim((string) $request->request->get('pseudo', ''));
            $nom = trim((string) $request->request->get('nom', ''));
            $prenom = trim((string) $request->request->get('prenom', ''));
            $password = (string) $request->request->get('password', '');
            $credit = max(0, (int) $request->request->get('credit', $user->getCredit()));

            if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->addFlash('error', 'Email invalide.');
                return $this->redirectToRoute('legacy_employee_edit_user', ['id' => $id]);
            }
            if ($pseudo === '') {
                $this->addFlash('error', 'Pseudo obligatoire.');
                return $this->redirectToRoute('legacy_employee_edit_user', ['id' => $id]);
            }
            $existing = $utilisateurRepository->findOneBy(['email' => $email]);
            if ($existing && $existing->getId() !== $user->getId()) {
                $this->addFlash('error', 'Email déjà utilisé.');
                return $this->redirectToRoute('legacy_employee_edit_user', ['id' => $id]);
            }

            $user->setEmail($email)
                ->setPseudo($pseudo)
                ->setNom($nom === '' ? $user->getNom() : $nom)
                ->setPrenom($prenom === '' ? $user->getPrenom() : $prenom)
                ->setCredit($credit);
            if ($request->request->get('remove_photo') === '1') {
                $user->setPhoto(null);
            }
            if ($password !== '') {
                $user->setPassword($hasher->hashPassword($user, $password));
            }
            $this->em->flush();
            $this->addFlash('success', 'Profil utilisateur mis à jour.');
            return $this->redirectToRoute('legacy_employee_space', ['pageUsers' => 1]);
        }

        return $this->render('legacy/employee_user_edit.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/espace-employe/utilisateur/{id}/delete', name: 'legacy_employee_delete_user', methods: ['POST'])]
    public function deleteUser(int $id, Request $request, UtilisateurRepository $utilisateurRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');
        $user = $utilisateurRepository->find($id);
        if ($user && $user->getRole() === 'user' && $this->isCsrfTokenValid('delete_user_' . $id, (string) $request->request->get('_csrf_token'))) {
            $this->em->remove($user);
            $this->em->flush();
            $this->addFlash('success', 'Utilisateur supprimé.');
        }
        return $this->redirectToRoute('legacy_employee_space');
    }

    #[Route('/espace-employe/utilisateur/{id}/remove-photo', name: 'legacy_employee_remove_photo', methods: ['POST'])]
    public function removeUserPhoto(int $id, Request $request, UtilisateurRepository $utilisateurRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');
        $user = $utilisateurRepository->find($id);
        if ($user && $user->getRole() === 'user' && $this->isCsrfTokenValid('remove_photo_' . $id, (string) $request->request->get('_csrf_token'))) {
            $user->setPhoto(null);
            $this->em->flush();
            $this->addFlash('success', 'Photo de profil supprimée.');
        } else {
            $this->addFlash('error', 'Impossible de supprimer la photo.');
        }
        return $this->redirectToRoute('legacy_employee_space', ['tab' => 'users']);
    }

    #[Route('/espace-employe/utilisateur/{id}/credit', name: 'legacy_employee_grant_credit', methods: ['POST'])]
    public function grantCredit(int $id, Request $request, UtilisateurRepository $utilisateurRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');
        $user = $utilisateurRepository->find($id);
        $amount = max(0, (int) $request->request->get('amount', 0));
        if ($user && $user->getRole() === 'user' && $amount > 0 && $this->isCsrfTokenValid('credit_user_' . $id, (string) $request->request->get('_csrf_token'))) {
            $user->setCredit($user->getCredit() + $amount);
            $this->em->flush();
            $this->addFlash('success', 'Crédits octroyés (+'.$amount.').');
        }
        return $this->redirectToRoute('legacy_employee_space', ['pageUsers' => $this->getParameterFromRequest('pageUsers')]);
    }

    private function getParameterFromRequest(string $name, mixed $default = 1): mixed
    {
        $request = $this->requestStack->getCurrentRequest();
        return $request?->get($name, $default);
    }

    private function markdownToHtml(string $text): string
    {
        $escaped = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $escaped = preg_replace('/^#{3}\s*(.+)$/m', '<h3>$1</h3>', $escaped);
        $escaped = preg_replace('/^#{2}\s*(.+)$/m', '<h2>$1</h2>', $escaped);
        $escaped = preg_replace('/^#\s*(.+)$/m', '<h1>$1</h1>', $escaped);
        $escaped = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $escaped);
        $escaped = preg_replace('/_(.+?)_/s', '<em>$1</em>', $escaped);
        $escaped = preg_replace('/\[(.+?)\]\((https?:\/\/[^\s)]+)\)/i', '<a href="$2" rel="noopener noreferrer">$1</a>', $escaped);
        $escaped = preg_replace('/^>\s?(.*)$/m', '<blockquote>$1</blockquote>', $escaped);
        $escaped = nl2br($escaped);
        return '<div>' . $escaped . '</div>';
    }

    private function uniqueSlug(SluggerInterface $slugger, string $base): string
    {
        $repo = $this->em->getRepository(\App\Entity\Utilisateur::class);
        $slug = strtolower($slugger->slug($base));
        $i = 1;
        while ($repo->findOneBy(['slug' => $slug])) {
            $slug = strtolower($slugger->slug($base . '-' . $i));
            ++$i;
        }
        return $slug;
    }
}
