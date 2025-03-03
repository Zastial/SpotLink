<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use App\Enum\Role;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250226212308 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO role (id, name) VALUES (1, 'USER')");
        $this->addSql("INSERT INTO role (id, name) VALUES (2, 'ADMIN')");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
    }
}
