CREATE TABLE "diagnostic_template" (
	"id"	INTEGER NOT NULL UNIQUE,
	"diagnostic"	TEXT NOT NULL,
	PRIMARY KEY("id" AUTOINCREMENT)
);

INSERT INTO diagnostic_template (diagnostic) SELECT DISTINCT diagnostic FROM diagnostic;

ALTER TABLE "diagnostic" ADD COLUMN diagnostic_template_id INTEGER AFTER patient_id;

UPDATE diagnostic SET diagnostic_template_id = (SELECT id FROM diagnostic_template WHERE diagnostic_template.diagnostic = diagnostic.diagnostic);

CREATE TABLE "diagnostic_template_prescription" (
	"id"	INTEGER NOT NULL UNIQUE,
	"diagnostic_template_id" INTEGER NOT NULL,
	"drug_id" INTEGER NOT NULL,
	"most_used" INTEGER,
	PRIMARY KEY("id" AUTOINCREMENT)
);