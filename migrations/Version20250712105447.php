<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250712105447 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE recipe ADD yield_unit_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE recipe ALTER yield TYPE DOUBLE PRECISION
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE recipe ADD CONSTRAINT FK_DA88B13785EE2642 FOREIGN KEY (yield_unit_id) REFERENCES unit (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_DA88B13785EE2642 ON recipe (yield_unit_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE recipe DROP CONSTRAINT FK_DA88B13785EE2642
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_DA88B13785EE2642
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE recipe DROP yield_unit_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE recipe ALTER yield TYPE INT
        SQL);
    }
}
