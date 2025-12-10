<?php

namespace App\Command;

use App\Entity\Avis;
use App\Entity\Covoiturage;
use App\Entity\Marque;
use App\Entity\Utilisateur;
use App\Entity\Voiture;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImportTrajetsFromBackupCommand extends Command
{
    protected static $defaultName = 'app:import:trajets-backup';

    public function __construct(
        private EntityManagerInterface $em,
        private SluggerInterface $slugger,
        private UserPasswordHasherInterface $hasher
    ) {
        parent::__construct(self::$defaultName);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $data = $this->getBackupData();
        $createdUsers = 0;
        $createdCars = 0;
        $createdTrips = 0;

        foreach ($data as $entry) {
            $cond = $entry['conducteur'];
            $user = $this->em->getRepository(Utilisateur::class)->findOneBy(['email' => $cond['email']]);
            if (!$user) {
                $user = new Utilisateur();
                $user->setNom($this->extractLastName($cond['nom']))
                    ->setPrenom($this->extractFirstName($cond['nom']))
                    ->setPseudo($cond['pseudo'])
                    ->setEmail($cond['email'])
                    ->setTelephone($cond['telephone'])
                    ->setRole('user')
                    ->setCredit(20)
                    ->setVerifie(true)
                    ->setSlug($this->uniqueSlug($cond['pseudo']))
                    ->setPassword($this->hasher->hashPassword($user, 'Password123$'));
                $this->em->persist($user);
                ++$createdUsers;
            }

            $preferences = $cond['preferences'] ?? [];
            $carsById = [];
            foreach ($cond['vehicules'] as $carData) {
                $marque = $this->em->getRepository(Marque::class)->findOneBy(['libelle' => $carData['marque']]);
                if (!$marque) {
                    $marque = new Marque();
                    $marque->setLibelle($carData['marque']);
                    $this->em->persist($marque);
                }
                $car = $this->em->getRepository(Voiture::class)->findOneBy(['immatriculation' => $carData['immatriculation']]);
                if (!$car) {
                    $car = new Voiture();
                    $car->setImmatriculation($carData['immatriculation'])
                        ->setModele($carData['modele'])
                        ->setEnergie(strtolower($carData['energie']))
                        ->setCouleur($carData['couleur'])
                        ->setDatePremiereImmatriculation(date('Y') . '-01-01')
                        ->setMarque($marque)
                        ->setProprietaire($user)
                        ->setProprietaireNom($user->getNom())
                        ->setProprietairePrenom($user->getPrenom())
                        ->setProprietairePseudo($user->getPseudo());
                    $this->em->persist($car);
                    ++$createdCars;
                }
                $carsById[$carData['id']] = $car;
            }

            foreach ($entry['trajets'] as $trajetData) {
                $car = $carsById[$trajetData['vehiculeId']] ?? null;
                if (!$car) {
                    continue;
                }
                $slugBase = strtolower($this->slugger->slug($trajetData['depart'] . '-' . $trajetData['arrivee'] . '-' . $trajetData['date']));
                $slug = $this->uniqueTripSlug($slugBase);
                $existing = $this->em->getRepository(Covoiturage::class)->findOneBy(['slug' => $slugBase]);
                if ($existing) {
                    continue;
                }
                $covoit = new Covoiturage();
                $covoit->setLieuDepart($trajetData['depart'])
                    ->setLieuArrivee($trajetData['arrivee'])
                    ->setDateDepart(new \DateTime($trajetData['date']))
                    ->setHeureDepart(new \DateTime($trajetData['heureDepart']))
                    ->setDateArrivee(new \DateTime($trajetData['date']))
                    ->setHeureArrivee(new \DateTime($trajetData['heureArrivee']))
                    ->setNbPlace($trajetData['placesDisponibles'])
                    ->setPrixPersonne($trajetData['prix'])
                    ->setStatut('à venir')
                    ->setVoiture($car)
                    ->setConducteurNom($user->getNom())
                    ->setConducteurPrenom($user->getPrenom())
                    ->setConducteurPseudo($user->getPseudo())
                    ->setSlug($slug)
                    ->setFumeur((bool) ($preferences['fumeur'] ?? false))
                    ->setAnimaux((bool) ($preferences['animaux'] ?? false))
                    ->setBagageType($preferences['autres'] ?? null);

                // Ajouter les passagers du tableau d'avis
                foreach ($trajetData['avis'] ?? [] as $avisData) {
                    $passenger = $this->getOrCreatePassenger($avisData['passager']);
                    $covoit->addParticipant($passenger);

                    $avis = new Avis();
                    $avis->setNote((string) ($avisData['note'] ?? 0))
                        ->setCommentaire($avisData['commentaire'] ?? '')
                        ->setStatut('valide')
                        ->setUtilisateur($passenger)
                        ->setCovoiturage($covoit)
                        ->setAuteurNom($passenger->getNom())
                        ->setAuteurPrenom($passenger->getPrenom())
                        ->setAuteurPseudo($passenger->getPseudo());
                    $this->em->persist($avis);
                }

                $this->em->persist($covoit);
                ++$createdTrips;
            }
        }

        $this->em->flush();
        $io->success(sprintf('Import terminé : %d utilisateurs, %d véhicules, %d covoiturages.', $createdUsers, $createdCars, $createdTrips));
        return Command::SUCCESS;
    }

    private function uniqueSlug(string $base): string
    {
        $slug = strtolower($this->slugger->slug($base));
        $i = 1;
        while ($this->em->getRepository(Utilisateur::class)->findOneBy(['slug' => $slug])) {
            $slug = strtolower($this->slugger->slug($base . '-' . $i));
            ++$i;
        }
        return $slug;
    }

    private function uniqueTripSlug(string $base): string
    {
        $slug = $base;
        $i = 1;
        while ($this->em->getRepository(Covoiturage::class)->findOneBy(['slug' => $slug])) {
            $slug = $base . '-' . $i;
            ++$i;
        }
        return $slug;
    }

    private function extractFirstName(string $fullName): string
    {
        $parts = explode(' ', $fullName);
        return $parts[0] ?? $fullName;
    }

    private function extractLastName(string $fullName): string
    {
        $parts = explode(' ', $fullName, 2);
        return $parts[1] ?? $fullName;
    }

    private function getOrCreatePassenger(string $pseudo): Utilisateur
    {
        $email = strtolower($pseudo) . '@ecoride.test';
        $user = $this->em->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);
        if ($user) {
            return $user;
        }
        $user = new Utilisateur();
        $user->setPseudo($pseudo)
            ->setNom('Passager')
            ->setPrenom($pseudo)
            ->setEmail($email)
            ->setRole('user')
            ->setCredit(20)
            ->setVerifie(true)
            ->setSlug($this->uniqueSlug($pseudo))
            ->setPassword($this->hasher->hashPassword($user, 'Password123$'));
        $this->em->persist($user);
        return $user;
    }

    private function getBackupData(): array
    {
        return [
            [
                'conducteur' => [
                    'id' => 101,
                    'pseudo' => 'JeanD',
                    'nom' => 'Jean Dupont',
                    'email' => 'jean.dupont@example.com',
                    'telephone' => '0612345678',
                    'preferences' => ['fumeur' => false, 'animaux' => true, 'autres' => 'Préfère les discussions légères'],
                    'vehicules' => [
                        ['id' => 201, 'modele' => 'Tesla Model 3', 'marque' => 'Tesla', 'couleur' => 'Blanc', 'energie' => 'electrique', 'immatriculation' => 'AB-123-CD', 'places' => 4],
                        ['id' => 202, 'modele' => 'Renault Zoé', 'marque' => 'Renault', 'couleur' => 'Bleu', 'energie' => 'electrique', 'immatriculation' => 'CD-456-EF', 'places' => 5],
                    ],
                ],
                'trajets' => [
                    ['id' => 1, 'depart' => 'Paris', 'arrivee' => 'Lyon', 'date' => '2025-10-15', 'heureDepart' => '10:00', 'heureArrivee' => '12:30', 'prix' => 15, 'placesDisponibles' => 2, 'vehiculeId' => 201],
                    ['id' => 2, 'depart' => 'Lyon', 'arrivee' => 'Marseille', 'date' => '2025-10-20', 'heureDepart' => '14:00', 'heureArrivee' => '17:30', 'prix' => 20, 'placesDisponibles' => 3, 'vehiculeId' => 202],
                ],
            ],
            [
                'conducteur' => [
                    'id' => 102,
                    'pseudo' => 'MarieL',
                    'nom' => 'Marie Lambert',
                    'email' => 'marie.lambert@example.com',
                    'telephone' => '0623456789',
                    'preferences' => ['fumeur' => false, 'animaux' => false, 'autres' => 'Préfère les trajets silencieux'],
                    'vehicules' => [
                        ['id' => 203, 'modele' => 'Peugeot 3008', 'marque' => 'Peugeot', 'couleur' => 'Noir', 'energie' => 'diesel', 'immatriculation' => 'EF-789-GH', 'places' => 5],
                    ],
                ],
                'trajets' => [
                    ['id' => 3, 'depart' => 'Bordeaux', 'arrivee' => 'Toulouse', 'date' => '2025-10-18', 'heureDepart' => '08:30', 'heureArrivee' => '11:00', 'prix' => 12, 'placesDisponibles' => 1, 'vehiculeId' => 203],
                ],
            ],
            [
                'conducteur' => [
                    'id' => 103,
                    'pseudo' => 'PierreT',
                    'nom' => 'Pierre Tremblay',
                    'email' => 'pierre.tremblay@example.com',
                    'telephone' => '0634567890',
                    'preferences' => ['fumeur' => true, 'animaux' => true, 'autres' => 'Aime discuter pendant le trajet'],
                    'vehicules' => [
                        ['id' => 204, 'modele' => 'Citroën C4', 'marque' => 'Citroën', 'couleur' => 'Gris', 'energie' => 'essence', 'immatriculation' => 'GH-123-IJ', 'places' => 4],
                    ],
                ],
                'trajets' => [
                    ['id' => 4, 'depart' => 'Nantes', 'arrivee' => 'Rennes', 'date' => '2025-10-22', 'heureDepart' => '09:00', 'heureArrivee' => '10:30', 'prix' => 8, 'placesDisponibles' => 2, 'vehiculeId' => 204],
                    ['id' => 5, 'depart' => 'Rennes', 'arrivee' => 'Paris', 'date' => '2025-10-25', 'heureDepart' => '16:00', 'heureArrivee' => '19:30', 'prix' => 25, 'placesDisponibles' => 3, 'vehiculeId' => 204],
                ],
            ],
            [
                'conducteur' => [
                    'id' => 104,
                    'pseudo' => 'SophieM',
                    'nom' => 'Sophie Martin',
                    'email' => 'sophie.martin@example.com',
                    'telephone' => '0645678901',
                    'preferences' => ['fumeur' => false, 'animaux' => true, 'autres' => 'Préfère les trajets calmes'],
                    'vehicules' => [
                        ['id' => 205, 'modele' => 'Toyota Prius', 'marque' => 'Toyota', 'couleur' => 'Vert', 'energie' => 'hybride', 'immatriculation' => 'IJ-456-KL', 'places' => 4],
                    ],
                ],
                'trajets' => [
                    ['id' => 6, 'depart' => 'Lille', 'arrivee' => 'Amiens', 'date' => '2025-10-19', 'heureDepart' => '13:00', 'heureArrivee' => '14:45', 'prix' => 10, 'placesDisponibles' => 1, 'vehiculeId' => 205],
                ],
            ],
        ];
    }
}
