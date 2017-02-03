CREATE TABLE IF NOT EXISTS contactinfo (
contact_id MEDIUMINT NOT NULL,
type ENUM('HomePhone','WorkPhone','MobilePhone','OtherPhone','Email', 'Other') DEFAULT 'HomePhone',
info VARCHAR(20) DEFAULT NULL,
PRIMARY KEY (contact_id, type, info),
FOREIGN KEY (contact_id) REFERENCES contact(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=INNODB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS contact (
id MEDIUMINT NOT NULL AUTO_INCREMENT,
firstname VARCHAR(20) DEFAULT NULL,
middlename VARCHAR (7) DEFAULT NULL,
lastname VARCHAR(20) DEFAULT NULL,
PRIMARY KEY (id),
KEY firstname (firstname),
KEY lastname (lastname)
) ENGINE=INNODB DEFAULT CHARSET=latin1;