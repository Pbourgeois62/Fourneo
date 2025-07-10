<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250710081303 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE product ADD size VARCHAR(50) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            -- ALTER TABLE recipe ALTER yield TYPE INT
            ALTER TABLE recipe ALTER COLUMN yield TYPE INT USING yield::integer;
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE recipe ALTER yield DROP NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE recipe ALTER yield TYPE VARCHAR(255)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE recipe ALTER yield SET NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product DROP size
        SQL);
    }
}
