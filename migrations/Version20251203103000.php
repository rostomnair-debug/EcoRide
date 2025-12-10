<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251203103000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Suppression de la table user (non utilisée)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS user');
    }

    public function down(Schema $schema): void
    {
        // On ne recrée pas la table user car elle est inutile dans le projet.
    }
}
