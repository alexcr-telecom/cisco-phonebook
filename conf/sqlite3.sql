CREATE TABLE contact (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  firstname VARCHAR(15) DEFAULT NULL,
  lastname VARCHAR(15) DEFAULT NULL
);

CREATE TABLE contacttype (
  type VARCHAR(20) PRIMARY KEY NOT NULL
);

INSERT INTO contacttype(type) VALUES ('homephone');
INSERT INTO contacttype(type) VALUES ('workphone');
INSERT INTO contacttype(type) VALUES ('mobilephone');
INSERT INTO contacttype(type) VALUES ('otherphone');
INSERT INTO contacttype(type) VALUES ('email');

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

