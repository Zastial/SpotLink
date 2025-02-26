<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250226205441 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("INSERT INTO category (id, name, marker_color) VALUES (1, 'Sport', '#006400')"); // Vert foncé
        $this->addSql("INSERT INTO category (id, name, marker_color) VALUES (2, 'Concert', '#FFA500')"); // Orange
        $this->addSql("INSERT INTO category (id, name, marker_color) VALUES (3, 'Conférence', '#8B0000')"); // Rouge foncé
        $this->addSql("INSERT INTO category (id, name, marker_color) VALUES (4, 'Exposition', '#4B0082')"); // Indigo
        $this->addSql("INSERT INTO category (id, name, marker_color) VALUES (5, 'Festival', '#FF1493')"); // Rose vif
        $this->addSql("INSERT INTO category (id, name, marker_color) VALUES (6, 'Théâtre', '#800000')"); // Marron
        $this->addSql("INSERT INTO category (id, name, marker_color) VALUES (7, 'Cinéma', '#00008B')"); // Bleu foncé
        $this->addSql("INSERT INTO category (id, name, marker_color) VALUES (8, 'Atelier', '#228B22')"); // Vert forêt
        $this->addSql("INSERT INTO category (id, name, marker_color) VALUES (9, 'Marché', '#D2691E')"); // Chocolat
        $this->addSql("INSERT INTO category (id, name, marker_color) VALUES (10, 'Autre', '#696969')"); // Gris foncé
    }
    

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
    }
}
