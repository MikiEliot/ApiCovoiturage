<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240303205447 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE compte (id INT AUTO_INCREMENT NOT NULL, login VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_CFF65260AA08CB10 (login), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE eleve (id INT AUTO_INCREMENT NOT NULL, voiture_id INT DEFAULT NULL, habiter_id INT NOT NULL, lier_id INT NOT NULL, prenom VARCHAR(50) NOT NULL, nom VARCHAR(50) NOT NULL, telephone VARCHAR(50) NOT NULL, email VARCHAR(50) NOT NULL, UNIQUE INDEX UNIQ_ECA105F7181A8BA (voiture_id), UNIQUE INDEX UNIQ_ECA105F77F15D43A (habiter_id), UNIQUE INDEX UNIQ_ECA105F7F7652B75 (lier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE eleve_trajet (eleve_id INT NOT NULL, trajet_id INT NOT NULL, INDEX IDX_F292934DA6CC7B2 (eleve_id), INDEX IDX_F292934DD12A823 (trajet_id), PRIMARY KEY(eleve_id, trajet_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE marque (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trajet (id INT AUTO_INCREMENT NOT NULL, id_conducteur INT NOT NULL, id_ville_arrivee INT NOT NULL, id_ville_depart INT NOT NULL, distance DOUBLE PRECISION NOT NULL, date_trajet DATETIME NOT NULL, places INT NOT NULL, INDEX IDX_2B5BA98C86EDF194 (id_conducteur), INDEX IDX_2B5BA98CCE6859CD (id_ville_arrivee), INDEX IDX_2B5BA98C2E1C84D0 (id_ville_depart), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ville (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, cp VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ville_eleve (ville_id INT NOT NULL, eleve_id INT NOT NULL, INDEX IDX_6324EA28A73F0036 (ville_id), INDEX IDX_6324EA28A6CC7B2 (eleve_id), PRIMARY KEY(ville_id, eleve_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE voiture (id INT AUTO_INCREMENT NOT NULL, associer_id INT DEFAULT NULL, immatriculation VARCHAR(20) NOT NULL, modele VARCHAR(30) NOT NULL, places INT NOT NULL, INDEX IDX_E9E2810FBB070F6A (associer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE eleve ADD CONSTRAINT FK_ECA105F7181A8BA FOREIGN KEY (voiture_id) REFERENCES voiture (id)');
        $this->addSql('ALTER TABLE eleve ADD CONSTRAINT FK_ECA105F77F15D43A FOREIGN KEY (habiter_id) REFERENCES ville (id)');
        $this->addSql('ALTER TABLE eleve ADD CONSTRAINT FK_ECA105F7F7652B75 FOREIGN KEY (lier_id) REFERENCES compte (id)');
        $this->addSql('ALTER TABLE eleve_trajet ADD CONSTRAINT FK_F292934DA6CC7B2 FOREIGN KEY (eleve_id) REFERENCES eleve (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE eleve_trajet ADD CONSTRAINT FK_F292934DD12A823 FOREIGN KEY (trajet_id) REFERENCES trajet (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trajet ADD CONSTRAINT FK_2B5BA98C86EDF194 FOREIGN KEY (id_conducteur) REFERENCES eleve (id)');
        $this->addSql('ALTER TABLE trajet ADD CONSTRAINT FK_2B5BA98CCE6859CD FOREIGN KEY (id_ville_arrivee) REFERENCES ville (id)');
        $this->addSql('ALTER TABLE trajet ADD CONSTRAINT FK_2B5BA98C2E1C84D0 FOREIGN KEY (id_ville_depart) REFERENCES ville (id)');
        $this->addSql('ALTER TABLE ville_eleve ADD CONSTRAINT FK_6324EA28A73F0036 FOREIGN KEY (ville_id) REFERENCES ville (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ville_eleve ADD CONSTRAINT FK_6324EA28A6CC7B2 FOREIGN KEY (eleve_id) REFERENCES eleve (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE voiture ADD CONSTRAINT FK_E9E2810FBB070F6A FOREIGN KEY (associer_id) REFERENCES marque (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE eleve DROP FOREIGN KEY FK_ECA105F7181A8BA');
        $this->addSql('ALTER TABLE eleve DROP FOREIGN KEY FK_ECA105F77F15D43A');
        $this->addSql('ALTER TABLE eleve DROP FOREIGN KEY FK_ECA105F7F7652B75');
        $this->addSql('ALTER TABLE eleve_trajet DROP FOREIGN KEY FK_F292934DA6CC7B2');
        $this->addSql('ALTER TABLE eleve_trajet DROP FOREIGN KEY FK_F292934DD12A823');
        $this->addSql('ALTER TABLE trajet DROP FOREIGN KEY FK_2B5BA98C86EDF194');
        $this->addSql('ALTER TABLE trajet DROP FOREIGN KEY FK_2B5BA98CCE6859CD');
        $this->addSql('ALTER TABLE trajet DROP FOREIGN KEY FK_2B5BA98C2E1C84D0');
        $this->addSql('ALTER TABLE ville_eleve DROP FOREIGN KEY FK_6324EA28A73F0036');
        $this->addSql('ALTER TABLE ville_eleve DROP FOREIGN KEY FK_6324EA28A6CC7B2');
        $this->addSql('ALTER TABLE voiture DROP FOREIGN KEY FK_E9E2810FBB070F6A');
        $this->addSql('DROP TABLE compte');
        $this->addSql('DROP TABLE eleve');
        $this->addSql('DROP TABLE eleve_trajet');
        $this->addSql('DROP TABLE marque');
        $this->addSql('DROP TABLE trajet');
        $this->addSql('DROP TABLE ville');
        $this->addSql('DROP TABLE ville_eleve');
        $this->addSql('DROP TABLE voiture');
    }
}
