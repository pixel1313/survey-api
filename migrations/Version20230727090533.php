<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230727090533 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE survey ADD owner_id INT NOT NULL');
        $this->addSql('ALTER TABLE survey ADD CONSTRAINT FK_AD5F9BFC7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_AD5F9BFC7E3C61F9 ON survey (owner_id)');
        $this->addSql('ALTER TABLE survey_response ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE survey_response ADD CONSTRAINT FK_628C4DDCA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_628C4DDCA76ED395 ON survey_response (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE survey_response DROP FOREIGN KEY FK_628C4DDCA76ED395');
        $this->addSql('DROP INDEX IDX_628C4DDCA76ED395 ON survey_response');
        $this->addSql('ALTER TABLE survey_response DROP user_id');
        $this->addSql('ALTER TABLE survey DROP FOREIGN KEY FK_AD5F9BFC7E3C61F9');
        $this->addSql('DROP INDEX IDX_AD5F9BFC7E3C61F9 ON survey');
        $this->addSql('ALTER TABLE survey DROP owner_id');
    }
}
