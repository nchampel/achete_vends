<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260905145150 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE world_data (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, number INT NOT NULL, amount DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP TABLE world_amount');
        $this->addSql('ALTER TABLE world DROP name, CHANGE number world_id INT NOT NULL');
        $this->addSql('ALTER TABLE world ADD CONSTRAINT FK_3A7711438925311C FOREIGN KEY (world_id) REFERENCES world_data (id)');
        $this->addSql('CREATE INDEX IDX_3A7711438925311C ON world (world_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE world DROP FOREIGN KEY FK_3A7711438925311C');
        $this->addSql('CREATE TABLE world_amount (id INT AUTO_INCREMENT NOT NULL, number INT NOT NULL, amount DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE world_data');
        $this->addSql('DROP INDEX IDX_3A7711438925311C ON world');
        $this->addSql('ALTER TABLE world ADD name VARCHAR(50) NOT NULL, CHANGE world_id number INT NOT NULL');
    }
}
