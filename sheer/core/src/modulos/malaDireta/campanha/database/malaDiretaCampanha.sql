CREATE TABLE sh_ml_campanha (
	id 						CHAR(36)NOT NULL,
	idRemetente				CHAR(36)NOT NULL,
	assunto					VARCHAR(128) NOT NULL,
	html					TEXT NOT NULL,
	criadoEm				DATE NOT NULL,
	criadoPor				CHAR(36) NOT NULL,
	atualizadoEm			DATETIME NULL,
	atualizadoPor			CHAR(36) NULL,
	
	CONSTRAINT SH_ML_CAMPANHA PRIMARY KEY (id),
	CONSTRAINT SH_ML_CAMPANHA_REMETENTE FOREIGN KEY (idRemetente) REFERENCES sh_ml_remetente (id)
	
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
