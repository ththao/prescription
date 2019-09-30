ALTER TABLE "prescription" ADD COLUMN unit_price INTEGER AFTER unit_in_time;

UPDATE prescription SET unit_price = (SELECT price FROM drug where id = prescription.drug_id);