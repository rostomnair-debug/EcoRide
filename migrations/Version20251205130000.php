<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251205130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout de rated_user_id sur avis pour distinguer l’utilisateur noté';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE avis ADD rated_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0DC44E82B FOREIGN KEY (rated_user_id) REFERENCES utilisateur (id)');
        $this->addSql('CREATE INDEX IDX_8F91ABF0DC44E82B ON avis (rated_user_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0DC44E82B');
        $this->addSql('DROP INDEX IDX_8F91ABF0DC44E82B ON avis');
        $this->addSql('ALTER TABLE avis DROP rated_user_id');
    }
}
