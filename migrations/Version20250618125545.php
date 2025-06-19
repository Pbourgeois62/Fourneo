<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250618125545 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE delivery_note ADD image_name VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE delivery_note ADD image_size INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE delivery_note ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE delivery_note ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE delivery_note ADD registered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE label DROP registered_at
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE label ADD registered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE delivery_note DROP image_name
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE delivery_note DROP image_size
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE delivery_note DROP created_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE delivery_note DROP updated_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE delivery_note DROP registered_at
        SQL);
    }
}
