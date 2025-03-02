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
     public function up(Schema $schema): void
    {
        foreach (Role::cases() as $role) {
            $this->addSql("INSERT INTO role (roleValue) VALUES ('{$role->value}')");
        }
    }

    public function down(Schema $schema): void
    {
        foreach (Role::cases() as $role) {
            $this->addSql("DELETE FROM roleValue WHERE name = '{$role->value}'");
        }
    }
}
