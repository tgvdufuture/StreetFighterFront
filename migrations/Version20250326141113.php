<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250326141113 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout du champ image à la table character';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `character` ADD image VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `character` DROP image');
    }
}
