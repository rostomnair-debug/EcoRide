<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251205121500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add support_message table for contact messages and replies';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE support_message (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, pseudo VARCHAR(255) DEFAULT NULL, subject VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, created_at DATETIME NOT NULL, replied TINYINT(1) NOT NULL, replied_at DATETIME DEFAULT NULL, reply_content LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE support_message');
    }
}
