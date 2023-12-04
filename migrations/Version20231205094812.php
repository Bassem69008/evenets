<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231205094812 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subject_like DROP FOREIGN KEY FK_3EA9C46823EDC87');
        $this->addSql('ALTER TABLE subject_like DROP FOREIGN KEY FK_3EA9C468A76ED395');
        $this->addSql('ALTER TABLE subject_like ADD CONSTRAINT FK_3EA9C46823EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subject_like ADD CONSTRAINT FK_3EA9C468A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subject_like DROP FOREIGN KEY FK_3EA9C46823EDC87');
        $this->addSql('ALTER TABLE subject_like DROP FOREIGN KEY FK_3EA9C468A76ED395');
        $this->addSql('ALTER TABLE subject_like ADD CONSTRAINT FK_3EA9C46823EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE subject_like ADD CONSTRAINT FK_3EA9C468A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
