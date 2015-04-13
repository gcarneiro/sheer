CREATE TABLE sh_ml_agendamento (

	id 						CHAR(36)NOT NULL,
	idCampanha				CHAR(36)NOT NULL,
	idRemetente				CHAR(36)NOT NULL,
	assunto					VARCHAR(128) NOT NULL,
	html					TEXT NOT NULL,
	listaEmail				TEXT  NULL,
	data					DATE NOT NULL,
	hora					VARCHAR(8) NOT NULL,
	status					INTEGER NOT NULL,
	criadoPor				CHAR(36) NOT NULL,
	criadoEm				DATETIME NOT NULL,
	
	CONSTRAINT SH_ML_AGENDAMENTO PRIMARY KEY (id),
	CONSTRAINT SH_ML_AGENDAMENTO_CAMPANHA_FK FOREIGN KEY (idCampanha) REFERENCES sh_ml_campanha (id),
	CONSTRAINT SH_ML_AGENDAMENTO_CRIADORPOR_FK FOREIGN KEY (criadoPor) REFERENCES sh_user (id),
	CONSTRAINT SH_ML_AGENDAMENTO_REMETENTE_FK FOREIGN KEY (idRemetente) REFERENCES sh_ml_remetente (id)
	
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE sh_ml_agendamento CHANGE listaEmail idLista CHAR(36) NULL;
ALTER TABLE sh_ml_agendamento ADD CONSTRAINT SH_ML_AGENDAMENTO_LISTA_FK FOREIGN KEY (idLista) REFERENCES sh_ml_lista(id);