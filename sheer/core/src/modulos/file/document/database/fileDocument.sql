CREATE TABLE sh_fileDocument (

	id 					CHAR(36) NOT NULL,
	size 				INT NOT NULL COMMENT 'bytes',
	name				VARCHAR(256) NOT NULL,
	nameFull			VARCHAR(256) NOT NULL,
	nameExt				VARCHAR(256) NULL,
	path				VARCHAR(256) NOT NULL,
	adicionadoEm		DATETIME NOT NULL,
	adicionadoPor		CHAR(36) NULL,
	downloads			INT NOT NULL DEFAULT 0,
	mimeType			VARCHAR(128) NULL,
	
	CONSTRAINT SH_FILEDOCUMENT_PK PRIMARY KEY (id),
	CONSTRAINT SH_FILEDOCUMENT_FK FOREIGN KEY (adicionadoPor) REFERENCES sh_user(id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE sh_fileDocument ADD COLUMN remove INT NOT NULL DEFAULT 2 COMMENT '1->SIM, 2->NAO';