CREATE TABLE sh_ml_remetente (

	id 						CHAR(36)NOT NULL,
	nomeEnvio				VARCHAR(258) NOT NULL,
	emailEnvio				VARCHAR(256) NOT NULL,
	listaEmail				VARCHAR(128) NULL,
	
	CONSTRAINT SH_ML_REMETENTE PRIMARY KEY (id)
	
) ENGINE=InnoDB DEFAULT CHARSET=utf8;