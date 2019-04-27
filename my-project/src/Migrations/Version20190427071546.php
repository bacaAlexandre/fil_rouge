<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190427071546 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE commentaires ADD utilisateur_id INT NOT NULL, ADD film_id INT NOT NULL');
        $this->addSql('ALTER TABLE commentaires ADD CONSTRAINT FK_D9BEC0C4FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs (id)');
        $this->addSql('ALTER TABLE commentaires ADD CONSTRAINT FK_D9BEC0C4567F5183 FOREIGN KEY (film_id) REFERENCES films (id)');
        $this->addSql('CREATE INDEX IDX_D9BEC0C4FB88E14F ON commentaires (utilisateur_id)');
        $this->addSql('CREATE INDEX IDX_D9BEC0C4567F5183 ON commentaires (film_id)');
        $this->addSql('ALTER TABLE favoris ADD utilisateur_id INT NOT NULL, ADD film_id INT NOT NULL');
        $this->addSql('ALTER TABLE favoris ADD CONSTRAINT FK_8933C432FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs (id)');
        $this->addSql('ALTER TABLE favoris ADD CONSTRAINT FK_8933C432567F5183 FOREIGN KEY (film_id) REFERENCES films (id)');
        $this->addSql('CREATE INDEX IDX_8933C432FB88E14F ON favoris (utilisateur_id)');
        $this->addSql('CREATE INDEX IDX_8933C432567F5183 ON favoris (film_id)');
        $this->addSql('ALTER TABLE notes ADD utilisateur_id INT NOT NULL, ADD film_id INT NOT NULL');
        $this->addSql('ALTER TABLE notes ADD CONSTRAINT FK_11BA68CFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs (id)');
        $this->addSql('ALTER TABLE notes ADD CONSTRAINT FK_11BA68C567F5183 FOREIGN KEY (film_id) REFERENCES films (id)');
        $this->addSql('CREATE INDEX IDX_11BA68CFB88E14F ON notes (utilisateur_id)');
        $this->addSql('CREATE INDEX IDX_11BA68C567F5183 ON notes (film_id)');
        $this->addSql('ALTER TABLE utilisateurs ADD is_actif TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE commentaires DROP FOREIGN KEY FK_D9BEC0C4FB88E14F');
        $this->addSql('ALTER TABLE commentaires DROP FOREIGN KEY FK_D9BEC0C4567F5183');
        $this->addSql('DROP INDEX IDX_D9BEC0C4FB88E14F ON commentaires');
        $this->addSql('DROP INDEX IDX_D9BEC0C4567F5183 ON commentaires');
        $this->addSql('ALTER TABLE commentaires DROP utilisateur_id, DROP film_id');
        $this->addSql('ALTER TABLE favoris DROP FOREIGN KEY FK_8933C432FB88E14F');
        $this->addSql('ALTER TABLE favoris DROP FOREIGN KEY FK_8933C432567F5183');
        $this->addSql('DROP INDEX IDX_8933C432FB88E14F ON favoris');
        $this->addSql('DROP INDEX IDX_8933C432567F5183 ON favoris');
        $this->addSql('ALTER TABLE favoris DROP utilisateur_id, DROP film_id');
        $this->addSql('ALTER TABLE notes DROP FOREIGN KEY FK_11BA68CFB88E14F');
        $this->addSql('ALTER TABLE notes DROP FOREIGN KEY FK_11BA68C567F5183');
        $this->addSql('DROP INDEX IDX_11BA68CFB88E14F ON notes');
        $this->addSql('DROP INDEX IDX_11BA68C567F5183 ON notes');
        $this->addSql('ALTER TABLE notes DROP utilisateur_id, DROP film_id');
        $this->addSql('ALTER TABLE utilisateurs DROP is_actif');
    }
}
