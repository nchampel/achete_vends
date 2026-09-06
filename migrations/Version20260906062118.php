<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260906062118 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE item DROP is_buyable');
        $this->addSql('ALTER TABLE stock_item ADD is_buyable TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE user ADD current_world_id INT DEFAULT NULL, DROP world');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649145D4B36 FOREIGN KEY (current_world_id) REFERENCES world (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649145D4B36 ON user (current_world_id)');
        $this->addSql('ALTER TABLE world DROP FOREIGN KEY FK_3A7711438925311C');
        $this->addSql('DROP INDEX IDX_3A7711438925311C ON world');
        $this->addSql('ALTER TABLE world CHANGE world_id world_data_id INT NOT NULL');
        $this->addSql('ALTER TABLE world ADD CONSTRAINT FK_3A771143D8B08F6B FOREIGN KEY (world_data_id) REFERENCES world_data (id)');
        $this->addSql('CREATE INDEX IDX_3A771143D8B08F6B ON world (world_data_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stock_item DROP is_buyable');
        $this->addSql('ALTER TABLE item ADD is_buyable TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE world DROP FOREIGN KEY FK_3A771143D8B08F6B');
        $this->addSql('DROP INDEX IDX_3A771143D8B08F6B ON world');
        $this->addSql('ALTER TABLE world CHANGE world_data_id world_id INT NOT NULL');
        $this->addSql('ALTER TABLE world ADD CONSTRAINT FK_3A7711438925311C FOREIGN KEY (world_id) REFERENCES world_data (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_3A7711438925311C ON world (world_id)');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649145D4B36');
        $this->addSql('DROP INDEX IDX_8D93D649145D4B36 ON user');
        $this->addSql('ALTER TABLE user ADD world INT NOT NULL, DROP current_world_id');
    }
}
