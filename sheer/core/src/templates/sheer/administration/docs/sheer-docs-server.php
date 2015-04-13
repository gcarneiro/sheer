<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>{{page.title}}</title>
		{{page.baseUrl}}
		
		{{template.includes/inclusaoScripts.html}}
		
		<link href="scripts/highlight/obsidian.css" type="text/css" rel="stylesheet" >
		<script src="resources/addons/sheer/sheer.docs.js"></script>
		
		{{page.scripts}}
		{{page.styles}}
		
	</head>
	
	<body>
	
		<section class="sh-box">
			<div class="sh-box-content">
				<h1>Sheer - Documentação - ServerSide</h1>
				
				<p>Algumas considerações: Quando eu utilizar ".." significa que pode ficar dentro de "sheer/core" ou "sheer/projeto". Quando eu descrever "core" ou "project" será para considerar o seu respectivo.</p>
		
				<h2>Criando um novo projeto com o Sheer</h2>
				
				<h3>Subindo o banco de dados</h3>
				<p>Você deverá criar um novo banco de dados dando o nome de sua preferência. Deve importa os bancos na seguinte ordem: sheer/localidades, sheer. Deve-se criar um novo arquivo chamado *projeto*.dbchanges.sql para mapear as alterações em banco realizadas pelo desenvolvedor.</p>
				
				<h3>Configurando o setup</h3>
				<p>Deverá acessar o path sheer/project/setup e criar um arquivo "config.json" copiando do "config_template.json". Neste arquivo você deverá configurar as informações do projeto, as conexões com banco de dados, servidor de email, facebook, gmail e instagram.</p>
				
				<h4>Editando as configurações do projeto</h4>
				<p>Precisamos configurar</p>
<div>
	<pre>
		<code class="json">
"project" : {
	"id" 			: "Sheer",
	"name" 			: "Sheer",
	"domain" 		: "ip;ip;ip",
	"domainPath"	: "",
	"description"	: "Sheer - Plataforma de desenvolvimento"
}
		</code>
	</pre>
</div>
				<ul>
					<li>id - Identificador do projeto. Utilizado para gerar tokens para as sessões dos usuários. Não pode conter espaços e nem caracteres especiais.</li>
					<li>name - Nome do projeto a ser utilizado pelo Sheer.</li>
					<li>domain -> Domínio ou ip a ser utilizado pelo projeto. Aceitamos vários domínios separados por ";"</li>
					<li>domainPath -> Path pós dominio para encontrar o documento</li>
					<li>description -> Descrição resumida do projeto</li>
					<li>serverPath -> Depreciado, não tem mais utilização</li>
				</ul>
				
				<h5>Editando as configurações com o banco de dados</h5>
				<p>Cada elemento mapeia uma conexão a ser chamada pelo id</p>
<div>
	<pre>
		<code class="json">
"mailer" : {
	"default" : {
		"host"		: "smtpDomain.com",
		"username"	: "smtpUser",
		"password"	: "smtpPassword"
	}
}
		</code>
	</pre>
</div>
				
				<h5>Editando as configurações com o servidor de email</h5>
				<p>Cada elemento mapeia uma conexão a ser chamada pelo id</p>
<div>
	<pre>
		<code class="json">
"database" : {
	"default" : {
		"driver"	: "mysql",
		"host"		: "localhost",
		"username"	: "root",
		"password"	: "",
		"database"	: "sheer_150114"
	}
}
		</code>
	</pre>
</div>
				
				<h5>Editando as configurações do facebook</h5>
				<p>Configuração com appId e secret com opção de habilitação. Só habilitar se for utilizar.</p>
<div>
	<pre>
		<code class="json">
"facebook" : {
	"enable"	: false,
	"appId"		: "737673686298127",
	"appSecret"	: "072f2b1005b958945ac9a026c12c1fc6"
}
		</code>
	</pre>
</div>
				
				<h5>Editando as configurações do gmail</h5>
				<p>Configuração com appId e secret com opção de habilitação. Só habilitar se for utilizar.</p>
				
<div>
	<pre>
		<code class="json">
"google" : {
	"enable"	: false,
	"appId"		: "{your-facebook-app-id}",
	"appSecret"	: "{your-facebook-app-id}",
	"apiKey"	: "{your-facebook-app-id}"
}
		</code>
	</pre>
</div>				
				
				<h5>Editando as configurações do instagram</h5>
				<p>Configuração com appId e secret com opção de habilitação. Só habilitar se for utilizar.</p>
<div>
	<pre>
		<code class="json">
"instagram" : {
	"enable"	: false,
	"appId"		: "{your-facebook-app-id}",
	"appSecret"	: "{your-facebook-app-id}"
}
		</code>
	</pre>
</div>				
				
				
				<h2>Entendendo a arquitetura</h2>
				
				<p>O Sheer é mapeado em algumas entidades principais: <em><a href="#modulos">Módulos</a></em>, <em><a href="">Data Sources</a></em>, <em><a href="">Data Providers</a></em>, <em><a href="">Renderables</a></em>, <em><a href="">Action Handlers</a></em> e <em><a href="">Jobs</a></em>. A seguir irei explicar como elas estão relacionadas e quais são suas funções.</p>
				
				<h3 id="modulos">Módulos</h3>
				
				<p>O Sheer é composto de módulos. Cada módulo irá indicar quais interfaces ele irá renderizar, quais tabelas em banco de dados ele vai mapear, quais métodos de recuperação de dados e funções de tratamento que possui e também quais são as ações que ele executa.</p>
				
				<p>Para o sheer conhecer um módulo precisamos registra-lo no arquivo de configuração "../setup/modules.json". Este arquivo mapeia "idModulo" : "modulePath". O "idModulo" será como Sheer irá conhecer a existencia do módulo, e o path é a pasta que possui os arquivos de configuração do módulo. Este path é relativo a "../src/modulos/".</p>
				
				<p>Todo módulo deverá ter um xml nomeado "idModulo.xml" dentro da pasta do módulo. Este xml irá descrever todo o corpo do módulo e deverá ser validado pelo schema "core/setup/schemas/module.xsd"</p>
				
				<p class="todo">Devemos retirar os schemas de dentro de setup.</p>
				
				<p class="todo">O módulo poderá possuir 3 pastas comuns: <strong>"database"</strong> a qual irá possuir os .sql necessários para o módulo executar; <strong>"styles"</strong> a qual irá possuir os arquivos de estilo que serão utilizados pelos <em>Renderables</em>; <strong>"templates"</strong> a qual irá possuir os templates a serem usados.</p>
				
				<h3 id="dataSources">Data Sources</h3>
				
				<p>---------------------------</p>
				
				<h3 id="dataProviders">Data Providers</h3>
				
				<p>---------------------------</p>
				
				<h3 id="renderables">Renderables</h3>
				
				<p>---------------------------</p>
				
				<h3 id="actionHandlers">Action Handlers</h3>
				
				<p>---------------------------</p>
				
				<h3 id="jobs">Jobs</h3>
				
				<p>---------------------------</p>
				
				
				
				
				
				<hr />
				
				
				
				
				<h2>Entendendo a estrutura de pastas</h2>
				<p>A estrutura de pastas padrão para o Sheer é a seguinte (começando por uma pasta root):<br />
		    		<strong>/sheer</strong> e <strong>/www</strong>.<br /> 
		    		<strong>/www</strong> é o path publico, onde deverão estar os arquivos a serem acessados pelo projeto, explicarei sobre ele depois.
		    	</p>
				
				<h3>Sheer</h3>
				<p>Dentro de Sheer encontramos duas pastas, "core" e "projeto". Dentro de "core" temos todos os arquivos do core do Sheer, não devemos alterar nenhum arquivo desse path pois eles são completamente substituidos quando o Sheer é atualizado. Dentro de projeto encontraremos as bibliotecas e configurações próprias do projeto, estas não são afetadas pela atualização do Sheer (exceto alguns arquivos da pasta setup).</p>
				<p>Ambas core e projeto seguem a mesma estrutura para módulos, navigation e templates que serão explicados a baixo</p>
				
				<h4>Módulos</h4>
				<p>Essa é a pasta que guarda os módulos do sistema. Esses módulos serão explicados posteriormente.</p>
				
				<h4>Navigation</h4>
				<p>Esta irá armazenar as páginas acessíveis por url. Esta seguirá a estrutura de "perfilAcesso/path". Explicação posterior.</p>
				
				<h4>Templates</h4>
				<p>Estes são os arquivos que servirão de templates a serem utilizados na renderização dos navigations. Explicação posterior.</p>
				
				
				<h3>WWW</h3>
				<p>Path publico para acesso a ferramenta. Nesta pasta encontraremos diversos arquivos que serão explicados rapidamente.</p>
				<ul>
					<li>index.php -> Arquivo para renderização de páginas por chamada http</li>
					<li>renderer.php -> Arquivo para a renderização de renderables sem dependencia de página</li>
					<li>action.php -> Arquivo para execução de um actionHandler padrão do sistema</li>
					<li>dp.php -> Arquivo para execução de um dataProvider customizado</li>
					<li>ph.php -> </li>
					<li>cron.php -> </li>
					<li>cronMl.php -> </li>
					<li>dfp.php -> </li>
					<li>dfd.php -> </li>
				</ul>
				
				<h4>data</h4>
				<p>Pasta onde serão armazenados os arquivos subidos por upload pelos usuários </p>
				
				<h4>resources</h4>
				<p>Pasta onde ficarão todos os resources que o Sheer pode vir a incluir. Dentro desta existirão as imagens utilizadas, folhas de estilos e arquivos de scripts.</p>
				
				<h4>scripts</h4>
				<p>Pasta onde o Sheer espera que sejam alocados as bibliotecas de scripts externas para uso.</p>
				
				<h2></h2>
			</div>
		</section>
		
		<script>require(['sheer/docs'], function () {});</script>
	
	</body>
	
</html>