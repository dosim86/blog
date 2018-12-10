<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181210140229 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, article_id INT NOT NULL, text VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_9474526C7E3C61F9 (owner_id), INDEX IDX_9474526C7294869C (article_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C7294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE article RENAME INDEX uniq_c0155143989d9b62 TO UNIQ_23A0E66989D9B62');
        $this->addSql('ALTER TABLE article RENAME INDEX idx_c0155143f675f31b TO IDX_23A0E66F675F31B');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE comment');
        $this->addSql('ALTER TABLE article RENAME INDEX uniq_23a0e66989d9b62 TO UNIQ_C0155143989D9B62');
        $this->addSql('ALTER TABLE article RENAME INDEX idx_23a0e66f675f31b TO IDX_C0155143F675F31B');
    }
}
