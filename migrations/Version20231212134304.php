<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231212134304 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subject DROP FOREIGN KEY FK_FBCE3E7A9F4EDD82');
        $this->addSql('DROP INDEX IDX_FBCE3E7A9F4EDD82 ON subject');
        $this->addSql('ALTER TABLE subject CHANGE speacker_id_id speacker_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE subject ADD CONSTRAINT FK_FBCE3E7A213241A1 FOREIGN KEY (speacker_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_FBCE3E7A213241A1 ON subject (speacker_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subject DROP FOREIGN KEY FK_FBCE3E7A213241A1');
        $this->addSql('DROP INDEX IDX_FBCE3E7A213241A1 ON subject');
        $this->addSql('ALTER TABLE subject CHANGE speacker_id speacker_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE subject ADD CONSTRAINT FK_FBCE3E7A9F4EDD82 FOREIGN KEY (speacker_id_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_FBCE3E7A9F4EDD82 ON subject (speacker_id_id)');
    }
}
