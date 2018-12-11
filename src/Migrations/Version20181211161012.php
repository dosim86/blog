<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181211161012 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE like_article MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE like_article DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE like_article DROP id');
        $this->addSql('ALTER TABLE like_article ADD PRIMARY KEY (user_id, target_id)');
        $this->addSql('ALTER TABLE like_comment MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE like_comment DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE like_comment DROP id');
        $this->addSql('ALTER TABLE like_comment ADD PRIMARY KEY (user_id, target_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE like_article DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE like_article ADD id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE like_article ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE like_comment DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE like_comment ADD id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE like_comment ADD PRIMARY KEY (id)');
    }
}
