<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250523004858 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Cria tabela de tipos de quadras e a relação com a tabela de quadras';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE court_types (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_ACF13E685E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE courts (id INT AUTO_INCREMENT NOT NULL, court_type_id INT NOT NULL, name VARCHAR(50) NOT NULL, description VARCHAR(150) DEFAULT NULL, scheduling_fee NUMERIC(10, 2) NOT NULL, capacity SMALLINT NOT NULL, active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_AD0B4C2F5E237E06 (name), INDEX IDX_AD0B4C2FE22B8821 (court_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE courts ADD CONSTRAINT FK_AD0B4C2FE22B8821 FOREIGN KEY (court_type_id) REFERENCES court_types (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE courts DROP FOREIGN KEY FK_AD0B4C2FE22B8821
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE court_types
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE courts
        SQL);
    }
}
