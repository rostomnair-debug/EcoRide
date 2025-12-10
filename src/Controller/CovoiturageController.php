<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Covoiturage;
use App\Entity\Voiture;
use App\Entity\Utilisateur;
use App\Repository\AvisRepository;
use App\Repository\CovoiturageRepository;
use App\Security\Voter\CovoiturageVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class CovoiturageController extends AbstractController
{
    #[Route('/', name: 'legacy_home')]
    public function home(CovoiturageRepository $covoiturageRepository): Response
    {
        $covoiturages = $covoiturageRepository
            ->createQueryBuilder('c')
            ->leftJoin('c.voiture', 'v')->addSelect('v')
            ->leftJoin('v.marque', 'm')->addSelect('m')
            ->leftJoin('v.proprietaire', 'u')->addSelect('u')
            ->where('c.statut IN (:upcomingStatuses)')
            ->setParameter('upcomingStatuses', ['à venir', 'ouvert'])
            ->orderBy('c.dateDepart', 'ASC')
            ->addOrderBy('c.heureDepart', 'ASC')
            ->setMaxResults(6)
            ->getQuery()
            ->getResult();
        foreach ($covoiturages as $trajet) {
            if ($trajet->getStatut() === 'ouvert') {
                $trajet->setStatut('à venir');
            }
        }

        return $this->render('legacy/index.html.twig', [
            'covoiturages' => $covoiturages,
        ]);
    }

    #[Route('/covoiturages', name: 'legacy_covoiturages')]
    public function covoiturages(Request $request, CovoiturageRepository $covoiturageRepository, AvisRepository $avisRepository): Response
    {
        $filters = [
            'depart' => trim((string) $request->query->get('depart', '')),
            'arrivee' => trim((string) $request->query->get('arrivee', '')),
            'date' => $request->query->get('date'),
            'prixMax' => $request->query->get('prixMax'),
            'dureeMax' => $request->query->get('dureeMax'),
            'eco' => $request->query->getBoolean('eco', false),
            'noteMin' => $request->query->get('noteMin'),
            'sort' => $request->query->get('sort', 'date'),
            'dir' => strtolower((string) $request->query->get('dir', 'asc')),
        ];
        $filters['dir'] = $filters['dir'] === 'desc' ? 'desc' : 'asc';

        $qb = $covoiturageRepository->createQueryBuilder('c')
            ->leftJoin('c.voiture', 'v')->addSelect('v')
            ->leftJoin('v.marque', 'm')->addSelect('m')
            ->leftJoin('v.proprietaire', 'u')->addSelect('u')
            ->where('c.nbPlace > 0')
            ->andWhere('c.statut IN (:upcomingStatuses)')
            ->setParameter('upcomingStatuses', ['à venir', 'ouvert']);

        $dir = strtoupper($filters['dir']);
        switch ($filters['sort']) {
            case 'price':
                $qb->orderBy('c.prixPersonne', $dir)
                    ->addOrderBy('c.dateDepart', 'ASC')
                    ->addOrderBy('c.heureDepart', 'ASC');
                break;
            case 'date':
            default:
                $filters['sort'] = 'date';
                $qb->orderBy('c.dateDepart', $dir)
                    ->addOrderBy('c.heureDepart', $dir);
                break;
        }

        if ($filters['depart'] !== '') {
            $qb->andWhere('LOWER(c.lieuDepart) LIKE :depart')
                ->setParameter('depart', '%' . strtolower($filters['depart']) . '%');
        }
        if ($filters['arrivee'] !== '') {
            $qb->andWhere('LOWER(c.lieuArrivee) LIKE :arrivee')
                ->setParameter('arrivee', '%' . strtolower($filters['arrivee']) . '%');
        }
        if ($filters['date']) {
            $qb->andWhere('c.dateDepart = :date')->setParameter('date', $filters['date']);
        }

        $covoiturages = $qb->getQuery()->getResult();
        foreach ($covoiturages as $trajet) {
            if ($trajet->getStatut() === 'ouvert') {
                $trajet->setStatut('à venir');
            }
        }

        $computeAvg = static function ($trajet): float {
            $avis = $trajet->getAvis();
            if ($avis->isEmpty()) {
                return 0.0;
            }
            $sum = 0;
            $count = 0;
            foreach ($avis as $a) {
                $note = (float) $a->getNote();
                if ($note > 0) {
                    $sum += $note;
                    ++$count;
                }
            }
            return $count ? $sum / $count : 0.0;
        };

        $covoiturages = array_filter($covoiturages, function ($trajet) use ($filters, $computeAvg) {
            /** @var \App\Entity\Covoiturage $trajet */
            if ($filters['prixMax'] && $trajet->getPrixPersonne() > (float) $filters['prixMax']) {
                return false;
            }
            $energie = strtolower((string) $trajet->getVoiture()->getEnergie());
            if ($filters['eco'] && !in_array($energie, ['electrique', 'hybride'], true)) {
                return false;
            }
            if ($filters['dureeMax']) {
                $dateDep = $trajet->getDateDepart();
                $heureDep = $trajet->getHeureDepart();
                $dateArr = $trajet->getDateArrivee();
                $heureArr = $trajet->getHeureArrivee();
                if ($dateDep instanceof \DateTimeInterface && $heureDep instanceof \DateTimeInterface && $dateArr instanceof \DateTimeInterface && $heureArr instanceof \DateTimeInterface) {
                    $depart = \DateTimeImmutable::createFromInterface($dateDep)
                        ->setTime((int) $heureDep->format('H'), (int) $heureDep->format('i'));
                    $arrivee = \DateTimeImmutable::createFromInterface($dateArr)
                        ->setTime((int) $heureArr->format('H'), (int) $heureArr->format('i'));
                    $interval = $depart->diff($arrivee);
                    $hours = $interval->days * 24 + $interval->h + $interval->i / 60;
                    if ($hours > (float) $filters['dureeMax']) {
                        return false;
                    }
                }
            }
            if ($filters['noteMin']) {
                $avg = $computeAvg($trajet);
                if ($avg < (float) $filters['noteMin']) {
                    return false;
                }
            }

            return true;
        });

        // Tri côté PHP pour garantir l'ordre final après filtrage
        usort($covoiturages, function ($a, $b) use ($filters) {
            /** @var \App\Entity\Covoiturage $a */
            /** @var \App\Entity\Covoiturage $b */
            $dir = $filters['dir'] === 'desc' ? -1 : 1;
            if ($filters['sort'] === 'price') {
                return $dir * ($a->getPrixPersonne() <=> $b->getPrixPersonne());
            }
            $aDate = $a->getDateDepart();
            $bDate = $b->getDateDepart();
            $aTime = $a->getHeureDepart();
            $bTime = $b->getHeureDepart();
            $aTs = $aDate ? $aDate->format('Y-m-d') : '';
            $bTs = $bDate ? $bDate->format('Y-m-d') : '';
            if ($aTs === $bTs) {
                return $dir * (($aTime?->format('H:i') ?? '') <=> ($bTime?->format('H:i') ?? ''));
            }
            return $dir * ($aTs <=> $bTs);
        });

        $computeAvgMap = static function (array $list) use ($computeAvg): array {
            $map = [];
            foreach ($list as $trajet) {
                $map[$trajet->getId()] = $computeAvg($trajet);
            }
            return $map;
        };

        $pageSize = 6;
        $page = max(1, (int) $request->query->get('page', 1));
        $covoiturages = array_values($covoiturages);
        $total = count($covoiturages);
        $totalPages = $total > 0 ? (int) ceil($total / $pageSize) : 1;
        if ($page > $totalPages) {
            $page = $totalPages;
        }
        $offset = ($page - 1) * $pageSize;
        $covoiturages = array_slice($covoiturages, $offset, $pageSize);
        $avgNotes = $computeAvgMap($covoiturages);

        $altBefore = [];
        $altAfter = [];
        if (empty($covoiturages) && $filters['date']) {
            $dateSearch = new \DateTime($filters['date']);
            $baseQb = $covoiturageRepository->createQueryBuilder('c')
                ->leftJoin('c.voiture', 'v')->addSelect('v')
                ->leftJoin('v.marque', 'm')->addSelect('m')
                ->leftJoin('v.proprietaire', 'u')->addSelect('u')
                ->where('c.nbPlace > 0')
                ->andWhere('c.statut IN (:upcomingStatuses)')
                ->setParameter('upcomingStatuses', ['à venir', 'ouvert']);

            if ($filters['depart'] !== '') {
                $baseQb->andWhere('LOWER(c.lieuDepart) LIKE :departAlt')
                    ->setParameter('departAlt', '%' . strtolower($filters['depart']) . '%');
            }
            if ($filters['arrivee'] !== '') {
                $baseQb->andWhere('LOWER(c.lieuArrivee) LIKE :arriveeAlt')
                    ->setParameter('arriveeAlt', '%' . strtolower($filters['arrivee']) . '%');
            }

            $qbBefore = clone $baseQb;
            $altBefore = $qbBefore
                ->andWhere('c.dateDepart < :dateSearch')
                ->setParameter('dateSearch', $dateSearch->format('Y-m-d'))
                ->orderBy('c.dateDepart', 'DESC')
                ->addOrderBy('c.heureDepart', 'DESC')
                ->setMaxResults(5)
                ->getQuery()
                ->getResult();

            $qbAfter = clone $baseQb;
            $altAfter = $qbAfter
                ->andWhere('c.dateDepart > :dateSearch')
                ->setParameter('dateSearch', $dateSearch->format('Y-m-d'))
                ->orderBy('c.dateDepart', 'ASC')
                ->addOrderBy('c.heureDepart', 'ASC')
                ->setMaxResults(5)
                ->getQuery()
                ->getResult();
        }
        if (!empty($altBefore)) {
            $avgNotes += $computeAvgMap($altBefore);
        }
        if (!empty($altAfter)) {
            $avgNotes += $computeAvgMap($altAfter);
        }

        $driverAverages = [];
        $collectDriverAvg = function ($trajets) use (&$driverAverages, $avisRepository) {
            foreach ($trajets as $t) {
                $driver = $t->getVoiture()->getProprietaire();
                if ($driver) {
                    $id = $driver->getId();
                    if (!isset($driverAverages[$id])) {
                        $driverAverages[$id] = $avisRepository->averageForUser($driver);
                    }
                }
            }
        };
        $collectDriverAvg($covoiturages);
        $collectDriverAvg($altBefore);
        $collectDriverAvg($altAfter);

        return $this->render('legacy/covoiturages.html.twig', [
            'covoiturages' => $covoiturages,
            'filters' => $filters,
            'avgNotes' => $avgNotes,
            'driverAverages' => $driverAverages,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'altBefore' => $altBefore,
            'altAfter' => $altAfter,
        ]);
    }

    #[Route('/publish-ride', name: 'legacy_publish_ride', methods: ['GET', 'POST'])]
    public function publishRide(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Utilisateur) {
            $this->addFlash('error', 'Connectez-vous pour publier un trajet.');
            return $this->redirectToRoute('legacy_sign_in');
        }
        /** @var Utilisateur $user */

        $cars = $user->getVoitures();
        if ($cars->isEmpty()) {
            $this->addFlash('error', 'Ajoutez un véhicule (onglet "Véhicules") pour pouvoir publier un trajet.');
            return $this->redirectToRoute('legacy_my_space', ['tab' => 'cars', '_fragment' => 'cars']);
        }

        if ($request->isMethod('POST')) {
            $depCity = trim((string) $request->request->get('departureCity'));
            $arrCity = trim((string) $request->request->get('arrivalCity'));
            $depDate = (string) $request->request->get('departureDate');
            $depTime = (string) $request->request->get('departureTime');
            $arrTime = (string) $request->request->get('arrivalTime');
            $seats = (int) $request->request->get('availableSeats');
            $price = (float) $request->request->get('pricePerSeat');
            $vehicleId = (int) $request->request->get('vehicleSelect');
            $pickupPoint = trim((string) $request->request->get('pickupPoint'));
            $dropPoint = trim((string) $request->request->get('dropPoint'));
            $smokingRule = (string) $request->request->get('smokingRule', 'non');
            $animalsRule = (string) $request->request->get('animalsRule', 'non');
            $luggageRule = (string) $request->request->get('luggageRule', 'leger');

            if ($depCity === '' || $arrCity === '' || !$depDate || !$depTime || $seats <= 0 || $price <= 0 || !$vehicleId) {
                $this->addFlash('error', 'Merci de remplir tous les champs requis.');
                return $this->redirectToRoute('legacy_publish_ride');
            }

            $voiture = $entityManager->getRepository(Voiture::class)->find($vehicleId);
            if (!$voiture || $voiture->getProprietaire()->getId() !== $user->getId()) {
                $this->addFlash('error', 'Véhicule invalide.');
                return $this->redirectToRoute('legacy_publish_ride');
            }

            $trajet = new Covoiturage();
            $owner = $voiture->getProprietaire();
            $trajet->setLieuDepart($depCity)
                ->setLieuArrivee($arrCity)
                ->setDateDepart(new \DateTime($depDate))
                ->setHeureDepart(new \DateTime($depTime))
                ->setDateArrivee(new \DateTime($depDate))
                ->setHeureArrivee(new \DateTime($arrTime ?: $depTime))
                ->setNbPlace($seats)
                ->setPrixPersonne($price)
                ->setStatut('à venir')
                ->setConducteurNom($owner?->getNom())
                ->setConducteurPrenom($owner?->getPrenom())
                ->setConducteurPseudo($owner?->getPseudo())
                ->setPointRdv($pickupPoint ?: null)
                ->setPointArrivee($dropPoint ?: null)
                ->setFumeur(in_array($smokingRule, ['oui', 'pause'], true))
                ->setAnimaux(in_array($animalsRule, ['oui', 'petits'], true))
                ->setBagageType(match ($luggageRule) {
                    'leger' => 'Sac à dos uniquement',
                    'cabine' => 'Type valise cabine',
                    'soute' => 'Type bagage soute',
                    default => null,
                })
                ->setVoiture($voiture);

            $slugBase = strtolower($slugger->slug($depCity . '-' . $arrCity . '-' . $depDate));
            $slug = $slugBase;
            $index = 1;
            while ($entityManager->getRepository(Covoiturage::class)->findOneBy(['slug' => $slug])) {
                $slug = $slugBase . '-' . $index;
                ++$index;
            }
            $trajet->setSlug($slug);

            $entityManager->persist($trajet);
            $entityManager->flush();

            $this->addFlash('success', 'Trajet publié !');
            return $this->redirectToRoute('legacy_covoiturage_detail', ['slug' => $slug]);
        }

        return $this->render('legacy/publish_ride.html.twig', [
            'cars' => $cars,
        ]);
    }

    #[Route('/covoiturage/{slug}', name: 'legacy_covoiturage_detail')]
    public function covoiturageDetail(string $slug, CovoiturageRepository $covoiturageRepository, AvisRepository $avisRepository): Response
    {
        $trajet = $covoiturageRepository->createQueryBuilder('c')
            ->leftJoin('c.voiture', 'v')->addSelect('v')
            ->leftJoin('v.marque', 'm')->addSelect('m')
            ->leftJoin('v.proprietaire', 'u')->addSelect('u')
            ->leftJoin('c.participants', 'p')->addSelect('p')
            ->leftJoin('c.avis', 'a')->addSelect('a')
            ->where('c.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$trajet) {
            throw $this->createNotFoundException();
        }

        /** @var Utilisateur|null $current */
        $current = $this->getUser();
        $isDriver = $current && $trajet->getVoiture()->getProprietaire()->getId() === $current->getId();

        $avg = 0.0;
        $avis = $trajet->getAvis();
        if (!$avis->isEmpty()) {
            $sum = 0; $count = 0;
            foreach ($avis as $a) {
                $note = (float) $a->getNote();
                if ($note > 0) { $sum += $note; $count++; }
            }
            $avg = $count ? $sum / $count : 0.0;
        }
        $driverAvg = $avisRepository->averageForUser($trajet->getVoiture()->getProprietaire());

        return $this->render('legacy/covoiturage_detail.html.twig', [
            'trajet' => $trajet,
            'avgNote' => $avg,
            'driverAvg' => $driverAvg,
            'isDriver' => $isDriver,
            'currentUser' => $current,
        ]);
    }

    #[Route('/covoiturage/{slug}/participer', name: 'legacy_covoiturage_participate', methods: ['POST'])]
    public function covoiturageParticipate(
        string $slug,
        CovoiturageRepository $covoiturageRepository,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof Utilisateur) {
            $this->addFlash('error', 'Connectez-vous pour participer.');
            return $this->redirectToRoute('legacy_sign_in');
        }
        /** @var Utilisateur $user */

        $trajet = $covoiturageRepository->createQueryBuilder('c')
            ->leftJoin('c.voiture', 'v')->addSelect('v')
            ->leftJoin('v.proprietaire', 'u')->addSelect('u')
            ->leftJoin('c.participants', 'p')->addSelect('p')
            ->where('c.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$trajet) {
            throw $this->createNotFoundException();
        }

        if ($trajet->getNbPlace() <= 0) {
            $this->addFlash('error', 'Plus aucune place disponible.');
            return $this->redirectToRoute('legacy_covoiturage_detail', ['slug' => $slug]);
        }

        if ($trajet->getParticipants()->contains($user)) {
            $this->addFlash('error', 'Vous êtes déjà inscrit à ce trajet.');
            return $this->redirectToRoute('legacy_covoiturage_detail', ['slug' => $slug]);
        }

        $prix = (int) ceil($trajet->getPrixPersonne());
        if ($user->getCredit() < $prix) {
            $this->addFlash('error', 'Crédit insuffisant pour réserver.');
            return $this->redirectToRoute('legacy_covoiturage_detail', ['slug' => $slug]);
        }

        $trajet->addParticipant($user);
        $trajet->setNbPlace(max(0, $trajet->getNbPlace() - 1));

        $user->removeCredit($prix);
        $driver = $trajet->getVoiture()->getProprietaire();
        if ($driver && $driver->getId() !== $user->getId()) {
            $driverGain = max(0, $prix - 2);
            $driver->addCredit($driverGain);
        }

        $conn = $entityManager->getConnection();
        $conn->executeStatement(
            'UPDATE covoiturage_participant SET participant_nom = :nom, participant_prenom = :prenom, participant_pseudo = :pseudo WHERE utilisateur_id = :uid AND covoiturage_id = :cid',
            [
                'nom' => $user->getNom(),
                'prenom' => $user->getPrenom(),
                'pseudo' => $user->getPseudo(),
                'uid' => $user->getId(),
                'cid' => $trajet->getId(),
            ]
        );

        $entityManager->flush();

        $emailMessage = (new Email())
            ->from('support@ecoride.test')
            ->to($user->getEmail())
            ->subject('[Support EcoRide] Réservation confirmée')
            ->html(sprintf(
                '<p>Support EcoRide</p><p>Bonjour %s,</p><p>Votre réservation est confirmée.</p><ul><li>Trajet : %s → %s</li><li>Date : %s à %s</li><li>Prix : %s &#129689; (dont 2 &#129689; de commission EcoRide)</li><li>Conducteur : %s %s</li></ul>',
                htmlspecialchars($user->getPrenom() ?? $user->getPseudo() ?? ''),
                htmlspecialchars($trajet->getLieuDepart()),
                htmlspecialchars($trajet->getLieuArrivee()),
                $trajet->getDateDepart()?->format('d/m/Y') ?? '',
                $trajet->getHeureDepart()?->format('H:i') ?? '',
                number_format($trajet->getPrixPersonne(), 0, '', ' '),
                htmlspecialchars($trajet->getVoiture()->getProprietaire()->getPrenom() ?? ''),
                htmlspecialchars($trajet->getVoiture()->getProprietaire()->getNom() ?? '')
            ));
        $mailer->send($emailMessage);

        $this->addFlash('success', 'Réservation confirmée !');
        return $this->redirectToRoute('legacy_covoiturage_detail', ['slug' => $slug]);
    }

    #[Route('/covoiturage/{slug}/annuler-reservation', name: 'legacy_covoiturage_cancel_reservation', methods: ['POST'])]
    public function covoiturageCancelReservation(
        string $slug,
        CovoiturageRepository $covoiturageRepository,
        EntityManagerInterface $entityManager,
        AvisRepository $avisRepository
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof Utilisateur) {
            $this->addFlash('error', 'Connectez-vous pour annuler votre réservation.');
            return $this->redirectToRoute('legacy_sign_in');
        }
        /** @var Utilisateur $user */

        $trajet = $covoiturageRepository->createQueryBuilder('c')
            ->leftJoin('c.voiture', 'v')->addSelect('v')
            ->leftJoin('v.proprietaire', 'u')->addSelect('u')
            ->leftJoin('c.participants', 'p')->addSelect('p')
            ->where('c.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$trajet) {
            throw $this->createNotFoundException();
        }

        if (!$trajet->getParticipants()->contains($user)) {
            $this->addFlash('error', 'Vous n’êtes pas inscrit à ce trajet.');
            return $this->redirectToRoute('legacy_covoiturage_detail', ['slug' => $slug]);
        }

        if (!in_array($trajet->getStatut(), ['à venir', 'ouvert'], true)) {
            $this->addFlash('error', 'Annulation impossible pour ce trajet.');
            return $this->redirectToRoute('legacy_covoiturage_detail', ['slug' => $slug]);
        }

        $price = (int) ceil($trajet->getPrixPersonne());
        $commission = 2;
        $refund = max(0, $price - $commission);
        $driverGain = max(0, $price - $commission);
        $driver = $trajet->getVoiture()->getProprietaire();

        $trajet->removeParticipant($user);
        $trajet->setNbPlace($trajet->getNbPlace() + 1);

        $user->addCredit($refund);
        if ($driver && $driver->getId() !== $user->getId()) {
            $driver->removeCredit($driverGain);
        }

        $entityManager->flush();

        $this->addFlash('success', 'Réservation annulée. Remboursement effectué (commission EcoRide non remboursée).');
        return $this->redirectToRoute('legacy_covoiturage_detail', ['slug' => $slug]);
    }

    #[Route('/covoiturage/{slug}/avis', name: 'legacy_covoiturage_avis', methods: ['POST'])]
    public function covoiturageAvis(
        string $slug,
        Request $request,
        CovoiturageRepository $covoiturageRepository,
        EntityManagerInterface $entityManager,
        AvisRepository $avisRepository
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof Utilisateur) {
            $this->addFlash('error', 'Connectez-vous pour laisser un avis.');
            return $this->redirectToRoute('legacy_sign_in');
        }
        /** @var Utilisateur $user */

        $trajet = $covoiturageRepository->createQueryBuilder('c')
            ->leftJoin('c.participants', 'p')->addSelect('p')
            ->leftJoin('c.avis', 'a')->addSelect('a')
            ->where('c.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$trajet) {
            throw $this->createNotFoundException();
        }

        $isParticipant = $trajet->getParticipants()->contains($user);
        if (!$isParticipant || $trajet->getStatut() !== 'terminé') {
            $this->addFlash('error', 'Avis possible uniquement pour un trajet terminé auquel vous avez participé.');
            return $this->redirectToRoute('legacy_covoiturage_detail', ['slug' => $slug]);
        }

        foreach ($trajet->getAvis() as $avis) {
            if ($avis->getUtilisateur() && $avis->getUtilisateur()->getId() === $user->getId()) {
                $this->addFlash('error', 'Vous avez déjà laissé un avis pour ce trajet.');
                return $this->redirectToRoute('legacy_covoiturage_detail', ['slug' => $slug]);
            }
        }

        $note = (string) $request->request->get('note');
        $comment = trim((string) $request->request->get('commentaire'));
        if ($note === '') {
            $this->addFlash('error', 'Merci de sélectionner une note.');
            return $this->redirectToRoute('legacy_covoiturage_detail', ['slug' => $slug]);
        }

        $avis = new Avis();
        $avis->setNote($note)
            ->setCommentaire($comment)
            ->setStatut('en attente')
            ->setRatedUser($trajet->getVoiture()->getProprietaire())
            ->setUtilisateur($user)
            ->setCovoiturage($trajet)
            ->setAuteurNom($user->getNom())
            ->setAuteurPrenom($user->getPrenom())
            ->setAuteurPseudo($user->getPseudo());

        $entityManager->persist($avis);
        $entityManager->flush();

        $target = $trajet->getVoiture()->getProprietaire();
        if ($target) {
            $avg = $avisRepository->averageForUser($target);
            $target->setNoteMoyenne($avg);
            $entityManager->flush();
        }

        $this->addFlash('success', 'Avis soumis, en attente de validation.');
        return $this->redirectToRoute('legacy_covoiturage_detail', ['slug' => $slug]);
    }

    #[Route('/avis/{id}/signal', name: 'legacy_avis_signal', methods: ['POST'])]
    public function signalAvis(int $id, Request $request, AvisRepository $avisRepository, EntityManagerInterface $entityManager): Response
    {
        /** @var Utilisateur|null $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('legacy_sign_in');
        }

        $avis = $avisRepository->find($id);
        if (!$avis) {
            throw $this->createNotFoundException();
        }

        $motif = trim((string) $request->request->get('motif', ''));
        $avis->setSignale(true)->setMotifSignalement($motif ?: 'Signalé par un utilisateur.');
        $entityManager->flush();

        $this->addFlash('success', 'Avis signalé, un employé va le traiter.');
        return $this->redirectToRoute('legacy_covoiturage_detail', ['slug' => $avis->getCovoiturage()->getSlug()]);
    }

    #[Route('/covoiturage/{slug}/noter-passagers', name: 'legacy_covoiturage_rate_passengers', methods: ['POST'])]
    public function ratePassengers(
        string $slug,
        Request $request,
        CovoiturageRepository $covoiturageRepository,
        EntityManagerInterface $entityManager,
        AvisRepository $avisRepository
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof Utilisateur) {
            $this->addFlash('error', 'Connectez-vous pour noter vos passagers.');
            return $this->redirectToRoute('legacy_sign_in');
        }

        $trajet = $covoiturageRepository->createQueryBuilder('c')
            ->leftJoin('c.participants', 'p')->addSelect('p')
            ->where('c.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$trajet) {
            throw $this->createNotFoundException();
        }

        $isDriver = $trajet->getVoiture()->getProprietaire()->getId() === $user->getId();
        if (!$isDriver || $trajet->getStatut() !== 'terminé') {
            $this->addFlash('error', 'Seul le conducteur peut noter ses passagers après le trajet.');
            return $this->redirectToRoute('legacy_covoiturage_detail', ['slug' => $slug]);
        }

        $notes = $request->request->all('notes');
        $created = 0;
        $updatedUsers = [];
        foreach ($trajet->getParticipants() as $participant) {
            $pid = (string) $participant->getId();
            if (!isset($notes[$pid]) || $notes[$pid] === '') {
                continue;
            }
            $note = (int) $notes[$pid];
            if ($note < 1 || $note > 5) {
                continue;
            }

            $already = $trajet->getAvis()->filter(function (Avis $a) use ($participant, $user) {
                return $a->getRatedUser() && $a->getRatedUser()->getId() === $participant->getId() && $a->getUtilisateur() && $a->getUtilisateur()->getId() === $user->getId();
            });
            if ($already->count() > 0) {
                continue;
            }

            $avis = new Avis();
            $avis->setNote((string) $note)
                ->setCommentaire('')
                ->setStatut('en attente')
                ->setRatedUser($participant)
                ->setUtilisateur($user)
                ->setCovoiturage($trajet)
                ->setAuteurNom($user->getNom())
                ->setAuteurPrenom($user->getPrenom())
                ->setAuteurPseudo($user->getPseudo());
            $entityManager->persist($avis);
            ++$created;
            $updatedUsers[$participant->getId()] = $participant;
        }

        if ($created > 0) {
            $entityManager->flush();
            foreach ($updatedUsers as $u) {
                $avg = $avisRepository->averageForUser($u);
                $u->setNoteMoyenne($avg);
            }
            $entityManager->flush();
            $this->addFlash('success', 'Notes envoyées pour validation.');
        } else {
            $this->addFlash('info', 'Aucune note envoyée.');
        }

        return $this->redirectToRoute('legacy_covoiturage_detail', ['slug' => $slug]);
    }

    #[Route('/covoiturage/{slug}/signal', name: 'legacy_covoiturage_signal', methods: ['POST'])]
    public function covoiturageSignal(string $slug, Request $request, CovoiturageRepository $covoiturageRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Utilisateur) {
            $this->addFlash('error', 'Connectez-vous pour signaler un trajet.');
            return $this->redirectToRoute('legacy_sign_in');
        }
        /** @var Utilisateur $user */

        $trajet = $covoiturageRepository->createQueryBuilder('c')
            ->leftJoin('c.participants', 'p')->addSelect('p')
            ->leftJoin('c.voiture', 'v')->addSelect('v')
            ->leftJoin('v.proprietaire', 'pr')->addSelect('pr')
            ->where('c.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$trajet) {
            throw $this->createNotFoundException();
        }

        $isParticipant = $trajet->getParticipants()->contains($user) || ($trajet->getVoiture() && $trajet->getVoiture()->getProprietaire()->getId() === $user->getId());
        if (!$isParticipant) {
            $this->addFlash('error', 'Seuls les participants ou le conducteur peuvent signaler ce trajet.');
            return $this->redirectToRoute('legacy_covoiturage_detail', ['slug' => $slug]);
        }

        $motif = trim((string) $request->request->get('motif', ''));
        $trajet->setSignale(true)->setMotifSignalement($motif ?: 'Signalement trajet');
        $entityManager->flush();

        $this->addFlash('success', 'Trajet signalé, un employé va le traiter.');
        return $this->redirectToRoute('legacy_covoiturage_detail', ['slug' => $slug]);
    }

    #[Route('/covoiturage/{slug}/start', name: 'legacy_covoiturage_start', methods: ['POST'])]
    public function covoiturageStart(string $slug, CovoiturageRepository $covoiturageRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Utilisateur) {
            return $this->redirectToRoute('legacy_sign_in');
        }
        /** @var Utilisateur $user */

        $trajet = $covoiturageRepository->createQueryBuilder('c')
            ->leftJoin('c.voiture', 'v')->addSelect('v')
            ->leftJoin('v.proprietaire', 'p')->addSelect('p')
            ->where('c.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$trajet) {
            throw $this->createNotFoundException();
        }

        $this->denyAccessUnlessGranted(CovoiturageVoter::MANAGE, $trajet);

        $trajet->setStatut('en cours');
        $entityManager->flush();
        $this->addFlash('success', 'Trajet démarré.');
        return $this->redirectToRoute('legacy_covoiturage_detail', ['slug' => $slug]);
    }

    #[Route('/covoiturage/{slug}/finish', name: 'legacy_covoiturage_finish', methods: ['POST'])]
    public function covoiturageFinish(
        string $slug,
        CovoiturageRepository $covoiturageRepository,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof Utilisateur) {
            return $this->redirectToRoute('legacy_sign_in');
        }
        /** @var Utilisateur $user */

        $trajet = $covoiturageRepository->createQueryBuilder('c')
            ->leftJoin('c.voiture', 'v')->addSelect('v')
            ->leftJoin('v.proprietaire', 'p')->addSelect('p')
            ->where('c.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$trajet) {
            throw $this->createNotFoundException();
        }

        $this->denyAccessUnlessGranted(CovoiturageVoter::MANAGE, $trajet);

        $trajet->setStatut('terminé');
        $entityManager->flush();

        foreach ($trajet->getParticipants() as $participant) {
            if ($participant->getId() === $user->getId()) {
                continue;
            }
            if ($participant->getEmail()) {
                $emailMessage = (new Email())
                    ->from('support@ecoride.test')
                    ->to($participant->getEmail())
                    ->subject('[Support EcoRide] Merci de noter votre trajet')
                    ->html(sprintf(
                        '<p>Support EcoRide</p><p>Bonjour %s,</p><p>Merci d\'avoir voyagé avec nous.</p><p>Trajet : %s → %s (réf : %s)</p><p>Merci d\'évaluer votre expérience : <a href="%s">laisser un avis</a></p>',
                        htmlspecialchars($participant->getPrenom() ?? $participant->getPseudo() ?? ''),
                        htmlspecialchars($trajet->getLieuDepart()),
                        htmlspecialchars($trajet->getLieuArrivee()),
                        htmlspecialchars($trajet->getSlug()),
                        $this->generateUrl('legacy_covoiturage_detail', ['slug' => $trajet->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL)
                    ));
                $mailer->send($emailMessage);
            }
        }

        $this->addFlash('success', 'Merci d\'avoir choisi EcoRide. Trajet terminé. Pensez à évaluer vos passagers.');
        return $this->redirectToRoute('legacy_my_space', ['_fragment' => 'trips-tab']);
    }

    #[Route('/covoiturage/{slug}/cancel', name: 'legacy_covoiturage_cancel', methods: ['POST'])]
    public function covoiturageCancel(
        string $slug,
        CovoiturageRepository $covoiturageRepository,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ): Response {
        /** @var Utilisateur|null $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('legacy_sign_in');
        }

        $trajet = $covoiturageRepository->createQueryBuilder('c')
            ->leftJoin('c.voiture', 'v')->addSelect('v')
            ->leftJoin('v.proprietaire', 'p')->addSelect('p')
            ->leftJoin('c.participants', 'part')->addSelect('part')
            ->where('c.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$trajet) {
            throw $this->createNotFoundException();
        }

        $this->denyAccessUnlessGranted(CovoiturageVoter::MANAGE, $trajet);

        if ($trajet->getStatut() === 'terminé') {
            $this->addFlash('error', 'Trajet déjà terminé, impossible de l\'annuler.');
            return $this->redirectToRoute('legacy_covoiturage_detail', ['slug' => $slug]);
        }

        $price = (int) $trajet->getPrixPersonne();
        $commission = (int) $trajet->getCommissionPlateforme();
        $participants = $trajet->getParticipants();
        $participantCount = $participants->count();

        foreach ($participants as $participant) {
            if ($participant->getId() === $user->getId()) {
                continue;
            }
            $participant->addCredit($price);
            if ($participant->getEmail()) {
                $email = (new Email())
                    ->from('support@ecoride.test')
                    ->to($participant->getEmail())
                    ->subject('[Support EcoRide] Trajet annulé')
                    ->html(sprintf(
                        '<p>Support EcoRide</p><p>Bonjour %s,</p><p>Le trajet %s → %s prévu le %s à %s a été annulé par le conducteur. Vos crédits ont été recrédités.</p>',
                        htmlspecialchars($participant->getPrenom() ?? $participant->getPseudo() ?? ''),
                        htmlspecialchars($trajet->getLieuDepart()),
                        htmlspecialchars($trajet->getLieuArrivee()),
                        $trajet->getDateDepart()?->format('d/m/Y') ?? '',
                        $trajet->getHeureDepart()?->format('H:i') ?? ''
                    ));
                $mailer->send($email);
            }
        }

        $driverGain = max(0, $price - $commission);
        $totalGain = $driverGain * $participantCount;
        if ($totalGain > 0) {
            $user->removeCredit($totalGain);
        }

        $trajet->setStatut('annulé');
        $trajet->setNbPlace(0);
        $trajet->getParticipants()->clear();
        $entityManager->flush();

        $this->addFlash('success', 'Trajet annulé, participants remboursés.');
        return $this->redirectToRoute('legacy_covoiturages');
    }
}
