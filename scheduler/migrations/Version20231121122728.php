<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231121122728 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE room_activity (id INT AUTO_INCREMENT NOT NULL, room_id INT DEFAULT NULL, activity_id INT DEFAULT NULL, INDEX IDX_C92C99E54177093 (room_id), INDEX IDX_C92C99E81C06096 (activity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_entity (id INT AUTO_INCREMENT NOT NULL, login VARCHAR(10) NOT NULL, password VARCHAR(255) NOT NULL, user_role VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_in_class (id INT AUTO_INCREMENT NOT NULL, user_entity_id INT DEFAULT NULL, class_id INT DEFAULT NULL, relationship_type VARCHAR(50) DEFAULT NULL, INDEX IDX_4733E3E181C5F0B9 (user_entity_id), INDEX IDX_4733E3E1EA000B10 (class_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE room_activity ADD CONSTRAINT FK_C92C99E54177093 FOREIGN KEY (room_id) REFERENCES room (id)');
        $this->addSql('ALTER TABLE room_activity ADD CONSTRAINT FK_C92C99E81C06096 FOREIGN KEY (activity_id) REFERENCES teaching_activity (id)');
        $this->addSql('ALTER TABLE user_in_class ADD CONSTRAINT FK_4733E3E181C5F0B9 FOREIGN KEY (user_entity_id) REFERENCES user_entity (id)');
        $this->addSql('ALTER TABLE user_in_class ADD CONSTRAINT FK_4733E3E1EA000B10 FOREIGN KEY (class_id) REFERENCES class_entity (id)');
        $this->addSql('DROP TABLE user');
        $this->addSql('ALTER TABLE class_entity ADD abbr VARCHAR(5) NOT NULL');
        $this->addSql('ALTER TABLE own_activity ADD room_id INT DEFAULT NULL, ADD teacher_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE own_activity ADD CONSTRAINT FK_5D991E3154177093 FOREIGN KEY (room_id) REFERENCES room (id)');
        $this->addSql('ALTER TABLE own_activity ADD CONSTRAINT FK_5D991E3141807E1D FOREIGN KEY (teacher_id) REFERENCES user_entity (id)');
        $this->addSql('CREATE INDEX IDX_5D991E3154177093 ON own_activity (room_id)');
        $this->addSql('CREATE INDEX IDX_5D991E3141807E1D ON own_activity (teacher_id)');
        $this->addSql('ALTER TABLE scheduler_window ADD personal_activity_id INT DEFAULT NULL, ADD teaching_activity_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE scheduler_window ADD CONSTRAINT FK_9DCE4568575613D9 FOREIGN KEY (personal_activity_id) REFERENCES own_activity (id)');
        $this->addSql('ALTER TABLE scheduler_window ADD CONSTRAINT FK_9DCE45681D6A1CBD FOREIGN KEY (teaching_activity_id) REFERENCES teaching_activity (id)');
        $this->addSql('CREATE INDEX IDX_9DCE4568575613D9 ON scheduler_window (personal_activity_id)');
        $this->addSql('CREATE INDEX IDX_9DCE45681D6A1CBD ON scheduler_window (teaching_activity_id)');
        $this->addSql('ALTER TABLE teaching_activity ADD teacher_id INT NOT NULL, ADD class_id INT NOT NULL');
        $this->addSql('ALTER TABLE teaching_activity ADD CONSTRAINT FK_221DD96041807E1D FOREIGN KEY (teacher_id) REFERENCES user_entity (id)');
        $this->addSql('ALTER TABLE teaching_activity ADD CONSTRAINT FK_221DD960EA000B10 FOREIGN KEY (class_id) REFERENCES class_entity (id)');
        $this->addSql('CREATE INDEX IDX_221DD96041807E1D ON teaching_activity (teacher_id)');
        $this->addSql('CREATE INDEX IDX_221DD960EA000B10 ON teaching_activity (class_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE own_activity DROP FOREIGN KEY FK_5D991E3141807E1D');
        $this->addSql('ALTER TABLE teaching_activity DROP FOREIGN KEY FK_221DD96041807E1D');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, login VARCHAR(10) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, password VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, user_role VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE room_activity DROP FOREIGN KEY FK_C92C99E54177093');
        $this->addSql('ALTER TABLE room_activity DROP FOREIGN KEY FK_C92C99E81C06096');
        $this->addSql('ALTER TABLE user_in_class DROP FOREIGN KEY FK_4733E3E181C5F0B9');
        $this->addSql('ALTER TABLE user_in_class DROP FOREIGN KEY FK_4733E3E1EA000B10');
        $this->addSql('DROP TABLE room_activity');
        $this->addSql('DROP TABLE user_entity');
        $this->addSql('DROP TABLE user_in_class');
        $this->addSql('ALTER TABLE class_entity DROP abbr');
        $this->addSql('ALTER TABLE teaching_activity DROP FOREIGN KEY FK_221DD960EA000B10');
        $this->addSql('DROP INDEX IDX_221DD96041807E1D ON teaching_activity');
        $this->addSql('DROP INDEX IDX_221DD960EA000B10 ON teaching_activity');
        $this->addSql('ALTER TABLE teaching_activity DROP teacher_id, DROP class_id');
        $this->addSql('ALTER TABLE scheduler_window DROP FOREIGN KEY FK_9DCE4568575613D9');
        $this->addSql('ALTER TABLE scheduler_window DROP FOREIGN KEY FK_9DCE45681D6A1CBD');
        $this->addSql('DROP INDEX IDX_9DCE4568575613D9 ON scheduler_window');
        $this->addSql('DROP INDEX IDX_9DCE45681D6A1CBD ON scheduler_window');
        $this->addSql('ALTER TABLE scheduler_window DROP personal_activity_id, DROP teaching_activity_id');
        $this->addSql('ALTER TABLE own_activity DROP FOREIGN KEY FK_5D991E3154177093');
        $this->addSql('DROP INDEX IDX_5D991E3154177093 ON own_activity');
        $this->addSql('DROP INDEX IDX_5D991E3141807E1D ON own_activity');
        $this->addSql('ALTER TABLE own_activity DROP room_id, DROP teacher_id');
    }
}
