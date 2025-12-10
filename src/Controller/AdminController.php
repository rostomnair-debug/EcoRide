<?php

namespace App\Controller;

use App\Repository\AvisRepository;
use App\Repository\CovoiturageRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminController extends AbstractController
{
    public function __construct(private RequestStack $requestStack, private EntityManagerInterface $em)
    {
    }

    #[Route('/admin/user/{id}/toggle-suspend', name: 'legacy_admin_toggle_suspend', methods: ['POST'])]
    public function toggleSuspendUser(int $id, UtilisateurRepository $utilisateurRepository, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $target = $utilisateurRepository->find($id);
        if ($target) {
            $target->setSuspended(!$target->isSuspended());
            $entityManager->flush();
            $this->addFlash('success', 'Statut de suspension mis à jour.');
        }
        return $this->redirectToRoute('legacy_admin_dashboard');
    }

    #[Route('/admin/user/{id}/promote-employe', name: 'legacy_admin_promote_employe', methods: ['POST'])]
    public function promoteEmploye(int $id, UtilisateurRepository $utilisateurRepository, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $target = $utilisateurRepository->find($id);
        if ($target) {
            $target->setRole('employe');
            $entityManager->flush();
            $this->addFlash('success', 'Utilisateur promu employé.');
        }
        return $this->redirectToRoute('legacy_admin_dashboard');
    }

    #[Route('/admin-dashboard', name: 'legacy_admin_dashboard')]
    public function adminDashboard(
        UtilisateurRepository $utilisateurRepository,
        CovoiturageRepository $covoiturageRepository,
        AvisRepository $avisRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $page = max(1, (int) $this->getParameterFromRequest('pageUsers'));
        $userSort = (string) $this->getParameterFromRequest('userSort');
        $userSearch = trim((string) $this->getParameterFromRequest('userSearch', ''));
        $allowedSort = ['id', 'pseudo', 'email', 'role'];
        if (!in_array($userSort, $allowedSort, true)) {
            $userSort = 'id';
        }
        $userDir = (string) $this->getParameterFromRequest('userDir');
        $userDir = strtolower($userDir) === 'asc' ? 'ASC' : 'DESC';
        $pageSize = 6;
        $usersQb = $utilisateurRepository->createQueryBuilder('u')
            ->where('u.role = :roleUser')
            ->setParameter('roleUser', 'user')
            ->orderBy('u.' . $userSort, $userDir);
        if ($userSearch !== '') {
            $usersQb->andWhere('LOWER(u.pseudo) LIKE :q OR LOWER(u.email) LIKE :q OR LOWER(u.nom) LIKE :q OR LOWER(u.prenom) LIKE :q')
                ->setParameter('q', '%' . strtolower($userSearch) . '%');
        }
        $usersTotal = (int) $usersQb->select('COUNT(u.id)')->getQuery()->getSingleScalarResult();
        $usersTotalPages = max(1, (int) ceil($usersTotal / $pageSize));
        if ($page > $usersTotalPages) {
            $page = $usersTotalPages;
        }
        $users = $usersQb
            ->select('u')
            ->setFirstResult(($page - 1) * $pageSize)
            ->setMaxResults($pageSize)
            ->getQuery()
            ->getResult();

        $empPage = max(1, (int) $this->getParameterFromRequest('pageEmployees'));
        $empSize = 6;
        $empSearch = trim((string) $this->getParameterFromRequest('employeeSearch', ''));
        $employeesQb = $utilisateurRepository->createQueryBuilder('u')
            ->where('u.role = :roleEmploye')
            ->setParameter('roleEmploye', 'employe')
            ->orderBy('u.id', 'DESC');
        if ($empSearch !== '') {
            $employeesQb->andWhere('LOWER(u.pseudo) LIKE :eq OR LOWER(u.email) LIKE :eq OR LOWER(u.nom) LIKE :eq OR LOWER(u.prenom) LIKE :eq')
                ->setParameter('eq', '%' . strtolower($empSearch) . '%');
        }
        $empTotal = (int) $employeesQb->select('COUNT(u.id)')->getQuery()->getSingleScalarResult();
        $empTotalPages = max(1, (int) ceil($empTotal / $empSize));
        if ($empPage > $empTotalPages) {
            $empPage = $empTotalPages;
        }
        $employeesList = $employeesQb
            ->select('u')
            ->setFirstResult(($empPage - 1) * $empSize)
            ->setMaxResults($empSize)
            ->getQuery()
            ->getResult();

        $since = (new \DateTime())->modify('-7 days');
        $covoiturages = $covoiturageRepository->createQueryBuilder('c')
            ->leftJoin('c.participants', 'p')->addSelect('p')
            ->where('c.dateDepart >= :since')
            ->andWhere('c.statut != :cancel')
            ->setParameter('since', $since->format('Y-m-d'))
            ->setParameter('cancel', 'annulé')
            ->getQuery()
            ->getResult();

        $perDay = [];
        $revenue = 0;
        $revenueByDay = [];
        foreach ($covoiturages as $c) {
            $day = $c->getDateDepart()?->format('Y-m-d');
            if ($day) {
                $perDay[$day] = ($perDay[$day] ?? 0) + 1;
            }
            $gain = $c->getCommissionPlateforme() * $c->getParticipants()->count();
            $revenue += $gain;
            if ($day) {
                $revenueByDay[$day] = ($revenueByDay[$day] ?? 0) + $gain;
            }
        }

        ksort($perDay);
        ksort($revenueByDay);
        $perDayLabels = array_keys($perDay);
        $perDayValues = array_values($perDay);
        $revLabels = array_keys($revenueByDay);
        $revValues = array_values($revenueByDay);

        // Stats villes (top 6 lieux de départ)
        $ridesByCity = $covoiturageRepository->createQueryBuilder('c')
            ->select('c.lieuDepart AS city', 'COUNT(c.id) AS total')
            ->groupBy('c.lieuDepart')
            ->orderBy('total', 'DESC')
            ->setMaxResults(6)
            ->getQuery()
            ->getArrayResult();
        $ridesByCityLabels = array_map(static fn($row) => $row['city'], $ridesByCity);
        $ridesByCityValues = array_map(static fn($row) => (int) $row['total'], $ridesByCity);

        // Répartition éco (électrique/hybride vs thermique)
        $ecoQuery = $covoiturageRepository->createQueryBuilder('c')
            ->leftJoin('c.voiture', 'v')->addSelect('v')
            ->getQuery()
            ->getResult();
        $ecoCount = 0;
        $thermCount = 0;
        foreach ($ecoQuery as $traj) {
            $energie = strtolower($traj->getVoiture()?->getEnergie() ?? '');
            if (in_array($energie, ['electrique', 'hybride'], true)) {
                ++$ecoCount;
            } else {
                ++$thermCount;
            }
        }
        $ecoSplitValues = [$ecoCount, $thermCount];

        // Inscriptions mensuelles (année courante)
        $currentYear = (int) (new \DateTime())->format('Y');
        $monthlyValues = array_fill(1, 12, 0);
        $yearStart = new \DateTime($currentYear . '-01-01 00:00:00');
        $yearEnd = (clone $yearStart)->modify('+1 year');
        $usersYear = $utilisateurRepository->createQueryBuilder('u')
            ->where('u.createdAt >= :start')
            ->andWhere('u.createdAt < :end')
            ->setParameter('start', $yearStart)
            ->setParameter('end', $yearEnd)
            ->getQuery()
            ->getResult();
        foreach ($usersYear as $u) {
            $month = (int) $u->getCreatedAt()->format('n');
            $monthlyValues[$month] = ($monthlyValues[$month] ?? 0) + 1;
        }

        $totalUsers = $utilisateurRepository->createQueryBuilder('u')->select('COUNT(u.id)')->getQuery()->getSingleScalarResult();
        $employees = $utilisateurRepository->createQueryBuilder('u')->select('COUNT(u.id)')->where('u.role = :r')->setParameter('r', 'employe')->getQuery()->getSingleScalarResult();
        $suspended = $utilisateurRepository->createQueryBuilder('u')->select('COUNT(u.id)')->where('u.suspended = 1')->getQuery()->getSingleScalarResult();
        $totalCovoits = $covoiturageRepository->createQueryBuilder('c')->select('COUNT(c.id)')->getQuery()->getSingleScalarResult();
        $covoitsEnCours = $covoiturageRepository->createQueryBuilder('c')->select('COUNT(c.id)')->where('c.statut = :s')->setParameter('s', 'en cours')->getQuery()->getSingleScalarResult();
        $covoitsAVenir = $covoiturageRepository->createQueryBuilder('c')->select('COUNT(c.id)')->where('c.statut = :s')->setParameter('s', 'à venir')->getQuery()->getSingleScalarResult();
        $covoitsTermines = $covoiturageRepository->createQueryBuilder('c')->select('COUNT(c.id)')->where('c.statut = :s')->setParameter('s', 'terminé')->getQuery()->getSingleScalarResult();
        $pendingAvisCount = $avisRepository->createQueryBuilder('a')->select('COUNT(a.id)')->where('a.statut = :s')->setParameter('s', 'en attente')->getQuery()->getSingleScalarResult();
        $voitureCount = $entityManager->getRepository(\App\Entity\Voiture::class)->createQueryBuilder('v')->select('COUNT(v.id)')->getQuery()->getSingleScalarResult();

        $covoitPage = max(1, (int) $this->getParameterFromRequest('pageCovoits'));
        $covoitSort = (string) $this->getParameterFromRequest('covoitSort');
        $allowedCovoitSort = ['dateDepart', 'nbPlace', 'statut'];
        if (!in_array($covoitSort, $allowedCovoitSort, true)) {
            $covoitSort = 'dateDepart';
        }
        $covoitDir = (string) $this->getParameterFromRequest('covoitDir');
        $covoitDir = strtolower($covoitDir) === 'asc' ? 'ASC' : 'DESC';
        $covoitSize = 6;
        $covoitsQb = $covoiturageRepository->createQueryBuilder('c')
            ->leftJoin('c.voiture', 'v')->addSelect('v')
            ->leftJoin('v.proprietaire', 'p')->addSelect('p')
            ->orderBy('c.' . $covoitSort, $covoitDir)
            ->addOrderBy('c.heureDepart', 'DESC');
        $covoitsTotal = (int) $covoitsQb->select('COUNT(c.id)')->getQuery()->getSingleScalarResult();
        $covoitsTotalPages = max(1, (int) ceil($covoitsTotal / $covoitSize));
        if ($covoitPage > $covoitsTotalPages) {
            $covoitPage = $covoitsTotalPages;
        }
        $latestCovoits = $covoitsQb
            ->select('c', 'v', 'p')
            ->setFirstResult(($covoitPage - 1) * $covoitSize)
            ->setMaxResults($covoitSize)
            ->getQuery()
            ->getResult();

        return $this->render('legacy/admin_dashboard.html.twig', [
            'users' => $users,
            'perDay' => $perDay,
            'revenue' => $revenue,
            'revenueByDay' => $revenueByDay,
            'perDayLabels' => $perDayLabels,
            'perDayValues' => $perDayValues,
            'revLabels' => $revLabels,
            'revValues' => $revValues,
            'totalUsers' => $totalUsers,
            'employees' => $employees,
            'suspended' => $suspended,
            'totalCovoits' => $totalCovoits,
            'covoitsEnCours' => $covoitsEnCours,
            'covoitsAVenir' => $covoitsAVenir,
            'covoitsTermines' => $covoitsTermines,
            'pendingAvisCount' => $pendingAvisCount,
            'voitureCount' => $voitureCount,
            'latestCovoits' => $latestCovoits,
            'usersCurrentPage' => $page,
            'usersTotalPages' => $usersTotalPages,
            'covoitsCurrentPage' => $covoitPage,
            'covoitsTotalPages' => $covoitsTotalPages,
            'userSort' => $userSort,
            'userDir' => strtolower($userDir),
            'covoitSort' => $covoitSort,
            'covoitDir' => strtolower($covoitDir),
            'employeesList' => $employeesList,
            'employeesCurrentPage' => $empPage,
            'employeesTotalPages' => $empTotalPages,
            'ridesByCityLabels' => $ridesByCityLabels,
            'ridesByCityValues' => $ridesByCityValues,
            'ecoSplitValues' => $ecoSplitValues,
            'monthlyValues' => array_values($monthlyValues),
            'userSearch' => $userSearch,
            'employeeSearch' => $empSearch,
        ]);
    }

    #[Route('/admin/employee/new', name: 'legacy_admin_add_employee_form', methods: ['GET'])]
    public function addEmployeeForm(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('legacy/admin_employee_new.html.twig');
    }

    #[Route('/admin/employee/add', name: 'legacy_admin_add_employee', methods: ['POST'])]
    public function addEmployee(Request $request, EntityManagerInterface $entityManager, UtilisateurRepository $utilisateurRepository, UserPasswordHasherInterface $hasher, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $email = strtolower(trim((string) $request->request->get('email', '')));
        $pseudo = trim((string) $request->request->get('pseudo', ''));
        $nom = trim((string) $request->request->get('nom', 'Employe'));
        $prenom = trim((string) $request->request->get('prenom', $pseudo));
        $password = (string) $request->request->get('password', '');
        if ($email === '' || $pseudo === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->addFlash('error', 'Email ou pseudo invalide.');
            return $this->redirectToRoute('legacy_admin_add_employee_form');
        }
        if ($utilisateurRepository->findOneBy(['email' => $email])) {
            $this->addFlash('error', 'Un compte existe déjà avec cet email.');
            return $this->redirectToRoute('legacy_admin_add_employee_form');
        }
        if (!$this->isCsrfTokenValid('add_employee', (string) $request->request->get('_csrf_token'))) {
            $this->addFlash('error', 'Session expirée.');
            return $this->redirectToRoute('legacy_admin_add_employee_form');
        }
        $user = new \App\Entity\Utilisateur();
        $rawPassword = $password !== '' ? $password : 'Employe123$';
        $user->setEmail($email)
            ->setPseudo($pseudo)
            ->setNom($nom === '' ? 'Employe' : $nom)
            ->setPrenom($prenom === '' ? $pseudo : $prenom)
            ->setRole('employe')
            ->setCredit(0)
            ->setVerifie(true)
            ->setSlug($this->uniqueSlug($slugger, $pseudo))
            ->setPassword($hasher->hashPassword($user, $rawPassword));
        $entityManager->persist($user);
        $entityManager->flush();
        $this->addFlash('success', 'Employé ajouté avec succès.');
        return $this->redirectToRoute('legacy_admin_dashboard', ['tab' => 'employees']);
    }

    #[Route('/admin/employee/{id}/delete', name: 'legacy_admin_delete_employee', methods: ['POST'])]
    public function deleteEmployee(int $id, UtilisateurRepository $utilisateurRepository, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = $utilisateurRepository->find($id);
        if ($user && $user->getRole() === 'employe') {
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'Employé supprimé.');
        }
        return $this->redirectToRoute('legacy_admin_dashboard');
    }

    #[Route('/admin/employee/{id}/edit', name: 'legacy_admin_edit_employee', methods: ['GET', 'POST'])]
    public function editEmployee(int $id, Request $request, UtilisateurRepository $utilisateurRepository, EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = $utilisateurRepository->find($id);
        if (!$user || $user->getRole() !== 'employe') {
            $this->addFlash('error', 'Employé introuvable.');
            return $this->redirectToRoute('legacy_admin_dashboard');
        }
        if ($request->isMethod('POST')) {
            $email = strtolower(trim((string) $request->request->get('email', '')));
            $pseudo = trim((string) $request->request->get('pseudo', ''));
            $password = (string) $request->request->get('password', '');

            if (!$this->isCsrfTokenValid('edit_employee_' . $user->getId(), (string) $request->request->get('_csrf_token'))) {
                $this->addFlash('error', 'Session expirée.');
                return $this->redirectToRoute('legacy_admin_edit_employee', ['id' => $id]);
            }

            if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->addFlash('error', 'Email invalide.');
                return $this->redirectToRoute('legacy_admin_edit_employee', ['id' => $id]);
            }
            if ($pseudo === '') {
                $this->addFlash('error', 'Pseudo obligatoire.');
                return $this->redirectToRoute('legacy_admin_edit_employee', ['id' => $id]);
            }
            $existing = $utilisateurRepository->findOneBy(['email' => $email]);
            if ($existing && $existing->getId() !== $user->getId()) {
                $this->addFlash('error', 'Email déjà utilisé.');
                return $this->redirectToRoute('legacy_admin_edit_employee', ['id' => $id]);
            }
            $user->setEmail($email)->setPseudo($pseudo);
            if ($password !== '') {
                $user->setPassword($hasher->hashPassword($user, $password));
            }
            $entityManager->flush();
            $this->addFlash('success', 'Profil employé mis à jour.');
            return $this->redirectToRoute('legacy_admin_dashboard');
        }

        return $this->render('legacy/admin_employee_edit.html.twig', [
            'employee' => $user,
        ]);
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

    private function getParameterFromRequest(string $name, mixed $default = 1): mixed
    {
        $request = $this->requestStack->getCurrentRequest();
        return $request?->get($name, $default);
    }
}
