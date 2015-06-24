CREATE TABLE sh_cieloTransacao (

	id					CHAR(36) 		NOT NULL,
	loja				VARCHAR(64)		NULL,
	ordem				VARCHAR(16) 	NOT NULL,
	localizador			VARCHAR(64) 	NOT NULL,
	tid					VARCHAR(64) 	NOT NULL,
	modalidade			INT 			NOT NULL,
	bandeira			VARCHAR(32) 	NOT NULL,
	parcelas			INT 			NOT NULL DEFAULT 1,
	valor				DECIMAL(14,2) 	NOT NULL,
	linkPagamento		VARCHAR(256) 	NOT NULL,
	linkRetorno			VARCHAR(256) 	NOT NULL,
	status				INT 			NOT NULL,
	
	descricao			VARCHAR(1024) NULL,
	faturaDescricao		VARCHAR(13) NULL,
	
	
	
	CONSTRAINT SH_CIELOTRANSACAO_PK PRIMARY KEY (id),
	CONSTRAINT SH_CIELOTRANSACAO_UNIQUE_TID UNIQUE (tid),
	CONSTRAINT SH_CIELOTRANSACAO_UNIQUE_ORDEM UNIQUE (ordem)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX SH_CIELOTRANSACAO_STATUS_INDEX ON sh_cieloTransacao(status);
CREATE INDEX SH_CIELOTRANSACAO_BANDEIRA_INDEX ON sh_cieloTransacao(bandeira);



CREATE TABLE sh_cieloTransacaoSincronizacao (

	id					CHAR(36) NOT NULL,
	ordem				VARCHAR(16) NOT NULL,
	localizador			VARCHAR(64) NOT NULL,
	tid					VARCHAR(64) NOT NULL,
	modalidade			INT NOT NULL,
	bandeira			VARCHAR(32) NOT NULL,
	parcelas			INT NOT NULL DEFAULT 1,
	valor				DECIMAL(14,2) NOT NULL,
	linkPagamento		VARCHAR(256) NOT NULL,
	linkRetorno			VARCHAR(256) NOT NULL,
	status				INT NOT NULL,
	
	CONSTRAINT SH_CIELOTRANSACAO PRIMARY KEY(id)
	

) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 14.11.07
ALTER TABLE sh_cieloTransacao ADD COLUMN criadoEm DATETIME NULL;

DROP TABLE sh_cieloTransacaoSincronizacao;

CREATE TABLE sh_cieloTransacaoSincronizacao (

	id					CHAR(36) NOT NULL,
	idTransacao			CHAR(36) NOT NULL,
	
	statusAnterior		INT NOT NULL,
	statusAtual			INT NOT NULL,
	
	sincronizadoData	DATETIME NOT NULL,
	
	CONSTRAINT SH_CIELOTRANSACAOSINCRONIZACAO_PK PRIMARY KEY(id),
	CONSTRAINT SH_CIELOTRANSACAOSINCRONIZACAO_CIELOTRANSACAO_FK FOREIGN KEY (idTransacao) REFERENCES sh_cieloTransacao (id)
	

) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 14.12.08
ALTER TABLE sh_cieloTransacao ADD COLUMN modalidadeComunicacao INT NOT NULL DEFAULT 1 AFTER faturaDescricao;
ALTER TABLE sh_cieloTransacao ADD COLUMN dadoAuxiliar TEXT NULL AFTER modalidadeComunicacao;
ALTER TABLE sh_cieloTransacao ADD COLUMN softDescriptor VARCHAR(13) NULL AFTER dadoAuxiliar;
ALTER TABLE sh_cieloTransacao ADD COLUMN statusCielo INT NULL AFTER status;
ALTER TABLE sh_cieloTransacao ADD COLUMN idCliente CHAR(36) NULL AFTER id;
ALTER TABLE sh_cieloTransacao CHANGE linkPagamento linkPagamento VARCHAR(256) NULL;
ALTER TABLE sh_cieloTransacao CHANGE linkRetorno linkRetorno VARCHAR(256) NULL;

CREATE INDEX SH_CIELOTRANSACAO_IDCLIENTE_INDEX ON sh_cieloTransacao(idCliente);

CREATE TABLE sh_cieloCartaoToken (

	token				VARCHAR(128) NOT NULL,
	idCliente			CHAR(36) NULL,
	
	nomePortador		VARCHAR(128) NULL,
	cartaoTruncado		VARCHAR(64) NOT NULL,
	bandeira			VARCHAR(64) NOT NULL,
	validade			INT NOT NULL,
	status				INT NOT NULL,
	
	CONSTRAINT SH_CIELOCARTAOTOKEN_PK PRIMARY KEY(token)

) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE sh_cieloTransacao ADD COLUMN idToken VARCHAR(128) NULL AFTER idCliente;
ALTER TABLE sh_cieloTransacao ADD CONSTRAINT SH_CIELOTRANSACAO_TOKEN_FK FOREIGN KEY (idToken) REFERENCES sh_cieloCartaoToken(token);







