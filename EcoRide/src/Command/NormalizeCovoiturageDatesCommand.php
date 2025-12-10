<?php

namespace App\Command;

use App\Entity\Covoiturage;
use App\Repository\CovoiturageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:covoiturage:normalize-dates',
    description: 'Harmonise les dates/horaires des covoiturages à venir en recalculant des heures d’arrivée cohérentes.',
)]
class NormalizeCovoiturageDatesCommand extends Command
{
    public function __construct(
        private readonly CovoiturageRepository $covoiturageRepository,
        private readonly EntityManagerInterface $em
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $coords = $this->getCitiesCoords();
        $toProcess = $this->covoiturageRepository->createQueryBuilder('c')
            ->where('c.statut NOT IN (:states)')
            ->setParameter('states', ['en cours', 'terminé'])
            ->getQuery()
            ->getResult();

        $count = 0;
        $today = new \DateTimeImmutable('today');
        foreach ($toProcess as $trajet) {
            /** @var Covoiturage $trajet */
            $dep = $trajet->getLieuDepart();
            $arr = $trajet->getLieuArrivee();
            $depCoord = $coords[$dep] ?? null;
            $arrCoord = $coords[$arr] ?? null;
            $durationMinutes = 90; // valeur par défaut
            if ($depCoord && $arrCoord) {
                $distanceKm = $this->haversine($depCoord[0], $depCoord[1], $arrCoord[0], $arrCoord[1]);
                $speed = 90; // km/h
                $durationMinutes = max(45, min(600, (int) round(($distanceKm / $speed) * 60)));
            }

            $offsetDays = mt_rand(1, 5);
            $depDate = $today->modify('+' . $offsetDays . ' days');
            $depTime = $trajet->getHeureDepart()
                ? \DateTimeImmutable::createFromInterface($trajet->getHeureDepart())
                : (new \DateTimeImmutable('09:00'));
            $start = $depDate->setTime((int) $depTime->format('H'), (int) $depTime->format('i'));
            $arrival = $start->modify('+' . $durationMinutes . ' minutes');

            $trajet->setDateDepart(\DateTime::createFromImmutable($depDate))
                ->setDateArrivee(\DateTime::createFromImmutable($depDate))
                ->setHeureDepart(\DateTime::createFromImmutable($start))
                ->setHeureArrivee(\DateTime::createFromImmutable($arrival));
            ++$count;
        }

        $this->em->flush();
        $output->writeln(sprintf('Trajets mis à jour : %d', $count));
        return Command::SUCCESS;
    }

    private function haversine(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $r = 6371; // rayon Terre km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $r * $c;
    }

    private function getCitiesCoords(): array
    {
        return [
            'Paris' => [48.8566, 2.3522],
            'Marseille' => [43.2965, 5.3698],
            'Lyon' => [45.7640, 4.8357],
            'Toulouse' => [43.6047, 1.4442],
            'Nice' => [43.7102, 7.2620],
            'Nantes' => [47.2184, -1.5536],
            'Strasbourg' => [48.5734, 7.7521],
            'Montpellier' => [43.6109, 3.8772],
            'Bordeaux' => [44.8378, -0.5792],
            'Lille' => [50.6292, 3.0573],
            'Rennes' => [48.1173, -1.6778],
            'Reims' => [49.2583, 4.0317],
            'Le Havre' => [49.4944, 0.1079],
            'Saint-Étienne' => [45.4397, 4.3872],
            'Toulon' => [43.1242, 5.9280],
            'Grenoble' => [45.1885, 5.7245],
            'Dijon' => [47.3220, 5.0415],
            'Angers' => [47.4784, -0.5632],
            'Nîmes' => [43.8367, 4.3601],
            'Villeurbanne' => [45.7640, 4.8847],
            'Clermont-Ferrand' => [45.7772, 3.0870],
            'Aix-en-Provence' => [43.5297, 5.4474],
            'Brest' => [48.3904, -4.4861],
            'Amiens' => [49.8941, 2.2957],
        ];
    }
}
