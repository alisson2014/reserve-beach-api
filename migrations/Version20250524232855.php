<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250524232855 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Cria a tabela court_schedules para armazenar os horários de funcionamento das quadras.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE court_schedules (id INT AUTO_INCREMENT NOT NULL, court_id INT NOT NULL, day_of_week INT NOT NULL, start_time TIME NOT NULL, INDEX IDX_E0A5F3CAE3184009 (court_id), UNIQUE INDEX unique_schedule_idx (court_id, day_of_week, start_time), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE court_schedules ADD CONSTRAINT FK_E0A5F3CAE3184009 FOREIGN KEY (court_id) REFERENCES courts (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE court_schedules DROP FOREIGN KEY FK_E0A5F3CAE3184009
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE court_schedules
        SQL);
    }
}
