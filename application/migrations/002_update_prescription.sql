CREATE TABLE "package" (
	"id"	INTEGER NOT NULL UNIQUE,
	"package_name"	TEXT NOT NULL,
	"notes"	TEXT,
	PRIMARY KEY("id" AUTOINCREMENT)
);

CREATE TABLE "package_prescription" (
	"id"	INTEGER NOT NULL UNIQUE,
	"package_id" INTEGER NOT NULL,
	"drug_id" INTEGER NOT NULL,
	"quantity" INTEGER NOT NULL,
	"time_in_day" INTEGER NOT NULL,
	"unit_in_time" INTEGER NOT NULL,
	"notes" TEXT,
	PRIMARY KEY("id" AUTOINCREMENT)
);

CREATE TABLE "package_orders" (
	"id"	INTEGER NOT NULL UNIQUE,
	"package_id" INTEGER NOT NULL,
	"service_id" INTEGER NOT NULL,
	"quantity" INTEGER NOT NULL,
	"notes" TEXT,
	PRIMARY KEY("id" AUTOINCREMENT)
);