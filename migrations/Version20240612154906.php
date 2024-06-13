<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240612154906 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(/** @lang MySQL */'ALTER TABLE
          search_requests
        CHANGE
          firstname first_name VARCHAR(255) DEFAULT NULL,
        CHANGE
          lastname last_name VARCHAR(255) DEFAULT NULL,
        CHANGE
          status status VARCHAR(25) NOT NULL,
        CHANGE
          created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql(/** @lang MySQL */'CREATE INDEX idx_status ON search_requests (status)');
        $this->addSql(/** @lang MySQL */'CREATE INDEX idx_created_at ON search_requests (created_at)');
        $this->addSql(/** @lang MySQL */'ALTER TABLE search_results DROP FOREIGN KEY FK_CA88AE0C9AC8886F');
        $this->addSql(/** @lang MySQL */'ALTER TABLE
          search_results
        CHANGE
          search_request_id search_request_id INT NOT NULL,
        CHANGE
          created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
        CHANGE
          fullname full_name VARCHAR(255) DEFAULT NULL');
        $this->addSql(/** @lang MySQL */'ALTER TABLE
          search_results
        ADD
          CONSTRAINT FK_12720B8E9AC8886F FOREIGN KEY (search_request_id) REFERENCES search_requests (id) ON DELETE CASCADE');
        $this->addSql(/** @lang MySQL */'CREATE INDEX idx_parser_name ON search_results (parser_name)');
        $this->addSql(/** @lang MySQL */'CREATE INDEX idx_created_at ON search_results (created_at)');
        $this->addSql(/** @lang MySQL */'ALTER TABLE search_results RENAME INDEX idx_ca88ae0c9ac8886f TO idx_search_request');
        $this->addSql(/** @lang MySQL */'ALTER TABLE users CHANGE username username VARCHAR(255) NOT NULL');
        $this->addSql(/** @lang MySQL */'ALTER TABLE users RENAME INDEX uniq_8d93d649f85e0677 TO uniq_username');
        $this->addSql(/** @lang MySQL */'ALTER TABLE user_search_requests DROP FOREIGN KEY FK_D0A7827EA76ED395');
        $this->addSql(/** @lang MySQL */'ALTER TABLE user_search_requests DROP FOREIGN KEY FK_D0A7827E9AC8886F');
        $this->addSql(/** @lang MySQL */'ALTER TABLE
          user_search_requests
        ADD
          CONSTRAINT FK_790E116BA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql(/** @lang MySQL */'ALTER TABLE
          user_search_requests
        ADD
          CONSTRAINT FK_790E116B9AC8886F FOREIGN KEY (search_request_id) REFERENCES search_requests (id) ON DELETE CASCADE');
        $this->addSql(/** @lang MySQL */'ALTER TABLE user_search_requests RENAME INDEX idx_d0a7827ea76ed395 TO IDX_790E116BA76ED395');
        $this->addSql(/** @lang MySQL */'ALTER TABLE user_search_requests RENAME INDEX idx_d0a7827e9ac8886f TO IDX_790E116B9AC8886F');
    }

    public function down(Schema $schema): void
    {
        $this->addSql(/** @lang MySQL */'DROP INDEX idx_status ON search_requests');
        $this->addSql(/** @lang MySQL */'DROP INDEX idx_created_at ON search_requests');
        $this->addSql(/** @lang MySQL */'ALTER TABLE
          search_requests
        CHANGE
          first_name firstname VARCHAR(255) DEFAULT NULL,
        CHANGE
          last_name lastname VARCHAR(255) DEFAULT NULL,
        CHANGE
          created_at created_at DATETIME NOT NULL,
        CHANGE
          STATUS STATUS VARCHAR(255) NOT NULL');
        $this->addSql(/** @lang MySQL */'ALTER TABLE search_results DROP FOREIGN KEY FK_12720B8E9AC8886F');
        $this->addSql(/** @lang MySQL */'DROP INDEX idx_parser_name ON search_results');
        $this->addSql(/** @lang MySQL */'DROP INDEX idx_created_at ON search_results');
        $this->addSql(/** @lang MySQL */'ALTER TABLE
          search_results
        CHANGE
          search_request_id search_request_id INT DEFAULT NULL,
        CHANGE
          created_at created_at DATETIME NOT NULL,
        CHANGE
          full_name fullname VARCHAR(255) DEFAULT NULL');
        $this->addSql(/** @lang MySQL */'ALTER TABLE
          search_results
        ADD
          CONSTRAINT FK_CA88AE0C9AC8886F FOREIGN KEY (search_request_id) REFERENCES search_requests (id)');
        $this->addSql(/** @lang MySQL */'ALTER TABLE search_results RENAME INDEX idx_search_request TO IDX_CA88AE0C9AC8886F');
        $this->addSql(/** @lang MySQL */'ALTER TABLE user_search_requests DROP FOREIGN KEY FK_790E116BA76ED395');
        $this->addSql(/** @lang MySQL */'ALTER TABLE user_search_requests DROP FOREIGN KEY FK_790E116B9AC8886F');
        $this->addSql(/** @lang MySQL */'ALTER TABLE
          user_search_requests
        ADD
          CONSTRAINT FK_D0A7827EA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql(/** @lang MySQL */'ALTER TABLE
          user_search_requests
        ADD
          CONSTRAINT FK_D0A7827E9AC8886F FOREIGN KEY (search_request_id) REFERENCES search_requests (id) ON DELETE CASCADE');
        $this->addSql(/** @lang MySQL */'ALTER TABLE user_search_requests RENAME INDEX idx_790e116ba76ed395 TO IDX_D0A7827EA76ED395');
        $this->addSql(/** @lang MySQL */'ALTER TABLE user_search_requests RENAME INDEX idx_790e116b9ac8886f TO IDX_D0A7827E9AC8886F');
        $this->addSql(/** @lang MySQL */'ALTER TABLE users CHANGE username username VARCHAR(180) NOT NULL');
        $this->addSql(/** @lang MySQL */'ALTER TABLE users RENAME INDEX uniq_username TO UNIQ_8D93D649F85E0677');
    }
}
