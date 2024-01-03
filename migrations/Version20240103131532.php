<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240103131532 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE player (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, mana INT NOT NULL, pv INT NOT NULL, ap INT NOT NULL, ad INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user ADD password VARCHAR(255) NOT NULL, ADD description VARCHAR(255) DEFAULT NULL, ADD age INT DEFAULT NULL, DROP ap, DROP ad, DROP mana, DROP pv, CHANGE nom identifiant VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE player');
        $this->addSql('ALTER TABLE user ADD nom VARCHAR(255) NOT NULL, ADD ap INT NOT NULL, ADD mana INT DEFAULT NULL, ADD pv INT NOT NULL, DROP identifiant, DROP password, DROP description, CHANGE age ad INT DEFAULT NULL');
    }
}