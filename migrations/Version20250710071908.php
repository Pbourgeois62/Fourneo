<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250710071908 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE product ADD is_recipe_result BOOLEAN DEFAULT false NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product ADD recipe_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product DROP size
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product ADD CONSTRAINT FK_D34A04AD59D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_D34A04AD59D8A214 ON product (recipe_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE recipe DROP status
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE product DROP CONSTRAINT FK_D34A04AD59D8A214
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_D34A04AD59D8A214
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product ADD size VARCHAR(50) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product DROP is_recipe_result
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product DROP recipe_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE recipe ADD status VARCHAR(50) NOT NULL
        SQL);
    }
}
