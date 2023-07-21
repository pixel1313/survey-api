<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230721183453 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE answer (id INT AUTO_INCREMENT NOT NULL, question_id INT NOT NULL, discr VARCHAR(255) NOT NULL, INDEX IDX_DADD4A251E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE choice_answer (id INT NOT NULL, choices LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE response_answer (id INT NOT NULL, response VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE survey_response (id INT AUTO_INCREMENT NOT NULL, survey_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_628C4DDCB3FE509D (survey_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE answer ADD CONSTRAINT FK_DADD4A251E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE choice_answer ADD CONSTRAINT FK_3FAAC6C6BF396750 FOREIGN KEY (id) REFERENCES answer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE response_answer ADD CONSTRAINT FK_B1A66D0ABF396750 FOREIGN KEY (id) REFERENCES answer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE survey_response ADD CONSTRAINT FK_628C4DDCB3FE509D FOREIGN KEY (survey_id) REFERENCES survey (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE answer DROP FOREIGN KEY FK_DADD4A251E27F6BF');
        $this->addSql('ALTER TABLE choice_answer DROP FOREIGN KEY FK_3FAAC6C6BF396750');
        $this->addSql('ALTER TABLE response_answer DROP FOREIGN KEY FK_B1A66D0ABF396750');
        $this->addSql('ALTER TABLE survey_response DROP FOREIGN KEY FK_628C4DDCB3FE509D');
        $this->addSql('DROP TABLE answer');
        $this->addSql('DROP TABLE choice_answer');
        $this->addSql('DROP TABLE response_answer');
        $this->addSql('DROP TABLE survey_response');
    }
}
