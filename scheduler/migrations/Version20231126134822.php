<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231126134822 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE class_activity_entity ADD draft TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE personal_activity_entity ADD length INT NOT NULL');
        $this->addSql('ALTER TABLE schedule_window_entity ADD end DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE class_activity_entity DROP draft');
        $this->addSql('ALTER TABLE schedule_window_entity DROP end');
        $this->addSql('ALTER TABLE personal_activity_entity DROP length');
    }
}
