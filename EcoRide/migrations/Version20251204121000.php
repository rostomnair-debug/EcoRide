<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251204121000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout préférences covoiturage : fumeur, animaux, type de bagage';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE covoiturage ADD fumeur TINYINT(1) DEFAULT 0 NOT NULL, ADD animaux TINYINT(1) DEFAULT 0 NOT NULL, ADD bagage_type VARCHAR(100) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE covoiturage DROP fumeur, DROP animaux, DROP bagage_type');
    }
}
