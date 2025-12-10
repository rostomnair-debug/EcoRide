<?php

namespace App\Controller;

use App\Repository\AvisRepository;
use App\Repository\CovoiturageRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/conducteur/{slug}', name: 'legacy_detail_conducteur')]
    public function detailConducteur(
        string $slug,
        UtilisateurRepository $utilisateurRepository,
        CovoiturageRepository $covoiturageRepository,
        AvisRepository $avisRepository,
        EntityManagerInterface $em
    ): Response {
        $user = $utilisateurRepository->createQueryBuilder('u')
            ->leftJoin('u.voitures', 'v')->addSelect('v')
            ->leftJoin('u.avisDeposes', 'a')->addSelect('a')
            ->where('u.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$user) {
            throw $this->createNotFoundException();
        }

        $lastTrips = $covoiturageRepository->createQueryBuilder('c')
            ->leftJoin('c.voiture', 'v')->addSelect('v')
            ->leftJoin('v.proprietaire', 'p')->addSelect('p')
            ->leftJoin('c.participants', 'part')->addSelect('part')
            ->where('p = :user OR part = :user')
            ->setParameter('user', $user)
            ->orderBy('c.dateDepart', 'DESC')
            ->addOrderBy('c.heureDepart', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        $driverCovoits = $covoiturageRepository->createQueryBuilder('c2')
            ->select('c2.id')
            ->leftJoin('c2.voiture', 'v2')
            ->leftJoin('v2.proprietaire', 'p2')
            ->where('p2 = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleColumnResult();

        $avisRecus = $avisRepository->createQueryBuilder('a')
            ->leftJoin('a.covoiturage', 'c')->addSelect('c')
            ->where('a.ratedUser = :user')
            ->andWhere('a.statut = :valide')
            ->setParameter('user', $user)
            ->setParameter('valide', 'valide')
            ->orderBy('a.id', 'DESC')
            ->getQuery()
            ->getResult();

        $computeAvg = static function (array $avisList): float {
            $sum = 0; $count = 0;
            foreach ($avisList as $a) {
                $val = (float) $a->getNote();
                if ($val > 0) {
                    $sum += $val;
                    ++$count;
                }
            }
            return $count ? $sum / $count : 0.0;
        };

        $avgNote = $computeAvg($avisRecus);

        // fallback 1 : toutes les notes reçues (quel que soit le statut)
        if ($avgNote === 0.0) {
            $allAvis = $avisRepository->createQueryBuilder('a3')
                ->leftJoin('a3.covoiturage', 'c3')->addSelect('c3')
                ->where('a3.ratedUser = :user')
                ->setParameter('user', $user)
                ->getQuery()
                ->getResult();
            $avgNote = $computeAvg($allAvis);
            if ($avgNote > 0) {
                $avisRecus = $allAvis;
            }
        }

        // fallback 2 : anciens avis liés aux trajets du conducteur (sans ratedUser)
        if ($avgNote === 0.0 && !empty($driverCovoits)) {
            $legacyAvis = $avisRepository->createQueryBuilder('a4')
                ->leftJoin('a4.covoiturage', 'c4')->addSelect('c4')
                ->where('c4.id IN (:ids)')
                ->setParameter('ids', $driverCovoits)
                ->orderBy('a4.id', 'DESC')
                ->getQuery()
                ->getResult();
            $avgNote = $computeAvg($legacyAvis);
            if ($avgNote > 0 && empty($avisRecus)) {
                $avisRecus = $legacyAvis;
            }
        }

        // persiste la note moyenne
        $user->setNoteMoyenne($avgNote > 0 ? $avgNote : null);
        $em->flush();

        return $this->render('legacy/user_profile.html.twig', [
            'profileUser' => $user,
            'avisList' => $avisRecus,
            'profileAvg' => $avgNote,
            'lastTrips' => $lastTrips,
        ]);
    }
}
