CREATE TABLE sh_filePicture (

	id 					CHAR(36) NOT NULL,
	name				VARCHAR(256) NOT NULL,
	nameFull			VARCHAR(256) NOT NULL,
	nameExt				VARCHAR(256) NULL,
	legenda				VARCHAR(1024) NULL,
	adicionadoEm		DATETIME NOT NULL,
	adicionadoPor		CHAR(36) NULL,
	atualizadoEm		DATETIME NOT NULL,
	atualizadoPor		CHAR(36) NULL,
	downloads			INT NOT NULL DEFAULT 0,
	mimeType			VARCHAR(128) NULL,
	remove 				INT NOT NULL DEFAULT 2 COMMENT '1->SIM, 2->NAO',
	picsMap				TEXT NULL,
	
	CONSTRAINT SH_FILEPICTURE_PK PRIMARY KEY (id),
	CONSTRAINT SH_FILEPICTURE_ADD_FK FOREIGN KEY (adicionadoPor) REFERENCES sh_user(id),
	CONSTRAINT SH_FILEPICTURE_UPDATE_FK FOREIGN KEY (atualizadoPor) REFERENCES sh_user(id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8;