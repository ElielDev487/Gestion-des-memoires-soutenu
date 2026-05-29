USE memoires_db;

-- ALTER TABLE memoire MODIFY COLUMN id_professeur INT NULL;
-- UPDATE utilisateur SET mot_de_passe = '$2y$10$OJfnjI.jqGcKJw36u4Z6Z.K546qOtxqECiKF/ejZkknCR/AQAns0S' WHERE 1;

ALTER TABLE memoire
ADD COLUMN directeur_nom VARCHAR(200) NULL
AFTER id_professeur;