<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250608215029 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE cart_items (id INT AUTO_INCREMENT NOT NULL, cart_id INT NOT NULL, court_schedule_id INT NOT NULL, added_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_BEF484451AD5CDBF (cart_id), INDEX IDX_BEF484459BCFA413 (court_schedule_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE carts (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, status CHAR(2) NOT NULL, expires_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_4E004AACA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE court_schedules (id INT AUTO_INCREMENT NOT NULL, court_id INT NOT NULL, day_of_week INT NOT NULL, start_time TIME NOT NULL, INDEX IDX_E0A5F3CAE3184009 (court_id), UNIQUE INDEX unique_schedule_idx (court_id, day_of_week, start_time), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE court_types (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', UNIQUE INDEX UNIQ_ACF13E685E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE courts (id INT AUTO_INCREMENT NOT NULL, court_type_id INT NOT NULL, name VARCHAR(50) NOT NULL, description VARCHAR(150) DEFAULT NULL, scheduling_fee NUMERIC(10, 2) NOT NULL, capacity SMALLINT NOT NULL, active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', deleted_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', UNIQUE INDEX UNIQ_AD0B4C2F5E237E06 (name), INDEX IDX_AD0B4C2FE22B8821 (court_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE payment_methods (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', UNIQUE INDEX UNIQ_4FABF9835E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, last_name VARCHAR(150) NOT NULL, email VARCHAR(254) NOT NULL, password VARCHAR(255) NOT NULL, status CHAR(1) NOT NULL, phone VARCHAR(11) DEFAULT NULL, cpf VARCHAR(11) DEFAULT NULL, birth_date DATE DEFAULT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', roles JSON NOT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), UNIQUE INDEX UNIQ_1483A5E93E3E11F0 (cpf), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cart_items ADD CONSTRAINT FK_BEF484451AD5CDBF FOREIGN KEY (cart_id) REFERENCES carts (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cart_items ADD CONSTRAINT FK_BEF484459BCFA413 FOREIGN KEY (court_schedule_id) REFERENCES court_schedules (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE carts ADD CONSTRAINT FK_4E004AACA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE court_schedules ADD CONSTRAINT FK_E0A5F3CAE3184009 FOREIGN KEY (court_id) REFERENCES courts (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE courts ADD CONSTRAINT FK_AD0B4C2FE22B8821 FOREIGN KEY (court_type_id) REFERENCES court_types (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE cart_items DROP FOREIGN KEY FK_BEF484451AD5CDBF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cart_items DROP FOREIGN KEY FK_BEF484459BCFA413
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE carts DROP FOREIGN KEY FK_4E004AACA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE court_schedules DROP FOREIGN KEY FK_E0A5F3CAE3184009
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE courts DROP FOREIGN KEY FK_AD0B4C2FE22B8821
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE cart_items
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE carts
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE court_schedules
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE court_types
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE courts
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE payment_methods
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE users
        SQL);
    }
}
