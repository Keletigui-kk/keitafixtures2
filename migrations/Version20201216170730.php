<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201216170730 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users CHANGE forgot_password_token_requested_at forgot_password_token_requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE forgot_password_must_verified_before forgot_password_must_verified_before DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE forgot_password_verified_at forgot_password_verified_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users CHANGE forgot_password_token_requested_at forgot_password_token_requested_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE forgot_password_must_verified_before forgot_password_must_verified_before DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE forgot_password_verified_at forgot_password_verified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }
}
