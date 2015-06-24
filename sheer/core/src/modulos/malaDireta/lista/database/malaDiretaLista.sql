CREATE TABLE sh_ml_lista (

	id					CHAR(36)NOT NULL,
	nome				VARCHAR(256)NOT NULL,
	criadoEm			DATETIME NOT NULL,
	criadoPor			CHAR(36) NOT NULL,
	totalEmails			INTEGER NOT NULL,
	totalHabilitados	INTEGER NOT NULL,
	tipo				INTEGER NOT NULL,
	classe				VARCHAR(256) NULL,
	
	CONSTRAINT SH_ML_LISTA_PK PRIMARY KEY (id)

)ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE sh_ml_listaSincronizacao (

	id					CHAR(36) NOT NULL,
	idLista				CHAR(36) NOT NULL,
	sincronizadoEm		DATETIME NOT NULL,
	total				INTEGER NOT NULL,
	novos				INTEGER NOT NULL,
	atualizados			INTEGER NOT NULL,
	
	CONSTRAINT SH_ML_LISTASINCRONIZACAO_PK PRIMARY KEY (id),
	CONSTRAINT SH_ML_LISTASINCRONIZACAO_LISTA_FK FOREIGN KEY (idLista) REFERENCES sh_ml_lista(id)

)ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE sh_ml_lista DROP COLUMN classe;
ALTER TABLE sh_ml_lista ADD COLUMN host 			VARCHAR(128) NULL;
ALTER TABLE sh_ml_lista ADD COLUMN username 		VARCHAR(128) NULL;
ALTER TABLE sh_ml_lista ADD COLUMN password 		VARCHAR(128) NULL;
ALTER TABLE sh_ml_lista ADD COLUMN databaseName		VARCHAR(128) NULL;
ALTER TABLE sh_ml_lista ADD COLUMN databaseTable 	VARCHAR(128) NULL;
ALTER TABLE sh_ml_lista ADD COLUMN fieldNome 		VARCHAR(32) NULL;
ALTER TABLE sh_ml_lista ADD COLUMN fieldEmail 		VARCHAR(32) NULL;
ALTER TABLE sh_ml_lista ADD COLUMN fieldEnviar 		VARCHAR(32) NULL;
ALTER TABLE sh_ml_lista ADD COLUMN fieldEnviarValor VARCHAR(8) NULL;