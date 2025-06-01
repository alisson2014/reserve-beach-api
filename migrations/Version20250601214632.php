<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250601214632 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Popula tabela de métodos de pagamento com os dados iniciais.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('INSERT INTO payment_methods (name, active, created_at) VALUES
            ("Dinheiro", 1, NOW()),
            ("Cheque", 1, NOW()),
            ("Cartão de Crédito", 1, NOW()),
            ("Cartão de Débito", 1, NOW()),
            ("Pix", 1, NOW())
        ');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DELETE FROM payment_methods WHERE name IN ("Dinheiro", "Cheque", "Cartão de Crédito", "Cartão de Débito", "Pix")');   
    }
}
