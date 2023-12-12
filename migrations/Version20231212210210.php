<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231212210210 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subject DROP FOREIGN KEY FK_FBCE3E7A9D6A1065');
        $this->addSql('ALTER TABLE subject ADD CONSTRAINT FK_FBCE3E7A9D6A1065 FOREIGN KEY (events_id) REFERENCES events (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subject DROP FOREIGN KEY FK_FBCE3E7A9D6A1065');
        $this->addSql('ALTER TABLE subject ADD CONSTRAINT FK_FBCE3E7A9D6A1065 FOREIGN KEY (events_id) REFERENCES events (id) ON UPDATE NO ACTION ON DELETE CASCADE');
    }
}
