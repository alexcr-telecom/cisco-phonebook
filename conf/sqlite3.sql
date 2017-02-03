CREATE TABLE contact (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  firstname VARCHAR(20) DEFAULT NULL,
  middlename VARCHAR(7) DEFAULT NULL,
  lastname VARCHAR(20) DEFAULT NULL
);

CREATE TABLE contacttype (
  type VARCHAR(20) PRIMARY KEY NOT NULL,
  abbreviation VARCHAR(2) NOT NULL
);

INSERT INTO contacttype VALUES ('HomePhone', 'H');
INSERT INTO contacttype VALUES ('WorkPhone', 'W');
INSERT INTO contacttype VALUES ('MobilePhone', 'M');
INSERT INTO contacttype VALUES ('OtherPhone', 'O');
INSERT INTO contacttype VALUES ('Email', 'E');

CREATE TABLE contactinfo (
  contact_id INTEGER NOT NULL,
  type VARCHAR(20) NOT NULL DEFAULT 'homephone',
  info VARCHAR(20) DEFAULT NULL,
  PRIMARY KEY (contact_id, type, info),
  FOREIGN KEY (contact_id) REFERENCES contact(id),
  FOREIGN KEY (type) REFERENCES contacttype(type)
);

CREATE INDEX contact_firstname ON contact (firstname);
CREATE INDEX contact_lastname ON contact (lastname);

