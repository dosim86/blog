<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181222163921 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE article ADD like_count INT NOT NULL, ADD dislike_count INT NOT NULL, DROP likes, DROP dislikes');
        $this->addSql('ALTER TABLE comment ADD like_count INT NOT NULL, ADD dislike_count INT NOT NULL, DROP likes, DROP dislikes');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE article ADD likes INT NOT NULL, ADD dislikes INT NOT NULL, DROP like_count, DROP dislike_count');
        $this->addSql('ALTER TABLE comment ADD likes INT NOT NULL, ADD dislikes INT NOT NULL, DROP like_count, DROP dislike_count');
    }
}
