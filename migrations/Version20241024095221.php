<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241024095221 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
{
    $this->addSql('ALTER TABLE measurement ADD date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
}

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__measurement AS SELECT id, weather_record_id, celsius FROM measurement');
        $this->addSql('DROP TABLE measurement');
        $this->addSql('CREATE TABLE measurement (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, weather_record_id INTEGER NOT NULL, celsius NUMERIC(3, 0) NOT NULL, CONSTRAINT FK_2CE0D8116773F43F FOREIGN KEY (weather_record_id) REFERENCES weather_record (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO measurement (id, weather_record_id, celsius) SELECT id, weather_record_id, celsius FROM __temp__measurement');
        $this->addSql('DROP TABLE __temp__measurement');
        $this->addSql('CREATE INDEX IDX_2CE0D8116773F43F ON measurement (weather_record_id)');

    }
}
