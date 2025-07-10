<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250709151332 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE recipe_product ADD sub_recipe_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE recipe_product ADD CONSTRAINT FK_9FAE0AED4BD5E4AA FOREIGN KEY (sub_recipe_id) REFERENCES recipe (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_9FAE0AED4BD5E4AA ON recipe_product (sub_recipe_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE recipe_product DROP CONSTRAINT FK_9FAE0AED4BD5E4AA
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_9FAE0AED4BD5E4AA
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE recipe_product DROP sub_recipe_id
        SQL);
    }
}
