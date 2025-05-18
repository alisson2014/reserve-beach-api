<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Enum\UserStatus;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250518214151 extends AbstractMigration
{
    private const USER_EMAIL = 'almeidaalisson2014@gmail.com';

    public function getDescription(): string
    {
        return 'Migration para adicionar super usuário.';
    }

    public function up(Schema $schema): void
    {
        $hashedPassword = '$2y$13$U4s0Drkb1ewWfBptnn/RJu/d8QuMfgEHLEqDyra9Mf3MkkDN7nC.u';
        $active = UserStatus::ACTIVE->value;
        $this->addSql("INSERT INTO users (name, last_name, email, password, status, created_at, roles, position) VALUES (
            'Root',
            'User',
            :email,
            '{$hashedPassword}',
            '{$active}',
            NOW(),
            '[\"ROLE_SUPER_ADMIN\"]',
            'root'
        )", ['email' => self::USER_EMAIL]);
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM users WHERE email = :email", ['email' => self::USER_EMAIL]);    
    }
}
