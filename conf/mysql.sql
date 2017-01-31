CREATE TABLE IF NOT EXISTS person (
  id MEDIUMINT NOT NULL AUTO_INCREMENT,
  firstname VARCHAR(15) DEFAULT NULL,
  lastname VARCHAR(15) DEFAULT NULL,
  PRIMARY KEY   (id),
  KEY firstname (firstname),
  KEY lastname  (lastname)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS phonenumbers (
  person_id MEDIUMINT NOT NULL,
  type ENUM('home','work','mobile','other') DEFAULT 'home',
  phonenumber VARCHAR(20) DEFAULT NULL,
  PRIMARY KEY (person_id, type, phonenumber),
  FOREIGN KEY (person_id) REFERENCES person(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=INNODB DEFAULT CHARSET=latin1;

