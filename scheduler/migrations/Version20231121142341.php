<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231121142341 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE own_activity DROP FOREIGN KEY FK_5D991E3154177093');
        $this->addSql('ALTER TABLE own_activity DROP FOREIGN KEY FK_5D991E3141807E1D');
        $this->addSql('ALTER TABLE room_activity DROP FOREIGN KEY FK_C92C99E54177093');
        $this->addSql('ALTER TABLE room_activity DROP FOREIGN KEY FK_C92C99E81C06096');
        $this->addSql('ALTER TABLE scheduler_window DROP FOREIGN KEY FK_9DCE4568575613D9');
        $this->addSql('ALTER TABLE scheduler_window DROP FOREIGN KEY FK_9DCE45681D6A1CBD');
        $this->addSql('ALTER TABLE teaching_activity DROP FOREIGN KEY FK_221DD96041807E1D');
        $this->addSql('ALTER TABLE teaching_activity DROP FOREIGN KEY FK_221DD960EA000B10');
        $this->addSql('ALTER TABLE user_in_class DROP FOREIGN KEY FK_4733E3E181C5F0B9');
        $this->addSql('ALTER TABLE user_in_class DROP FOREIGN KEY FK_4733E3E1EA000B10');
        $this->addSql('DROP TABLE class_entity');
        $this->addSql('DROP TABLE own_activity');
        $this->addSql('DROP TABLE room');
        $this->addSql('DROP TABLE room_activity');
        $this->addSql('DROP TABLE scheduler_window');
        $this->addSql('DROP TABLE teaching_activity');
        $this->addSql('DROP TABLE user_entity');
        $this->addSql('DROP TABLE user_in_class');
        $this->addSql('DROP TABLE messenger_messages');

        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE class_entity (id INT AUTO_INCREMENT NOT NULL, abbr VARCHAR(5) NOT NULL, name VARCHAR(255) NOT NULL, anotation LONGTEXT NOT NULL, credit_number INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE own_activity (id INT AUTO_INCREMENT NOT NULL, room_id INT DEFAULT NULL, teacher_id INT DEFAULT NULL, description LONGTEXT DEFAULT NULL, repetition VARCHAR(255) NOT NULL, INDEX IDX_5D991E3154177093 (room_id), INDEX IDX_5D991E3141807E1D (teacher_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE room (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, name VARCHAR(10) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE room_activity (id INT AUTO_INCREMENT NOT NULL, room_id INT DEFAULT NULL, activity_id INT DEFAULT NULL, INDEX IDX_C92C99E54177093 (room_id), INDEX IDX_C92C99E81C06096 (activity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE scheduler_window (id INT AUTO_INCREMENT NOT NULL, personal_activity_id INT DEFAULT NULL, teaching_activity_id INT DEFAULT NULL, start DATETIME NOT NULL, INDEX IDX_9DCE4568575613D9 (personal_activity_id), INDEX IDX_9DCE45681D6A1CBD (teaching_activity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE teaching_activity (id INT AUTO_INCREMENT NOT NULL, teacher_id INT NOT NULL, class_id INT NOT NULL, name VARCHAR(255) NOT NULL, repetition VARCHAR(255) NOT NULL, length INT NOT NULL, INDEX IDX_221DD96041807E1D (teacher_id), INDEX IDX_221DD960EA000B10 (class_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_entity (id INT AUTO_INCREMENT NOT NULL, login VARCHAR(10) NOT NULL, password VARCHAR(255) NOT NULL, user_role VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_in_class (id INT AUTO_INCREMENT NOT NULL, user_entity_id INT DEFAULT NULL, class_id INT DEFAULT NULL, relationship_type VARCHAR(50) DEFAULT NULL, INDEX IDX_4733E3E181C5F0B9 (user_entity_id), INDEX IDX_4733E3E1EA000B10 (class_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE own_activity ADD CONSTRAINT FK_5D991E3154177093 FOREIGN KEY (room_id) REFERENCES room (id)');
        $this->addSql('ALTER TABLE own_activity ADD CONSTRAINT FK_5D991E3141807E1D FOREIGN KEY (teacher_id) REFERENCES user_entity (id)');
        $this->addSql('ALTER TABLE room_activity ADD CONSTRAINT FK_C92C99E54177093 FOREIGN KEY (room_id) REFERENCES room (id)');
        $this->addSql('ALTER TABLE room_activity ADD CONSTRAINT FK_C92C99E81C06096 FOREIGN KEY (activity_id) REFERENCES teaching_activity (id)');
        $this->addSql('ALTER TABLE scheduler_window ADD CONSTRAINT FK_9DCE4568575613D9 FOREIGN KEY (personal_activity_id) REFERENCES own_activity (id)');
        $this->addSql('ALTER TABLE scheduler_window ADD CONSTRAINT FK_9DCE45681D6A1CBD FOREIGN KEY (teaching_activity_id) REFERENCES teaching_activity (id)');
        $this->addSql('ALTER TABLE teaching_activity ADD CONSTRAINT FK_221DD96041807E1D FOREIGN KEY (teacher_id) REFERENCES user_entity (id)');
        $this->addSql('ALTER TABLE teaching_activity ADD CONSTRAINT FK_221DD960EA000B10 FOREIGN KEY (class_id) REFERENCES class_entity (id)');
        $this->addSql('ALTER TABLE user_in_class ADD CONSTRAINT FK_4733E3E181C5F0B9 FOREIGN KEY (user_entity_id) REFERENCES user_entity (id)');
        $this->addSql('ALTER TABLE user_in_class ADD CONSTRAINT FK_4733E3E1EA000B10 FOREIGN KEY (class_id) REFERENCES class_entity (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE own_activity DROP FOREIGN KEY FK_5D991E3154177093');
        $this->addSql('ALTER TABLE own_activity DROP FOREIGN KEY FK_5D991E3141807E1D');
        $this->addSql('ALTER TABLE room_activity DROP FOREIGN KEY FK_C92C99E54177093');
        $this->addSql('ALTER TABLE room_activity DROP FOREIGN KEY FK_C92C99E81C06096');
        $this->addSql('ALTER TABLE scheduler_window DROP FOREIGN KEY FK_9DCE4568575613D9');
        $this->addSql('ALTER TABLE scheduler_window DROP FOREIGN KEY FK_9DCE45681D6A1CBD');
        $this->addSql('ALTER TABLE teaching_activity DROP FOREIGN KEY FK_221DD96041807E1D');
        $this->addSql('ALTER TABLE teaching_activity DROP FOREIGN KEY FK_221DD960EA000B10');
        $this->addSql('ALTER TABLE user_in_class DROP FOREIGN KEY FK_4733E3E181C5F0B9');
        $this->addSql('ALTER TABLE user_in_class DROP FOREIGN KEY FK_4733E3E1EA000B10');
        $this->addSql('DROP TABLE class_entity');
        $this->addSql('DROP TABLE own_activity');
        $this->addSql('DROP TABLE room');
        $this->addSql('DROP TABLE room_activity');
        $this->addSql('DROP TABLE scheduler_window');
        $this->addSql('DROP TABLE teaching_activity');
        $this->addSql('DROP TABLE user_entity');
        $this->addSql('DROP TABLE user_in_class');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
