ALTER TABLE "drug" ADD COLUMN in_price INTEGER AFTER unit;

ALTER TABLE "prescription" ADD COLUMN in_unit_price INTEGER AFTER date_created;