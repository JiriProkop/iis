<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231122165422 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE class_activity_entity (id INT AUTO_INCREMENT NOT NULL, class_id INT NOT NULL, teacher_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, repetition VARCHAR(50) DEFAULT NULL, length INT NOT NULL, INDEX IDX_C0912CDFEA000B10 (class_id), INDEX IDX_C0912CDF41807E1D (teacher_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE class_activity_entity_room_entity (class_activity_entity_id INT NOT NULL, room_entity_id INT NOT NULL, INDEX IDX_25DB67455EAD7F81 (class_activity_entity_id), INDEX IDX_25DB67451D8C9D34 (room_entity_id), PRIMARY KEY(class_activity_entity_id, room_entity_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE class_entity (id INT AUTO_INCREMENT NOT NULL, guarantor_id INT DEFAULT NULL, abbreviation VARCHAR(20) NOT NULL, name VARCHAR(100) NOT NULL, anotation LONGTEXT NOT NULL, credits INT NOT NULL, INDEX IDX_C17406D05C3575A7 (guarantor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE class_entity_person_entity (class_entity_id INT NOT NULL, person_entity_id INT NOT NULL, INDEX IDX_7E2B8669EAFAE262 (class_entity_id), INDEX IDX_7E2B8669390C9EF7 (person_entity_id), PRIMARY KEY(class_entity_id, person_entity_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE person_entity (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, role VARCHAR(100) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE personal_activity_entity (id INT AUTO_INCREMENT NOT NULL, room_id INT DEFAULT NULL, person_id INT NOT NULL, description LONGTEXT DEFAULT NULL, repetition VARCHAR(50) DEFAULT NULL, INDEX IDX_8135607754177093 (room_id), INDEX IDX_81356077217BBB47 (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE room_entity (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(10) NOT NULL, type VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE schedule_window_entity (id INT AUTO_INCREMENT NOT NULL, class_activity_id INT DEFAULT NULL, personal_activity_id INT DEFAULT NULL, start DATETIME NOT NULL, INDEX IDX_804F44FBB9AEAFA0 (class_activity_id), INDEX IDX_804F44FB575613D9 (personal_activity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE class_activity_entity ADD CONSTRAINT FK_C0912CDFEA000B10 FOREIGN KEY (class_id) REFERENCES class_entity (id)');
        $this->addSql('ALTER TABLE class_activity_entity ADD CONSTRAINT FK_C0912CDF41807E1D FOREIGN KEY (teacher_id) REFERENCES person_entity (id)');
        $this->addSql('ALTER TABLE class_activity_entity_room_entity ADD CONSTRAINT FK_25DB67455EAD7F81 FOREIGN KEY (class_activity_entity_id) REFERENCES class_activity_entity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE class_activity_entity_room_entity ADD CONSTRAINT FK_25DB67451D8C9D34 FOREIGN KEY (room_entity_id) REFERENCES room_entity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE class_entity ADD CONSTRAINT FK_C17406D05C3575A7 FOREIGN KEY (guarantor_id) REFERENCES person_entity (id)');
        $this->addSql('ALTER TABLE class_entity_person_entity ADD CONSTRAINT FK_7E2B8669EAFAE262 FOREIGN KEY (class_entity_id) REFERENCES class_entity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE class_entity_person_entity ADD CONSTRAINT FK_7E2B8669390C9EF7 FOREIGN KEY (person_entity_id) REFERENCES person_entity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE personal_activity_entity ADD CONSTRAINT FK_8135607754177093 FOREIGN KEY (room_id) REFERENCES room_entity (id)');
        $this->addSql('ALTER TABLE personal_activity_entity ADD CONSTRAINT FK_81356077217BBB47 FOREIGN KEY (person_id) REFERENCES person_entity (id)');
        $this->addSql('ALTER TABLE schedule_window_entity ADD CONSTRAINT FK_804F44FBB9AEAFA0 FOREIGN KEY (class_activity_id) REFERENCES class_activity_entity (id)');
        $this->addSql('ALTER TABLE schedule_window_entity ADD CONSTRAINT FK_804F44FB575613D9 FOREIGN KEY (personal_activity_id) REFERENCES personal_activity_entity (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE class_activity_entity DROP FOREIGN KEY FK_C0912CDFEA000B10');
        $this->addSql('ALTER TABLE class_activity_entity DROP FOREIGN KEY FK_C0912CDF41807E1D');
        $this->addSql('ALTER TABLE class_activity_entity_room_entity DROP FOREIGN KEY FK_25DB67455EAD7F81');
        $this->addSql('ALTER TABLE class_activity_entity_room_entity DROP FOREIGN KEY FK_25DB67451D8C9D34');
        $this->addSql('ALTER TABLE class_entity DROP FOREIGN KEY FK_C17406D05C3575A7');
        $this->addSql('ALTER TABLE class_entity_person_entity DROP FOREIGN KEY FK_7E2B8669EAFAE262');
        $this->addSql('ALTER TABLE class_entity_person_entity DROP FOREIGN KEY FK_7E2B8669390C9EF7');
        $this->addSql('ALTER TABLE personal_activity_entity DROP FOREIGN KEY FK_8135607754177093');
        $this->addSql('ALTER TABLE personal_activity_entity DROP FOREIGN KEY FK_81356077217BBB47');
        $this->addSql('ALTER TABLE schedule_window_entity DROP FOREIGN KEY FK_804F44FBB9AEAFA0');
        $this->addSql('ALTER TABLE schedule_window_entity DROP FOREIGN KEY FK_804F44FB575613D9');
        $this->addSql('DROP TABLE class_activity_entity');
        $this->addSql('DROP TABLE class_activity_entity_room_entity');
        $this->addSql('DROP TABLE class_entity');
        $this->addSql('DROP TABLE class_entity_person_entity');
        $this->addSql('DROP TABLE person_entity');
        $this->addSql('DROP TABLE personal_activity_entity');
        $this->addSql('DROP TABLE room_entity');
        $this->addSql('DROP TABLE schedule_window_entity');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
