<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250711145613 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE unit RENAME COLUMN factor TO factor_to_base
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE unit ALTER factor_to_base TYPE DOUBLE PRECISION
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE unit RENAME COLUMN factor_to_base TO factor
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE unit ALTER factor TYPE DOUBLE PRECISION
        SQL);
    }
}
