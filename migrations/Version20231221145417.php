<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231221145417 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comments_event ADD parent_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE comments_event ADD CONSTRAINT FK_7398EDC1727ACA70 FOREIGN KEY (parent_id) REFERENCES comments_event (id)');
        $this->addSql('CREATE INDEX IDX_7398EDC1727ACA70 ON comments_event (parent_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comments_event DROP FOREIGN KEY FK_7398EDC1727ACA70');
        $this->addSql('DROP INDEX IDX_7398EDC1727ACA70 ON comments_event');
        $this->addSql('ALTER TABLE comments_event DROP parent_id');
    }
}
