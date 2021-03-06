CREATE TABLE sh_userProfile (
	
	id				VARCHAR(36) NOT NULL,
	alias			VARCHAR(128) NOT NULL,
	nome			VARCHAR(128) NOT NULL,
	descricao		VARCHAR(1024) NULL,
	renderPath		VARCHAR(128) NOT NULL,
	
	CONSTRAINT SH_USERPROFILE_PK PRIMARY KEY (id),
	CONSTRAINT SH_USERPROFILE_ALIAS UNIQUE(alias)

) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE sh_user (
	id					VARCHAR(36) NOT NULL,
	defaultProfile 		VARCHAR(36) NULL,
	nome				VARCHAR(256) NOT NULL,
	email				VARCHAR(128) NOT NULL,
	login				VARCHAR(128) NOT NULL,
	password			VARCHAR(256) NOT NULL,
	habilitado			INT NOT NULL,
	multiSecao			INT NOT NULL,
	
	CONSTRAINT SH_USER_PK PRIMARY KEY (id),
	CONSTRAINT SH_USER_EMAIL_UNIQUE UNIQUE (email),
	CONSTRAINT SH_USER_LOGIN_UNIQUE UNIQUE (login),
	CONSTRAINT SH_USER_DEFAULTPROFILE_FK FOREIGN KEY (defaultProfile) REFERENCES sh_userProfile(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE sh_userProfileUser (
	
	id				VARCHAR(36) NOT NULL,
	idUser			VARCHAR(36) NOT NULL,
	idProfile		VARCHAR(36) NOT NULL,
	
	CONSTRAINT SH_USERPROFILEUSER_PK PRIMARY KEY (id),
	CONSTRAINT SH_USERPROFILEUSER_USER_FK FOREIGN KEY (idUser) REFERENCES sh_user(id),
	CONSTRAINT SH_USERPROFILEUSER_PROFILE_FK FOREIGN KEY (idProfile) REFERENCES sh_userProfile(id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE UNIQUE INDEX SH_USERPROFILEUSER_USER_PROFILE_UNIQUE ON sh_userProfileUser(idUser, idProfile);

CREATE TABLE sh_role (

	id				VARCHAR(36) NOT NULL,
	alias			VARCHAR(128) NOT NULL,
	description		VARCHAR(1024) NULL,
	
	CONSTRAINT SH_ROLE_PK PRIMARY KEY (id),
	CONSTRAINT SH_ROLE_ALIAS_UNIQUE UNIQUE (alias)

) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE sh_contentControl (
	idContent 		VARCHAR(36) NOT NULL,
	idModule 		VARCHAR(128) NOT NULL,
	created 		DATETIME NOT NULL,
	createdBy 		VARCHAR(36) NOT NULL,
	updated 		DATETIME NOT NULL,
	updatedBy 		VARCHAR(36) NOT NULL,
	revision 		INT(11) NOT NULL,
	
	CONSTRAINT SH_CONTENTCONTROL_PK PRIMARY KEY (idContent,idModule)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE sh_contentLog (
	id 				VARCHAR(36) NOT NULL,
	idContent 		VARCHAR(36) NOT NULL,
	idModule 		VARCHAR(128) NOT NULL,
	idUser 			VARCHAR(36) NOT NULL,
	opDate 			DATETIME NOT NULL,
	serialized		TEXT NOT NULL,
  
  CONSTRAINT SH_CONTENTLOG_PK PRIMARY KEY (id)
  
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE sh_variavel (
	id				CHAR(36) NOT NULL,
	nome			VARCHAR(256) NOT NULL,
	nomeAcesso		VARCHAR(256) NOT NULL,
	valor			VARCHAR(256) NOT NULL,
	tipoVariavel	INT NULL DEFAULT 0 COMMENT '1->string, 2->integer, 3->float, 4->date, 5->time, 6->datetime',
	
	CONSTRAINT SH_VARIAVEL_PK PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- CRIANDO PERFIS/USUÃ�RIO PADRAO DE SISTEMA
INSERT INTO sh_user VALUES ('24b4287000db46b8a6f717ec1a3deb15', NULL, 'Administrador', 'admin@bgstudios.com.br', 'admin', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', '1', '1');

-- CRIANDO VARIAVEIS PADROES DO SHEER
INSERT INTO sh_variavel VALUES ('dcd8dee7fba67a0d9a1950a8f04bab0c', 'País padrão', 'paisPadrao', '76', 1);
INSERT INTO sh_variavel VALUES ('67beecce2c49db7706c61b0c0bbf2afc', 'Estado padrão', 'estadoPadrao', '33', 2);
INSERT INTO sh_variavel VALUES ('760b30d702f343395a9a6108232aeae7', 'Cidade padrão', 'cidadePadrao', '330020', 2);
INSERT INTO sh_variavel VALUES ('760b30d702f343395a9a6108232aftgj', 'CEP padrão', 'cepPadrao', '28.970-000', 2);

-- 2014.06.04
ALTER TABLE sh_variavel CHANGE id id CHAR(36) NOT NULL;

-- 2014.06.05
ALTER TABLE sh_userProfileUser ADD COLUMN dataInicio DATE NOT NULL DEFAULT '2014-01-01';
ALTER TABLE sh_userProfileUser ADD COLUMN dataFim DATE NULL;
ALTER TABLE sh_userProfileUser ADD COLUMN ativo INT NOT NULL DEFAULT 3 COMMENT '1->ATIVO, 2->ENCERRADO, 3->AGUARDANDO';

-- 2014.06.06
ALTER TABLE sh_user ADD COLUMN changePassNextLogin INT NOT NULL DEFAULT 2;
INSERT INTO sh_userProfile VALUES ('F291FE30-9171-4703-AD5F-6872BA7B65B7', 'sheer.loginChangePassword', 'Sheer - Change Password on login', NULL, 'sheer/loginChangePassword');


-- 2014.06.09
ALTER TABLE sh_user CHANGE email email VARCHAR(128) NULL;

CREATE TABLE sh_userLoggedIn (
	idUser				VARCHAR(36) NOT NULL,
	loggedAt 			DATETIME NOT NULL,
	userAgent			VARCHAR(256) NOT NULL,
	userIp				VARCHAR(64) NOT NULL,
	sessionId			VARCHAR(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 14.07.22
CREATE TABLE sh_fileDocument (

	id 					CHAR(36) NOT NULL,
	size 				INT NOT NULL COMMENT 'bytes',
	name				VARCHAR(256) NOT NULL,
	nameFull			VARCHAR(256) NOT NULL,
	nameExt				VARCHAR(256) NULL,
	path				VARCHAR(256) NOT NULL,
	adicionadoEm		DATETIME NOT NULL,
	adicionadoPor		CHAR(36) NULL,
	downloads			INT NOT NULL DEFAULT 0,
	mimeType			VARCHAR(128) NULL,
	
	CONSTRAINT SH_FILEDOCUMENT_PK PRIMARY KEY (id),
	CONSTRAINT SH_FILEDOCUMENT_FK FOREIGN KEY (adicionadoPor) REFERENCES sh_user(id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE sh_fileDocument ADD COLUMN remove INT NOT NULL DEFAULT 2 COMMENT '1->SIM, 2->NAO';

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

-- 14.08.07
INSERT INTO sh_userProfile VALUES ('0903577E-D787-43D4-B553-F2E12F5082C1', 'sheer.administration', 'Sheer - Administração', NULL, 'sheer/administration');
INSERT INTO sh_userProfileUser VALUES ('1219CB78-BA2C-49A3-B373-9C0A725E8FFA', '24b4287000db46b8a6f717ec1a3deb15', '0903577E-D787-43D4-B553-F2E12F5082C1', '2010-01-01', null, 1);
UPDATE sh_user SET defaultProfile='0903577E-D787-43D4-B553-F2E12F5082C1' WHERE id='24b4287000db46b8a6f717ec1a3deb15';

-- 14.08.15
CREATE TABLE sh_ml_remetente (

	id 						CHAR(36)NOT NULL,
	nomeEnvio				VARCHAR(258) NOT NULL,
	emailEnvio				VARCHAR(256) NOT NULL,
	listaEmail				VARCHAR(128) NULL,
	
	CONSTRAINT SH_ML_REMETENTE PRIMARY KEY (id)
	
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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


CREATE TABLE sh_ml_listaImportacao(

	id					CHAR(36) NOT NULL,
	idLista				CHAR(36) NOT NULL,
	importadoEm 		DATETIME NOT NULL,
	importadoPor 		CHAR(36) NOT NULL,
	total				INTEGER NOT NULL,
	novos				INTEGER NOT NULL,
	atualizados			INTEGER NOT NULL,
	ativar				INTEGER NOT NULL,
	arquivo				CHAR(36) NOT NULL,
	
	CONSTRAINT SH_ML_LISTAIMPORTACAO_PK PRIMARY KEY (id),
	CONSTRAINT SH_ML_LISTAEIMPORTACAO_LISTA FOREIGN KEY (idLista) REFERENCES sh_ml_lista (id)
	
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

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

CREATE TABLE sh_ml_envioTeste(

	id 						CHAR(36)NOT NULL,
	idCampanha				CHAR(36)NOT NULL,
	idRemetente				VARCHAR(128) NOT NULL,
	assunto					VARCHAR(128) NOT NULL,
	html					TEXT NOT NULL,
	destinos				TEXT NOT NULL,
	envioEm					DATETIME NOT NULL,
	envioPor				CHAR(36) NOT NULL,
	
	CONSTRAINT SH_ML_ENVIOTESTE PRIMARY KEY (id),
	CONSTRAINT SH_ML_ENVIOTESTE_CAMPANHA_FK FOREIGN KEY (idCampanha) REFERENCES sh_ml_campanha (id),
	CONSTRAINT SH_ML_ENVIOTESTE_REMETENTE_FK FOREIGN KEY (idRemetente) REFERENCES sh_ml_remetente (id)
	
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE sh_ml_disparo (

	id 						CHAR(36) NOT NULL,
	idCampanha				CHAR(36) NOT NULL,
	idAgendamento			CHAR(36) NULL,
	idRemetente				CHAR(36) NOT NULL,
	idLista					CHAR(36) NOT NULL,
	assunto					VARCHAR(128) NOT NULL,
	html					TEXT NOT NULL,
	disparadoEm				DATETIME NOT NULL,
	total					INTEGER NOT NULL,
	enviados				INTEGER NOT NULL,
	
	CONSTRAINT SH_ML_DISPARO PRIMARY KEY (id),
	CONSTRAINT SH_ML_DISPARO_CAMPANHA_FK FOREIGN KEY (idCampanha) REFERENCES sh_ml_campanha (id),
	CONSTRAINT SH_ML_DISPARO_REMETENTE_FK FOREIGN KEY (idRemetente) REFERENCES sh_ml_remetente (id),
	CONSTRAINT SH_ML_DISPARO_LISTAEMAIL_FK FOREIGN KEY (idLista) REFERENCES sh_ml_lista (id),
	CONSTRAINT SH_ML_DISPARO_AGENDAMENTO_FK FOREIGN KEY (idAgendamento) REFERENCES sh_ml_agendamento (id)
	
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE sh_ml_disparoEmail (

	id						CHAR(36) NOT NULL,
	idContato				CHAR(36) NOT NULL,
	idDisparo				CHAR(36) NOT NULL,
	dataDisparo 			DATETIME NOT NULL,
	contatoInfo				TEXT NULL,
	
	CONSTRAINT SH_ML_DISPAROEMAIL_PK PRIMARY KEY (id),
	CONSTRAINT SH_ML_DISPAROEMAIL_DISPARO_FK FOREIGN KEY (idDisparo) REFERENCES sh_ml_disparo(id)
	
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 14.08.18 AlteraÃ§Ãµes referentes aos addons
DROP TABLE IF EXISTS sh_contentControl;
DROP TABLE IF EXISTS sh_contentLog;

CREATE TABLE sh_publicationHistory (
	contentId			CHAR(36) NOT NULL,
	datasourceId		VARCHAR(128) NOT NULL,
	userId				CHAR(36) NOT NULL,
	operationDate		DATETIME NOT NULL,
	serialized			TEXT NOT NULL,
	
	CONSTRAINT SH_PUBLICATIONHISTORY_USERID FOREIGN KEY (userId) REFERENCES sh_user(id)
	
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE INDEX SH_PUBLICATIONHISTORY_CONTENTID ON sh_publicationHistory (contentId);

CREATE TABLE sh_publicationMetadata (

	contentId			CHAR(36) NOT NULL,
	datasourceId		VARCHAR(128) NOT NULL,
	created				DATETIME NOT NULL,
	createdBy			CHAR(36) NOT NULL,
	updated				DATETIME NOT NULL,
	updatedBy			CHAR(36) NOT NULL,
	removed				DATETIME NULL,
	removedBy			CHAR(36) NULL,
	revision			INT NOT NULL DEFAULT 1,
	
	CONSTRAINT SH_PUBLICATIONMETADATA_PK PRIMARY KEY (contentId, datasourceId),
	CONSTRAINT SH_PUBLICATIONMETADATA_CREATEDBY FOREIGN KEY (createdBy) REFERENCES sh_user(id),
	CONSTRAINT SH_PUBLICATIONMETADATA_UPDATEBY FOREIGN KEY (updatedBy) REFERENCES sh_user(id),
	CONSTRAINT SH_PUBLICATIONMETADATA_REMOVEDBY FOREIGN KEY (removedBy) REFERENCES sh_user(id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 2014.08.29
ALTER TABLE sh_publicationHistory CHANGE userId userId CHAR(36) NULL;
ALTER TABLE sh_publicationMetadata CHANGE createdBy createdBy CHAR(36) NULL;
ALTER TABLE sh_publicationMetadata CHANGE updatedBy updatedBy CHAR(36) NULL;
ALTER TABLE sh_publicationMetadata CHANGE removedBy removedBy CHAR(36) NULL;

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

-- Complementado o malaDireta
CREATE TABLE sh_ml_disparoVisualizacao (

	id						CHAR(36) NOT NULL,
	idDisparo				CHAR(36) NOT NULL,
	idUsuario				CHAR(36) NOT NULL,
	adicionadoEm			DATETIME NOT NULL,
	tipoVisualizacao		INTEGER NOT NULL,
	
	CONSTRAINT SH_ML_DISPAROVISUALIZACAO_PK PRIMARY KEY (id),
	CONSTRAINT SH_ML_DISPAROVISUALIZACAO_DISPARO_FK FOREIGN KEY (idDisparo) REFERENCES sh_ml_disparo(id)
	
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE sh_ml_disparoRemocao (

	id						CHAR(36) NOT NULL,
	idDisparo				CHAR(36) NOT NULL,
	idUsuario				CHAR(36) NOT NULL,
	adicionadoEm			DATETIME NOT NULL,	
	
	CONSTRAINT SH_ML_DISPAROREMOCAO_PK PRIMARY KEY (id),
	CONSTRAINT SH_ML_DISPAROREMOCAO_DISPARO_FK FOREIGN KEY (idDisparo) REFERENCES sh_ml_disparo(id)
	
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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

ALTER TABLE sh_ml_disparo ADD COLUMN remocoes INTEGER NOT NULL;
ALTER TABLE sh_ml_disparo ADD COLUMN totalVisualizacoes INTEGER NOT NULL;
ALTER TABLE sh_ml_disparo ADD COLUMN visualizacoesUnicas INTEGER NOT NULL;

ALTER TABLE sh_ml_agendamento CHANGE listaEmail idLista CHAR(36) NOT NULL;

-- 2014.09.02
CREATE TABLE sh_userFacebook (
	id				CHAR(36) 		NOT NULL,
	fbId			VARCHAR(36) 	NOT NULL,
	sheerId			CHAR(36) 		NULL,
	nome			VARCHAR(128) 	NOT NULL,
	email			VARCHAR(128)	NOT NULL,
	username		VARCHAR(128) 	NOT NULL,
	nascimento		DATE 			NULL,
	sexo			CHAR(1) 		NULL,
	foto			CHAR(36) 		NULL,
	link			VARCHAR(256) 	NULL,
	fbUpdated		DATETIME		NULL,
	
	CONSTRAINT SH_USERFACEBOOK_PK PRIMARY KEY (id),
	CONSTRAINT SH_USERFACEBOOK_SHEERID_FK FOREIGN KEY (sheerId) REFERENCES sh_user(id),
	CONSTRAINT SH_USERFACEBOOK_FBID_UNIQUE UNIQUE (fbId),
	CONSTRAINT SH_USERFACEBOOK_SHEERID_UNIQUE UNIQUE (sheerId)
	
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE sh_userFacebookAccessToken (
	id				CHAR(36) 		NOT NULL,
	fbId			VARCHAR(36) 	NOT NULL,
	accessToken		VARCHAR(256) 	NOT	NULL,
	machineId		VARCHAR(128) 	NULL,
	expiresAt		DATETIME 		NOT NULL,
	createdAt		DATETIME 		NOT NULL,
	
	CONSTRAINT SH_USERFACEBOOKACCESSTOKEN_PK PRIMARY KEY (id),
	CONSTRAINT SH_USERFACEBOOKACCESSTOKEN_FBUSER_FK FOREIGN KEY (fbId) REFERENCES sh_userFacebook(fbId)
	
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 14.09.04
ALTER TABLE sh_ml_agendamento ADD CONSTRAINT SH_ML_AGENDAMENTO_LISTA_FK FOREIGN KEY (idLista) REFERENCES sh_ml_lista(id);

-- 14.09.08
CREATE TABLE sh_blogCategoria (
	id				CHAR(36) NOT NULL,
	titulo			VARCHAR(128) NOT NULL,
	descricao		VARCHAR(1024) NULL,
	posicao		INT NOT NULL DEFAULT 0,
	
	CONSTRAINT SH_BLOGCATEGORIA_PK PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE sh_blog (

	id				CHAR(36) NOT NULL,
	idCategoria		CHAR(36) NULL,
	titulo			VARCHAR(128) NOT NULL,
	chamada			VARCHAR(1024) NULL,
	conteudo		TEXT NOT NULL,
	data			DATE NOT NULL,
	keywords		VARCHAR(256) NULL,
	autor			VARCHAR(128) NULL,
	
	CONSTRAINT SH_BLOG_PK PRIMARY KEY (id),
	CONSTRAINT SH_BLOG_CATEGORIA_FK FOREIGN KEY (idCategoria) REFERENCES sh_blogCategoria(id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 14.09.10
ALTER TABLE sh_userProfile ADD COLUMN guestProfile INT NOT NULL DEFAULT 2 AFTER descricao;

-- 14.09.12
RENAME TABLE sh_userFacebook TO sh_social_facebook;
RENAME TABLE sh_userFacebookAccessToken TO sh_social_facebookAccessToken;

-- 14.10.10
CREATE TABLE sh_token (
	id				CHAR(36)		NOT NULL,
	status			INT 			NOT NULL,
	geradoEm		DATETIME 		NOT NULL,
	finalizadoEm	DATETIME 		NULL,
	security		VARCHAR(128)	NULL,
	
	CONSTRAINT SH_TOKEN_PK PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE INDEX SH_TOKEN_STATUS ON sh_token(status);

-- 14.10.17
INSERT INTO sh_variavel VALUES ('AAD51EC7-819C-4A5D-B531-2F29D9D2FAF0', 'ActionHandlers - Permissao Padrão', 'sheer.ah.permissaoPadrao', 'denyGuest', 1);
INSERT INTO sh_variavel VALUES ('5D0F0250-BCB1-4130-980C-2A6E1370106D', 'Sessão - Tempo de duração de Sessão padrão', 'sheer.session.maxLifeTime', 180, 2);

-- 14.10.22
CREATE TABLE sh_contatoDestino (

	id				CHAR(36) 		NOT NULL,
	nome			VARCHAR(128) 	NOT NULL,
	email			VARCHAR(128)	NOT NULL,
	alias			VARCHAR(128)	NOT NULL,
	
	CONSTRAINT SH_CONTATODESTINO_PK PRIMARY KEY (id),
	CONSTRAINT SH_CONTATODESTINO_EMAIL_UNIQUE UNIQUE (email),
	CONSTRAINT SH_CONTATODESTINO_ALIAS_UNIQUE UNIQUE (alias)
	
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE INDEX SH_CONTATODESTINO_ALIAS ON sh_contatoDestino(alias);

CREATE TABLE sh_contato (

	id				CHAR(36) 		NOT NULL,
	idDestino		CHAR(36) 		NOT NULL,
	localizador		VARCHAR(32) 	NOT NULL,
	nome			VARCHAR(128) 	NOT NULL,
	telefone		VARCHAR(24)		NULL,
	email			VARCHAR(256)	NOT NULL,
	idCidade		CHAR(36)		NULL,
	idEstado		CHAR(36) 		NULL,
	assunto			VARCHAR(128)	NULL,
	mensagem		TEXT			NOT NULL,
	enviadoEm		DATETIME		NOT NULL,
	ip				VARCHAR(32)		NOT NULL,
	arquivado		INTEGER			NOT NULL DEFAULT 2,
	
	CONSTRAINT SH_CONTATO_PK PRIMARY KEY (id),
	CONSTRAINT SH_CONTATO_DESTINO_FK FOREIGN KEY (idDestino) REFERENCES sh_contatoDestino(id),
	CONSTRAINT SH_CONTATO_CIDADE_FK FOREIGN KEY (idCidade) REFERENCES ibge_localidadeCidade(id),
	CONSTRAINT SH_CONTATO_ESTADO_FK FOREIGN KEY (idEstado) REFERENCES ibge_localidadeEstado(id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE sh_contatoDestino ADD COLUMN removivel INTEGER NOT NULL DEFAULT 1;
INSERT INTO sh_contatoDestino VALUES ('849580E4-BED4-4002-BDD8-91F82DEB291E', 'Padrão', 'contato@bgstudios.com.br', 'default', 2);

ALTER TABLE sh_contato ADD COLUMN enviado INTEGER NOT NULL DEFAULT 2;

-- 14.10.28
CREATE TABLE sh_imageRepository (

	id 					CHAR(36) 		NOT NULL,
	idContent			CHAR(36) 		NOT NULL,
	idDataSource		VARCHAR(128) 	NOT NULL,
	idCapa				CHAR(36) 		NULL,
	legenda				VARCHAR(1024) 	NULL,
	quantidade			INTEGER 		NOT NULL DEFAULT 0,

	CONSTRAINT SH_IMAGEREPOSITORY_PK PRIMARY KEY (id),
	CONSTRAINT SH_IMAGEREPOSITORY_UNIQUE UNIQUE (idContent, idDataSource)

) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE sh_imageRepositoryPicture (

	id 					CHAR(36) 		NOT NULL,
	idRepository		CHAR(36) 		NOT NULL,
	idPicture			CHAR(36)		NOT NULL,
	idProximo 			CHAR(36) 		NULL,
	legenda				VARCHAR(1024)	NULL,
	primeiro			INT				NOT NULL DEFAULT 2,
	
	CONSTRAINT SH_IMAGEREPOSITORYPICTURE_PK 				PRIMARY KEY (id),
	CONSTRAINT SH_IMAGEREPOSITORYPICTURE_REPOSITORY_FK 		FOREIGN KEY (idRepository) 	REFERENCES sh_imageRepository(id),
	CONSTRAINT SH_IMAGEREPOSITORYPICTURE_PICTURE_FK 		FOREIGN KEY (idPicture) 	REFERENCES sh_filePicture(id),
	CONSTRAINT SH_IMAGEREPOSITORYPICTURE_PROXIMO_FK 		FOREIGN KEY (idProximo) 	REFERENCES sh_imageRepositoryPicture(id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE sh_imageRepository ADD CONSTRAINT SH_IMAGEREPOSITORY_CAPA_FK FOREIGN KEY (idCapa) REFERENCES sh_imageRepositoryPicture(id);

CREATE TABLE sh_imageRepositoryViewed (

	id				CHAR(36) NOT NULL,
	idRepository	CHAR(36) NOT NULL,
	idProfile		CHAR(36) NOT NULL,
	times			INT NOT NULL DEFAULT 0,
	
	CONSTRAINT SH_IMAGEREPOSITORYVIEWED_PK PRIMARY KEY (id),
	CONSTRAINT SH_IMAGEREPOSITORYVIEWED_REPOSITORY_FK FOREIGN KEY (idRepository) REFERENCES sh_imageRepository(id),
	CONSTRAINT SH_IMAGEREPOSITORYVIEWED_PROFILE_FK FOREIGN KEY (idProfile) REFERENCES sh_userProfile (id),
	CONSTRAINT SH_IMAGEREPOSITORYVIEWED_UNIQUE_REP_PROFILE UNIQUE (idRepository, idProfile)

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

-- 14.11.24
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

-- 14.12.29
ALTER TABLE sh_social_facebookAccessToken ADD CONSTRAINT SH_SOCIAL_FACEBOOKACCESSTOKEN_FBID_FK FOREIGN KEY (fbId) REFERENCES sh_social_facebook (fbId);
ALTER TABLE sh_userLoggedIn ADD CONSTRAINT SH_USERLOGGEDIN_USER_FK FOREIGN KEY (idUser) REFERENCES sh_user(id);

-- 15.03.19
INSERT INTO sh_variavel VALUES ('CF27ED83-990F-4EFD-83BD-59E9984D1F62', 'Email padrão', 'emailDefault', 'contato@bgstudios.com.br', 2);

-- 15.04.15
ALTER TABLE sh_contatoDestino DROP INDEX SH_CONTATODESTINO_EMAIL_UNIQUE;

-- 15.05.25
CREATE TABLE sh_ml_contato (

	id 					CHAR(36) NOT NULL,
	nome				VARCHAR(128) NULL,
	email				VARCHAR(128) NOT NULL,
	
	CONSTRAINT SH_ML_CONTATO_PK PRIMARY KEY (id),
	CONSTRAINT SH_ML_CONTATO_UNIQUE UNIQUE (email)
	
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 2015.05.25 - NESTE PASSO O SISTEMA PASSA A QUEBRAR E SE FAZ NECESSÁRIO A IMPORTAÇÃO
ALTER TABLE sh_ml_listaEmail DROP COLUMN nome;
ALTER TABLE sh_ml_listaEmail DROP COLUMN email;
ALTER TABLE sh_ml_listaEmail ADD COLUMN idContato CHAR(36) NULL AFTER idLista;
ALTER TABLE sh_ml_listaEmail ADD CONSTRAINT SH_ML_LISTAEMAIL_CONTATO FOREIGN KEY (idContato) REFERENCES sh_ml_contato(id);
ALTER TABLE sh_ml_listaEmail ADD CONSTRAINT SH_ML_LISTAEMAIL_UNIQUE_LISTACONTATO UNIQUE(idLista, idContato);


CREATE TABLE sh_ml_disparoLista (

	id					CHAR(36) NOT NULL,
	idLista				CHAR(36) NOT NULL,
	idAgendamento		CHAR(36) NULL,
	idDisparo			CHAR(36) NULL,
	
	CONSTRAINT SH_ML_DISPARO_LISTA PRIMARY KEY (id),
	CONSTRAINT SH_ML_LISTA_FK FOREIGN KEY (idLista) REFERENCES sh_ml_lista (id),
	CONSTRAINT SH_ML_AGENDAMENTO_FK FOREIGN KEY (idAgendamento) REFERENCES sh_ml_agendamento (id),
	CONSTRAINT SH_ML_DISPARO_FK FOREIGN KEY (idDisparo) REFERENCES sh_ml_disparo (id)
	
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE sh_ml_agendamento DROP FOREIGN KEY SH_ML_AGENDAMENTO_LISTA_FK;
ALTER TABLE sh_ml_agendamento DROP COLUMN idLista;

CREATE TABLE sh_ml_disparoLink (

	id				CHAR(36) NOT NULL,
	
	link			VARCHAR(256) NOT NULL,
	total			INTEGER NOT NULL,
	criadoEm		DATETIME NOT NULL,
	
	CONSTRAINT	 	SIM_ATENDIMENTO_PK PRIMARY KEY (id)
	
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 2015-05-26
ALTER TABLE sh_ml_disparoLink DROP COLUMN criadoEm;
ALTER TABLE sh_ml_disparo DROP FOREIGN KEY SH_ML_DISPARO_LISTAEMAIL_FK;
ALTER TABLE sh_ml_disparo DROP COLUMN idLista;

