<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260928114835 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE resource (id INT AUTO_INCREMENT NOT NULL, resource_id INT NOT NULL, duration INT NOT NULL, peremption DATETIME DEFAULT NULL, latitude NUMERIC(10, 7) NOT NULL, longitude NUMERIC(10, 7) NOT NULL, is_collected TINYINT(1) NOT NULL, collected_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_BC91F41689329D25 (resource_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE resource_data (id INT AUTO_INCREMENT NOT NULL, world_id INT NOT NULL, name VARCHAR(50) NOT NULL, image_url VARCHAR(100) NOT NULL, type VARCHAR(50) NOT NULL, quantity INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_A2D353D98925311C (world_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE resource ADD CONSTRAINT FK_BC91F41689329D25 FOREIGN KEY (resource_id) REFERENCES resource_data (id)');
        $this->addSql('ALTER TABLE resource_data ADD CONSTRAINT FK_A2D353D98925311C FOREIGN KEY (world_id) REFERENCES world_data (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE resource DROP FOREIGN KEY FK_BC91F41689329D25');
        $this->addSql('ALTER TABLE resource_data DROP FOREIGN KEY FK_A2D353D98925311C');
        $this->addSql('DROP TABLE resource');
        $this->addSql('DROP TABLE resource_data');
    }
}
