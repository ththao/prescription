ALTER TABLE "prescription" ADD COLUMN drug_name TEXT;

UPDATE prescription SET drug_name = (SELECT name FROM drug where id = prescription.drug_id);