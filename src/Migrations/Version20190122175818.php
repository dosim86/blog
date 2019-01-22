<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190122175818 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user CHANGE email email VARCHAR(50) NOT NULL, CHANGE firstname firstname VARCHAR(50) NOT NULL, CHANGE username username VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE like_article CHANGE user_id user_id INT UNSIGNED NOT NULL, CHANGE target_id target_id INT UNSIGNED NOT NULL, CHANGE value value SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE like_comment CHANGE user_id user_id INT UNSIGNED NOT NULL, CHANGE target_id target_id INT UNSIGNED NOT NULL, CHANGE value value SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE seo CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE tag CHANGE name name VARCHAR(50) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE like_article CHANGE user_id user_id INT NOT NULL, CHANGE target_id target_id INT NOT NULL, CHANGE value value INT NOT NULL');
        $this->addSql('ALTER TABLE like_comment CHANGE user_id user_id INT NOT NULL, CHANGE target_id target_id INT NOT NULL, CHANGE value value INT NOT NULL');
        $this->addSql('ALTER TABLE seo CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE tag CHANGE name name VARCHAR(255) NOT NULL COLLATE utf8_general_ci');
        $this->addSql('ALTER TABLE user CHANGE email email VARCHAR(180) NOT NULL COLLATE utf8_general_ci, CHANGE firstname firstname VARCHAR(255) NOT NULL COLLATE utf8_general_ci, CHANGE username username VARCHAR(100) NOT NULL COLLATE utf8_general_ci');
    }
}
