<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250613195445 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE product_event ADD out_of_stock_date_time TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_event ADD unsold_quantity INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sale_event DROP status
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sale_event ALTER start_date TYPE TIMESTAMP(0) WITHOUT TIME ZONE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sale_event ALTER start_date SET NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sale_event ALTER end_date TYPE TIMESTAMP(0) WITHOUT TIME ZONE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sale_event ALTER end_date SET NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE product_event DROP out_of_stock_date_time
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_event DROP unsold_quantity
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sale_event ADD status VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sale_event ALTER start_date TYPE TIMESTAMP(0) WITHOUT TIME ZONE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sale_event ALTER start_date DROP NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sale_event ALTER end_date TYPE TIMESTAMP(0) WITHOUT TIME ZONE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sale_event ALTER end_date DROP NOT NULL
        SQL);
    }
}
