<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240104103956 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comments_event (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, event_id INT NOT NULL, parent_id INT DEFAULT NULL, content LONGTEXT NOT NULL, is_active TINYINT(1) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7398EDC1A76ED395 (user_id), INDEX IDX_7398EDC171F7E88B (event_id), INDEX IDX_7398EDC1727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comments_event ADD CONSTRAINT FK_7398EDC1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comments_event ADD CONSTRAINT FK_7398EDC171F7E88B FOREIGN KEY (event_id) REFERENCES events (id)');
        $this->addSql('ALTER TABLE comments_event ADD CONSTRAINT FK_7398EDC1727ACA70 FOREIGN KEY (parent_id) REFERENCES comments_event (id)');
        $this->addSql('ALTER TABLE comment ADD event_id INT DEFAULT NULL, CHANGE subjects_id subjects_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C71F7E88B71F7E88B FOREIGN KEY (event_id) REFERENCES events (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_9474526C71F7E88B ON comment (event_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comments_event DROP FOREIGN KEY FK_7398EDC1A76ED395');
        $this->addSql('ALTER TABLE comments_event DROP FOREIGN KEY FK_7398EDC171F7E88B');
        $this->addSql('ALTER TABLE comments_event DROP FOREIGN KEY FK_7398EDC1727ACA70');
        $this->addSql('DROP TABLE comments_event');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C71F7E88B71F7E88B');
        $this->addSql('DROP INDEX IDX_9474526C71F7E88B ON comment');
        $this->addSql('ALTER TABLE comment DROP event_id, CHANGE subjects_id subjects_id INT NOT NULL');
    }
}
