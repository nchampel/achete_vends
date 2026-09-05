<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260905143332 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE constant (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, value INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE item (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, pay_price DOUBLE PRECISION NOT NULL, sell_price DOUBLE PRECISION NOT NULL, is_buyable TINYINT(1) NOT NULL, world INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stock_item (id INT AUTO_INCREMENT NOT NULL, item_id INT NOT NULL, user_id INT DEFAULT NULL, final_pay_price DOUBLE PRECISION NOT NULL, final_sell_price DOUBLE PRECISION NOT NULL, is_bought TINYINT(1) NOT NULL, is_sold TINYINT(1) NOT NULL, INDEX IDX_6017DDA126F525E (item_id), INDEX IDX_6017DDAA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE world (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(50) NOT NULL, number INT NOT NULL, is_unlocked TINYINT(1) NOT NULL, INDEX IDX_3A771143A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE world_amount (id INT AUTO_INCREMENT NOT NULL, number INT NOT NULL, amount DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE stock_item ADD CONSTRAINT FK_6017DDA126F525E FOREIGN KEY (item_id) REFERENCES item (id)');
        $this->addSql('ALTER TABLE stock_item ADD CONSTRAINT FK_6017DDAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE world ADD CONSTRAINT FK_3A771143A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stock_item DROP FOREIGN KEY FK_6017DDA126F525E');
        $this->addSql('ALTER TABLE stock_item DROP FOREIGN KEY FK_6017DDAA76ED395');
        $this->addSql('ALTER TABLE world DROP FOREIGN KEY FK_3A771143A76ED395');
        $this->addSql('DROP TABLE constant');
        $this->addSql('DROP TABLE item');
        $this->addSql('DROP TABLE stock_item');
        $this->addSql('DROP TABLE world');
        $this->addSql('DROP TABLE world_amount');
    }
}
