CREATE TABLE sh_variavel (
	id				CHAR(32) NOT NULL,
	nome			VARCHAR(256) NOT NULL,
	nomeAcesso		VARCHAR(256) NOT NULL,
	valor			VARCHAR(256) NOT NULL,
	tipoVariavel	INT NULL DEFAULT 0 COMMENT '1->string, 2->integer, 3->float, 4->date, 5->time, 6->datetime',
	
	CONSTRAINT SH_VARIAVEL_PK PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO sh_variavel VALUES ('dcd8dee7fba67a0d9a1950a8f04bab0c', 'País padrão', 'paisPadrao', '76', 1);
INSERT INTO sh_variavel VALUES ('67beecce2c49db7706c61b0c0bbf2afc', 'Estado padrão', 'estadoPadrao', '19', 2);
INSERT INTO sh_variavel VALUES ('760b30d702f343395a9a6108232aeae7', 'Cidade padrão', 'cidadePadrao', '3567', 2);
INSERT INTO sh_variavel VALUES ('760b30d702f343395a9a6108232aftgj', 'CEP padrão', 'cepPadrao', '28.970-000', 2);
