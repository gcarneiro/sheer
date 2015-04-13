CREATE TABLE bg_clienteSenha(
	id 						CHAR(36)NOT NULL,
	idCliente				CHAR(36)NOT NULL,
	login 					VARCHAR(128) NOT NULL,
	senha 					VARCHAR(128) NOT NULL,
	servico					VARCHAR(256) NULL,
	
	CONSTRAINT BG_CLIENTESENHA_PK PRIMARY KEY (id),
	CONSTRAINT BG_CLIENTESENHA_CLIENTE_FK FOREIGN KEY (idCliente) REFERENCES bg_cliente(id)
	
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE bg_clienteSenha ADD COLUMN observacao VARCHAR(256) NULL; 

-- 2014-05-15
ALTER TABLE bg_clienteSenha CHANGE servico servico TEXT NULL;
ALTER TABLE bg_clienteSenha CHANGE servico servico VARCHAR(256) NULL;
ALTER TABLE bg_clienteSenha CHANGE observacao observacao TEXT NULL;

-- 2014-06-26
ALTER TABLE bg_clienteSenha ADD COLUMN acesso VARCHAR(128) NULL AFTER idCliente;
