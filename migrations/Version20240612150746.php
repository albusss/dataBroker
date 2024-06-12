<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240612150746 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(/** @lang MySQL */'ALTER TABLE search_request RENAME search_requests');
        $this->addSql(/** @lang MySQL */'ALTER TABLE search_result RENAME search_results');
        $this->addSql(/** @lang MySQL */'ALTER TABLE user RENAME users');
    }

    public function down(Schema $schema): void
    {
        $this->addSql(/** @lang MySQL */'ALTER TABLE search_requests RENAME search_request');
        $this->addSql(/** @lang MySQL */'ALTER TABLE search_results RENAME search_result');
        $this->addSql(/** @lang MySQL */'ALTER TABLE users RENAME user');
    }
}
