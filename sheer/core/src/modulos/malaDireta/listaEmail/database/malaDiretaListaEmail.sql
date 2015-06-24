CREATE TABLE sh_ml_listaEmail(

	id					CHAR(36) NOT NULL,
	idLista				CHAR(36) NOT NULL,
	nome				CHAR(36) NULL,
	email				CHAR(36) NOT NULL,
	enviar				INTEGER NOT NULL,
	adicionadoEm 		DATETIME NOT NULL,
	idImportacao		CHAR(36) NULL,
	
	CONSTRAINT SH_ML_LISTAEMAIL_PK PRIMARY KEY (id),
	CONSTRAINT SH_ML_LISTAEMAIL_LISTA FOREIGN KEY (idLista) REFERENCES sh_ml_lista (id)
	
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 2015.05.25 - NESTE PASSO O SISTEMA PASSA A QUEBRAR E SE FAZ NECESSÁRIO A IMPORTAÇÃO
ALTER TABLE sh_ml_listaEmail DROP COLUMN nome;
ALTER TABLE sh_ml_listaEmail DROP COLUMN email;
ALTER TABLE sh_ml_listaEmail ADD COLUMN idContato CHAR(36) NULL AFTER idLista;
ALTER TABLE sh_ml_listaEmail ADD CONSTRAINT SH_ML_LISTAEMAIL_CONTATO FOREIGN KEY (idContato) REFERENCES sh_ml_contato(id);
ALTER TABLE sh_ml_listaEmail ADD CONSTRAINT SH_ML_LISTAEMAIL_UNIQUE_LISTACONTATO UNIQUE(idLista, idContato);