CREATE TABLE "forms" (
	"id"	INTEGER PRIMARY KEY AUTOINCREMENT,
	"name"	TEXT,
	"group"	TEXT,
	"year"	TEXT
);

CREATE TABLE "registered" (
	"id"	INTEGER PRIMARY KEY AUTOINCREMENT,
	"term"	TEXT,
	"form"	TEXT,
	"subject"	TEXT,
	"student"	TEXT,
	"year"	TEXT,
	"group"	TEXT
);

CREATE TABLE "scores" (
	"id"	INTEGER PRIMARY KEY AUTOINCREMENT,
	"student"	TEXT,
	"subject"	TEXT,
	"term"	TEXT,
	"form"	TEXT,
	"score"	TEXT,
	"end_term"	TEXT,
	"ca1"	TEXT,
	"ca2"	TEXT,
	"year"	TEXT,
	"group"	TEXT
);

CREATE TABLE `staff` (id INTEGER primary key autoincrement, phone TEXT, fullname TEXT, password TEXT, role TEXT);

CREATE TABLE stamps (id INTEGER PRIMARY KEY AUTOINCREMENT, year TEXT, term TEXT, file TEXT);

CREATE TABLE "student" (
	"id"	INTEGER PRIMARY KEY AUTOINCREMENT,
	"regnumber"	TEXT,
	"fullname"	TEXT,
	"password"	TEXT
);

CREATE TABLE `subject` (id INTEGER primary key autoincrement, name TEXT);

CREATE TABLE `systemctl` (name TEXT, value TEXT);

CREATE TABLE `terms` (id INTEGER primary key autoincrement, name TEXT, form TEXT);

CREATE TABLE `year` (id INTEGER primary key autoincrement, name TEXT, fees TEXT, uniform TEXT);