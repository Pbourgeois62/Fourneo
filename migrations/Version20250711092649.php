<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250711092649 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE unit ADD conversion_factor DOUBLE PRECISION DEFAULT 1 NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE unit ADD base_unit_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE unit ALTER name TYPE VARCHAR(255)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE unit ADD CONSTRAINT FK_DCBB0C53CCBBC969 FOREIGN KEY (base_unit_id) REFERENCES unit (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_DCBB0C535E237E06 ON unit (name)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_DCBB0C53CCBBC969 ON unit (base_unit_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE unit DROP CONSTRAINT FK_DCBB0C53CCBBC969
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_DCBB0C535E237E06
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_DCBB0C53CCBBC969
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE unit DROP conversion_factor
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE unit DROP base_unit_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE unit ALTER name TYPE VARCHAR(50)
        SQL);
    }
}
