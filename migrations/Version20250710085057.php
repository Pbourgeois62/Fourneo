<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250710085057 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE step_product DROP CONSTRAINT fk_63b78242cfae039
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_63b78242cfae039
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE step_product RENAME COLUMN recipe_product_id TO product_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE step_product ADD CONSTRAINT FK_63B782424584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_63B782424584665A ON step_product (product_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE step_product DROP CONSTRAINT FK_63B782424584665A
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_63B782424584665A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE step_product RENAME COLUMN product_id TO recipe_product_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE step_product ADD CONSTRAINT fk_63b78242cfae039 FOREIGN KEY (recipe_product_id) REFERENCES recipe_product (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_63b78242cfae039 ON step_product (recipe_product_id)
        SQL);
    }
}
