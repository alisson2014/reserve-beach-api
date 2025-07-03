<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250703141309 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE schedules (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, court_schedule_id INT NOT NULL, payment_method_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', scheduled_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', total_value NUMERIC(10, 2) NOT NULL, transaction_id VARCHAR(255) DEFAULT NULL, INDEX IDX_313BDC8EA76ED395 (user_id), INDEX IDX_313BDC8E9BCFA413 (court_schedule_id), INDEX IDX_313BDC8E5AA1164F (payment_method_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE schedules ADD CONSTRAINT FK_313BDC8EA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE schedules ADD CONSTRAINT FK_313BDC8E9BCFA413 FOREIGN KEY (court_schedule_id) REFERENCES court_schedules (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE schedules ADD CONSTRAINT FK_313BDC8E5AA1164F FOREIGN KEY (payment_method_id) REFERENCES payment_methods (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE schedules DROP FOREIGN KEY FK_313BDC8EA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE schedules DROP FOREIGN KEY FK_313BDC8E9BCFA413
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE schedules DROP FOREIGN KEY FK_313BDC8E5AA1164F
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE schedules
        SQL);
    }
}
