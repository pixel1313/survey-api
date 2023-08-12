<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230812221906 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE publisher (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE survey DROP FOREIGN KEY FK_AD5F9BFC7E3C61F9');
        $this->addSql('DROP INDEX IDX_AD5F9BFC7E3C61F9 ON survey');
        $this->addSql('ALTER TABLE survey CHANGE owner_id publisher_id INT NOT NULL');
        $this->addSql('ALTER TABLE survey ADD CONSTRAINT FK_AD5F9BFC40C86FCE FOREIGN KEY (publisher_id) REFERENCES publisher (id)');
        $this->addSql('CREATE INDEX IDX_AD5F9BFC40C86FCE ON survey (publisher_id)');
        $this->addSql('ALTER TABLE user ADD publisher_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64940C86FCE FOREIGN KEY (publisher_id) REFERENCES publisher (id)');
        $this->addSql('CREATE INDEX IDX_8D93D64940C86FCE ON user (publisher_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE survey DROP FOREIGN KEY FK_AD5F9BFC40C86FCE');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64940C86FCE');
        $this->addSql('DROP TABLE publisher');
        $this->addSql('DROP INDEX IDX_AD5F9BFC40C86FCE ON survey');
        $this->addSql('ALTER TABLE survey CHANGE publisher_id owner_id INT NOT NULL');
        $this->addSql('ALTER TABLE survey ADD CONSTRAINT FK_AD5F9BFC7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_AD5F9BFC7E3C61F9 ON survey (owner_id)');
        $this->addSql('DROP INDEX IDX_8D93D64940C86FCE ON user');
        $this->addSql('ALTER TABLE user DROP publisher_id');
    }
}
