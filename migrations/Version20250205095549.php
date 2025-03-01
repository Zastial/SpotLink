<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250205095549 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("INSERT INTO status (id, name) VALUES (1, 'CREATED')");
        $this->addSql("INSERT INTO status (id, name) VALUES (2, 'AWAITING_VALIDATION')");
        $this->addSql("INSERT INTO status (id, name) VALUES (3, 'VALIDATED')");
        $this->addSql("INSERT INTO status (id, name) VALUES (4, 'TOTAL_REFUSED')");
        $this->addSql("INSERT INTO status (id, name) VALUES (5, 'PARTIAL_REFUSED')");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("DELETE FROM status WHERE id IN (1, 2, 3, 4, 5)");
    }
}
