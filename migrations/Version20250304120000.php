<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250304120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Log CGU acceptance on user: cgu_accepted_at + cgu_version';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE utilisateur ADD cgu_accepted_at DATETIME DEFAULT NULL, ADD cgu_version VARCHAR(20) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE utilisateur DROP cgu_accepted_at, DROP cgu_version');
    }
}
