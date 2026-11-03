-- Relie chaque inscription au stage choisi.
-- À lancer une fois dans phpMyAdmin, sur la base `oms_2026` (onglet SQL).
-- Ne crée aucune table : ajoute seulement une colonne à `inscription`.

ALTER TABLE `inscription`
  ADD COLUMN `id_stage` int DEFAULT NULL,
  ADD KEY `inscription_id_stage` (`id_stage`);
