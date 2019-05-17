<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190418103111 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, question_id INT NOT NULL, user_id INT DEFAULT NULL, author VARCHAR(255) NOT NULL, date DATETIME NOT NULL, content LONGTEXT NOT NULL, value INT NOT NULL, INDEX IDX_9474526C1E27F6BF (question_id), INDEX IDX_9474526CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, user_id INT NOT NULL, author VARCHAR(255) NOT NULL, content VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, value INT NOT NULL, INDEX IDX_B6F7494E12469DE2 (category_id), INDEX IDX_B6F7494EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question_like (id INT AUTO_INCREMENT NOT NULL, question_id INT DEFAULT NULL, user_id INT DEFAULT NULL, INDEX IDX_F28DAD5C1E27F6BF (question_id), INDEX IDX_F28DAD5CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(50) NOT NULL, date_creation DATETIME NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C1E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494E12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE question_like ADD CONSTRAINT FK_F28DAD5C1E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE question_like ADD CONSTRAINT FK_F28DAD5CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494E12469DE2');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C1E27F6BF');
        $this->addSql('ALTER TABLE question_like DROP FOREIGN KEY FK_F28DAD5C1E27F6BF');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA76ED395');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494EA76ED395');
        $this->addSql('ALTER TABLE question_like DROP FOREIGN KEY FK_F28DAD5CA76ED395');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE question_like');
        $this->addSql('DROP TABLE user');
    }
}
