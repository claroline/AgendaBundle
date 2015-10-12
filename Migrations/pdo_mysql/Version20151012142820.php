<?php

namespace Claroline\AgendaBundle\Migrations\pdo_mysql;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated migration based on mapping information: modify it with caution
 *
 * Generation date: 2015/10/12 02:28:21
 */
class Version20151012142820 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
            ALTER TABLE claro_event 
            ADD parent_id INT DEFAULT NULL
        ");
        $this->addSql("
            ALTER TABLE claro_event 
            ADD CONSTRAINT FK_B1ADDDB5727ACA70 FOREIGN KEY (parent_id) 
            REFERENCES claro_event (id) 
            ON DELETE CASCADE
        ");
        $this->addSql("
            CREATE INDEX IDX_B1ADDDB5727ACA70 ON claro_event (parent_id)
        ");
    }

    public function down(Schema $schema)
    {
        $this->addSql("
            ALTER TABLE claro_event 
            DROP FOREIGN KEY FK_B1ADDDB5727ACA70
        ");
        $this->addSql("
            DROP INDEX IDX_B1ADDDB5727ACA70 ON claro_event
        ");
        $this->addSql("
            ALTER TABLE claro_event 
            DROP parent_id
        ");
    }
}