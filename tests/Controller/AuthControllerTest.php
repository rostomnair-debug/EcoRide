<?php

namespace App\Tests\Controller;

use App\Controller\AuthController;
use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthControllerTest extends WebTestCase
{
    public function testRegisterRequiresCgu(): void
    {
        $client = static::createClient();

        $client->request('POST', '/login', [
            'name' => 'Test',
            'firstname' => 'User',
            'pseudo' => 'test-' . uniqid(),
            'email' => 'test-' . uniqid() . '@example.com',
            'password' => 'Aa!23456',
            'confirm_password' => 'Aa!23456',
            // accept_cgu intentionally missing
        ]);

        self::assertResponseRedirects('/login');
        $crawler = $client->followRedirect();
        self::assertSelectorExists('.alert.alert-danger');
        self::assertStringContainsString('conditions', $crawler->filter('.alert')->text());
    }

    public function testRegisterStoresCguAcceptance(): void
    {
        $client = static::createClient();

        $email = 'ok-' . uniqid() . '@example.com';
        $pseudo = 'ok-' . uniqid();

        $client->request('POST', '/login', [
            'name' => 'Ok',
            'firstname' => 'User',
            'pseudo' => $pseudo,
            'email' => $email,
            'password' => 'Aa!23456',
            'confirm_password' => 'Aa!23456',
            'accept_cgu' => 1,
        ]);

        self::assertResponseRedirects('/sign-in');

        $container = static::getContainer();
        /** @var UtilisateurRepository $repo */
        $repo = $container->get(UtilisateurRepository::class);
        /** @var Utilisateur|null $user */
        $user = $repo->findOneBy(['email' => $email]);

        self::assertNotNull($user, 'User should be created');
        self::assertNotNull($user->getCguAcceptedAt(), 'CGU acceptance date should be set');
        self::assertSame(AuthController::CGU_VERSION, $user->getCguVersion(), 'CGU version should be stored');

        // Cleanup
        if ($user instanceof Utilisateur) {
            /** @var EntityManagerInterface $em */
            $em = $container->get(EntityManagerInterface::class);
            $em->remove($user);
            $em->flush();
        }
    }
}
