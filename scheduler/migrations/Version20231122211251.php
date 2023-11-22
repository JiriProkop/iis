<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231122211251 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE person_entity ADD roles JSON NOT NULL, DROP role, CHANGE email email VARCHAR(180) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_928D74DEE7927C74 ON person_entity (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_928D74DEE7927C74 ON person_entity');
        $this->addSql('ALTER TABLE person_entity ADD role VARCHAR(100) DEFAULT NULL, DROP roles, CHANGE email email VARCHAR(255) NOT NULL');
    }
}
