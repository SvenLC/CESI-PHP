<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200128093525 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE serie_serie_genre (serie_id INT NOT NULL, serie_genre_id INT NOT NULL, INDEX IDX_4C60353FD94388BD (serie_id), INDEX IDX_4C60353FA107C27 (serie_genre_id), PRIMARY KEY(serie_id, serie_genre_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE movie_movie_genre (movie_id INT NOT NULL, movie_genre_id INT NOT NULL, INDEX IDX_D294A5938F93B6FC (movie_id), INDEX IDX_D294A5939E604892 (movie_genre_id), PRIMARY KEY(movie_id, movie_genre_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE serie_serie_genre ADD CONSTRAINT FK_4C60353FD94388BD FOREIGN KEY (serie_id) REFERENCES serie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE serie_serie_genre ADD CONSTRAINT FK_4C60353FA107C27 FOREIGN KEY (serie_genre_id) REFERENCES serie_genre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE movie_movie_genre ADD CONSTRAINT FK_D294A5938F93B6FC FOREIGN KEY (movie_id) REFERENCES movie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE movie_movie_genre ADD CONSTRAINT FK_D294A5939E604892 FOREIGN KEY (movie_genre_id) REFERENCES movie_genre (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE serie_serie_genre');
        $this->addSql('DROP TABLE movie_movie_genre');
    }
}
