<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200819091627 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Création de la table users';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1483A5E9F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE books ADD created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE books ADD CONSTRAINT FK_4A1B2A92B03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_4A1B2A92B03A8386 ON books (created_by_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE books DROP FOREIGN KEY FK_4A1B2A92B03A8386');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP INDEX IDX_4A1B2A92B03A8386 ON books');
        $this->addSql('ALTER TABLE books DROP created_by_id');
    }
}
