<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260531091647 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE processus (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, product_id INTEGER NOT NULL, CONSTRAINT FK_EEEA8C1D4584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_EEEA8C1D4584665A ON processus (product_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__product AS SELECT id, name FROM product');
        $this->addSql('DROP TABLE product');
        $this->addSql('CREATE TABLE product (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO product (id, name) SELECT id, name FROM __temp__product');
        $this->addSql('DROP TABLE __temp__product');
        $this->addSql('CREATE TEMPORARY TABLE __temp__step AS SELECT id, name, amout, is_gain FROM step');
        $this->addSql('DROP TABLE step');
        $this->addSql('CREATE TABLE step (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, amout DOUBLE PRECISION NOT NULL, is_gain BOOLEAN NOT NULL, processus_id INTEGER NOT NULL, CONSTRAINT FK_43B9FE3CA55629DC FOREIGN KEY (processus_id) REFERENCES processus (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO step (id, name, amout, is_gain) SELECT id, name, amout, is_gain FROM __temp__step');
        $this->addSql('DROP TABLE __temp__step');
        $this->addSql('CREATE INDEX IDX_43B9FE3CA55629DC ON step (processus_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE processus');
        $this->addSql('ALTER TABLE product ADD COLUMN created_at DATETIME NOT NULL');
        $this->addSql('CREATE TEMPORARY TABLE __temp__step AS SELECT id, name, amout, is_gain FROM step');
        $this->addSql('DROP TABLE step');
        $this->addSql('CREATE TABLE step (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, amout DOUBLE PRECISION NOT NULL, is_gain BOOLEAN NOT NULL, product_id INTEGER DEFAULT NULL, CONSTRAINT FK_43B9FE3C4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO step (id, name, amout, is_gain) SELECT id, name, amout, is_gain FROM __temp__step');
        $this->addSql('DROP TABLE __temp__step');
        $this->addSql('CREATE INDEX IDX_43B9FE3C4584665A ON step (product_id)');
    }
}
