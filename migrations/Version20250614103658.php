<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250614103658 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE product_category DROP CONSTRAINT fk_cdfc73564584665a
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_category DROP CONSTRAINT fk_cdfc735612469de2
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE product_category
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product ADD category_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product ADD CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_D34A04AD12469DE2 ON product (category_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE product_category (product_id INT NOT NULL, category_id INT NOT NULL, PRIMARY KEY(product_id, category_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_cdfc735612469de2 ON product_category (category_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_cdfc73564584665a ON product_category (product_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_category ADD CONSTRAINT fk_cdfc73564584665a FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_category ADD CONSTRAINT fk_cdfc735612469de2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product DROP CONSTRAINT FK_D34A04AD12469DE2
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_D34A04AD12469DE2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product DROP category_id
        SQL);
    }
}
