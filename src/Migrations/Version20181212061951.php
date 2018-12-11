<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181212061951 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE like_article ADD CONSTRAINT FK_51B74445158E0B66 FOREIGN KEY (target_id) REFERENCES article (id)');
        $this->addSql('CREATE INDEX IDX_51B74445158E0B66 ON like_article (target_id)');
        $this->addSql('ALTER TABLE like_comment ADD CONSTRAINT FK_C7F9184F158E0B66 FOREIGN KEY (target_id) REFERENCES comment (id)');
        $this->addSql('CREATE INDEX IDX_C7F9184F158E0B66 ON like_comment (target_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE like_article DROP FOREIGN KEY FK_51B74445158E0B66');
        $this->addSql('DROP INDEX IDX_51B74445158E0B66 ON like_article');
        $this->addSql('ALTER TABLE like_comment DROP FOREIGN KEY FK_C7F9184F158E0B66');
        $this->addSql('DROP INDEX IDX_C7F9184F158E0B66 ON like_comment');
    }
}
