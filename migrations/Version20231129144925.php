<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231129144925 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574AB03A8386');
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574A896DBBDE');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574AB03A8386B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574A896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subject DROP FOREIGN KEY FK_FBCE3E7A8FDDAB70');
        $this->addSql('ALTER TABLE subject DROP FOREIGN KEY FK_FBCE3E7A896DBBDE');
        $this->addSql('ALTER TABLE subject DROP FOREIGN KEY FK_FBCE3E7A9D6A1065');
        $this->addSql('ALTER TABLE subject DROP FOREIGN KEY FK_FBCE3E7A9F4EDD82');
        $this->addSql('ALTER TABLE subject ADD CONSTRAINT FK_FBCE3E7A8FDDAB708FDDAB70 FOREIGN KEY (owner_id_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subject ADD CONSTRAINT FK_FBCE3E7A896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subject ADD CONSTRAINT FK_FBCE3E7A9D6A1065 FOREIGN KEY (events_id) REFERENCES events (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subject ADD CONSTRAINT FK_FBCE3E7A9F4EDD82 FOREIGN KEY (speacker_id_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subject DROP FOREIGN KEY FK_FBCE3E7A8FDDAB708FDDAB70');
        $this->addSql('ALTER TABLE subject DROP FOREIGN KEY FK_FBCE3E7A9F4EDD82');
        $this->addSql('ALTER TABLE subject DROP FOREIGN KEY FK_FBCE3E7A896DBBDE');
        $this->addSql('ALTER TABLE subject DROP FOREIGN KEY FK_FBCE3E7A9D6A1065');
        $this->addSql('ALTER TABLE subject ADD CONSTRAINT FK_FBCE3E7A8FDDAB70 FOREIGN KEY (owner_id_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE subject ADD CONSTRAINT FK_FBCE3E7A9F4EDD82 FOREIGN KEY (speacker_id_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE subject ADD CONSTRAINT FK_FBCE3E7A896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE subject ADD CONSTRAINT FK_FBCE3E7A9D6A1065 FOREIGN KEY (events_id) REFERENCES events (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574AB03A8386B03A8386');
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574A896DBBDE');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574AB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574A896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
