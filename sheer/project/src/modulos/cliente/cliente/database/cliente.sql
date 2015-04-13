CREATE TABLE bg_cliente (
	id 						CHAR(36)NOT NULL,
	nome					VARCHAR(128) NOT NULL,
	-- verificar razão social
	tipoPessoa				INTEGER NOT NULL,
	razaoSocial				VARCHAR(128) NULL,
	cpf						VARCHAR(32) NULL,
	cnpj					VARCHAR(32) NULL,
	inscricaoMunicipal		VARCHAR(16) NULL,
	
	email					VARCHAR(256) NULL,
	email2					VARCHAR(256) NULL,
	telefone				VARCHAR(16) NULL,
	telefone2				VARCHAR(16) NULL,
	
	cep						VARCHAR(16) NULL,
	estado					INTEGER NULL,
	cidade					INTEGER NULL,
	bairro					VARCHAR(36) NULL,
	endereco				VARCHAR(256) NULL,
	numero					VARCHAR(16) NULL,
	complemento				VARCHAR(256) NULL,
	
	CONSTRAINT BG_CLIENTE_PK PRIMARY KEY (id),
	CONSTRAINT BG_CLIENTE_ESTADO_FK FOREIGN KEY (estado) REFERENCES sh_localidadeUf (id),
	CONSTRAINT BG_CLIENTE_CIDADE_FK FOREIGN KEY (cidade) REFERENCES sh_localidadeCidade (id),
	CONSTRAINT BG_CLIENTE_BAIRRO_FK FOREIGN KEY (bairro) REFERENCES sh_localidadeBairro (id),
	CONSTRAINT BG_CLIENTE_CPF_UNIQUE UNIQUE (cpf),
	CONSTRAINT BG_CLIENTE_CNPJ_UNIQUE UNIQUE (cnpj)	
	
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE bg_cliente CHANGE estado idEstado INTEGER NULL;
ALTER TABLE bg_cliente CHANGE cidade idCidade INTEGER NULL;
ALTER TABLE bg_cliente CHANGE bairro idBairro VARCHAR(36) NULL;

-- 2014.06.18
ALTER TABLE bg_cliente ADD COLUMN codigo VARCHAR(16) NULL;

-- 2014.07.17
ALTER TABLE bg_cliente ADD COLUMN foto VARCHAR(36) NULL;
ALTER TABLE bg_cliente ADD CONSTRAINT BG_CLIENTE_FOTO_FK FOREIGN KEY (foto) REFERENCES sh_filePicture(id);
