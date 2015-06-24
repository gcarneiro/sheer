<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>{{page.title}}</title>
		{{page.baseUrl}}
		
		<!-- 
			Inicializando o requirejs
			Dentro do script setup do sheer iremos inicializar todas as dependecias do Sheer.
		
		 -->
		<script src="./resources/addons/sheer/sheer.adapters.js"></script>
		<script src="./resources/addons/sheer/sheer.js"></script>
		<script src="./resources/addons/sheer/sheer.adm.js"></script>
		
		<!-- 
			Estilo
				ADDONS
		 -->
		<link rel="stylesheet" type="text/css" href="./resources/addons/jquery.colorbox/colorbox.css" />
		<link rel="stylesheet" type="text/css" href="./resources/addons/jquery.select2/select2.min.css" />
		<link rel="stylesheet" type="text/css" href="./resources/addons/bootstrap/less/bootstrap.css" />
		<link rel="stylesheet" type="text/css" href="./resources/addons/datepicker/datepicker.css" />
		<link rel="stylesheet" type="text/css" href="./resources/addons/qtip/jquery.qtip.min.css" />
		
		<!-- 
			Sheer
		 -->
		 <link rel="stylesheet" type="text/css" href="./resources/css/sheer/sheer.base.css" />
		 <link rel="stylesheet" type="text/css" href="./resources/css/sheer/sheer.interface.css" />
			
	
		<link href="scripts/highlight/obsidian.css" type="text/css" rel="stylesheet">
		<link href="resources/css/sheer/docs.css" type="text/css" rel="stylesheet">
		<script src="resources/addons/sheer/sheer.docs.js"></script>
			
		{{page.scripts}}
		{{page.styles}}
		
	</head>
	
<body>

	<div class="container ">
	
		<div class="row">
		
			<div id="shRouteHolder" class="col-xs-12 col-sm-10 col-md-9">
			
				<section class="sh-box">
					<div class="sh-box-content">
						
						<h1>sheer.js</h1>
			
						<h3 id="fieldValidation">Validação de campos</h3>
			
						<p>O Sheer é composto de módulos. Cada módulo irá indicar quais
							interfaces ele irá renderizar, quais tabelas em banco de dados ele
							vai mapear, quais métodos de recuperação de dados e funções de
							tratamento que possui e também quais são as ações que ele executa.</p>
			
						<p>Para o sheer conhecer um módulo precisamos registra-lo no arquivo
							de configuração <span class="code">setup/modules.json</span>. Este arquivo mapeia
							<span class="code">"idModulo" : "modulePath"</span>. O "idModulo" será como Sheer irá conhecer
							a existencia do módulo, e o path é a pasta que possui os arquivos de
							configuração do módulo. Este path é relativo a <span class="code">src/modulos/</span>.</p>
			
						<p>Todo módulo deverá ter um xml nomeado <span class="code">idModulo.xml</span> dentro da
							pasta do módulo. Este xml irá descrever todo o corpo do módulo e
							deverá ser validado pelo schema <span class="code">core/setup/schemas/module.xsd</span></p>
			
						<p class="todo">Devemos retirar os schemas de dentro de setup.</p>
						
						<p>O módulo poderá possuir 3 pastas comuns:</p>
						<ul>
							<li><span class="code">"database"</span> irá possuir os .sql necessários para o módulo executar</li>
							<li><span class="code">"styles"</span> irá possuir os arquivos de estilo que serão utilizados pelos <span class="code">renderables</span></li>
							<li><span class="code">"templates"</span> irá possuir os templates a serem usados</li>
						</ul>
						
						<p>Poderá ser criado o arquivo <span class="code">idModulo.php</span> e ele será automaticamente incluído pelo Sheer. 
							Nele você poderá declarar toda classe, função ou variavel desejada.<br />
							Este deverá ser descrito sob o namespace <span class="code">\Sh\Modules\idModulo</span></p>
			
						<h3 id="dataSources">Data Sources</h3>
			
						<p>Estruturas que mapeiam um banco de dados. Este irá declarar <span class="code">fields</span>, que seguem a class<span class="code">\Sh\DataSourceField</span></p>
						
						<p>Cada <span class="code">field</span> será diferenciado pelo seu <span class="code">dataType</span>. Cada <span class="code">dataType</span> é representado por alguma classe que herde de <span class="code">\Sh\DataSourceField</span>.</p>
				
						<!-- Falando sobre os fields -->
						<p><strong>Um pouco mais sobre os fields</strong></p>
						
						<p>
							Cada field descrito dentro do dataSource deve mapear uma coluna no banco de dados. Este field deve seguir um padrão de dado chamado de <span class="code">dataType</span>.<br />
							Além do <span class="code">dataType</span> temos outros atributos importantes como segue a relação abaixo							
						</p>
						
						<p><small>Alguns dados foram removidos do sheer como <span class="code">setNullIfBlank</span> e <span class="code">mask</span></small></p>
						
						<table class="table">
							<thead>
								<tr>
									<th>atributo</th>
									<th>uso</th>
									<th>tipo</th>
									<th>Info</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>id</td>
									<td>Obrigatório</td>
									<td>string</td>
									<td>Identificador da coluna para o banco de dados</td>
								</tr>
								<tr>
									<td>name</td>
									<td>Obrigatório</td>
									<td>string</td>
									<td>Nome a ser utilizado pelo Sheer para exibir o dado </td>
								</tr>
								<tr>
									<td>dataType</td>
									<td>Obrigatório</td>
									<td><span class="code">dataTypes</span></td>
									<td>Determina o tipo do campo a ser interpretado pelo Sheer.</td>
								</tr>
								<tr>
									<td>required</td>
									<td>Opcional</td>
									<td>boolean</td>
									<td>Default: <span class="code">false</span> Determina se o campo é obrigatório para o registro. Quando este for considerado como obrigatório o defaultValue é desconsiderado.</td>
								</tr>
								<tr>
									<td>primaryKey</td>
									<td>Opcional</td>
									<td>boolean</td>
									<td>Default: <span class="code">false</span> Determina se o campo será a chave primaria do DataSource. Apesar deste campo ser opcional, ao menos um field deve ser primaryKey para ser validado.</td>
								</tr>
								<tr>
									<td>primaryName</td>
									<td>Opcional</td>
									<td>boolean</td>
									<td>Default: <span class="code">false</span> Determina se o campo irá conter o valor principal para visualização/ordenação do Field. Apesar deste campo ser opcional, ao menos um field deve ser primaryName para ser validado.</td>
								</tr>
								<tr>
									<td>defaultValue</td>
									<td>Opcional</td>
									<td>boolean</td>
									<td>Este campo irá determinar o valor default do campo. Este valor pode assumir um valor literal ou algum especial que será traduzido pelo Sheer. Para a utilização de <span class="code"><a href="#aliasValue">aliasValues</a></span> verificar em <span class="code">\Sh\RuntimeVariables</span> como utilizar.</td>
								</tr>
								<tr>
									<td>lookup</td>
									<td>Opcional</td>
									<td>boolean</td>
									<td>Default: <span class="code">false</span>
										Indica se o Sheer deve tentar buscar uma tradução para este campo na sua configuração de options.
										Se o campo tiver options configurados sobre uma variavel estatica ele irá funcionar corretamente. Em qualquer outro caso não.
										Ele irá inserir mais um <span class="code">field</span> no seu dataSource sendo <span class="code">[id="field_lookup"]</span>
										Para trazer este lookup de um <span class="code">dataProvider</span> deverá ser utilizados os <span class="code">dataProvider/relation</span>.
									</td>
								</tr>
								<tr>
									<td>uppercase</td>
									<td>Opcional</td>
									<td>boolean</td>
									<td>Default: <span class="code">false</span>Determina se o campo deverá utilizar os valores todos formatados em upperCase</td>
								</tr>
								<tr>
									<td>lowercase</td>
									<td>Opcional</td>
									<td>boolean</td>
									<td>Default: <span class="code">false</span>Determina se o campo deverá utilizar os valores todos formatados em lowerCase</td>
								</tr>
							</tbody>
						</table>
						
						<!-- Falando um pouco mais sobre os dataTypes -->
						<p><strong>Conheça os dataTypes padrões</strong></p>
						<p>Para uma utilização mais rápida dos tipos de dados o sheer já oferece alguns dataTypes prontos para utilização. Segue informações em tabela abaixo</p>
						
						<table class="table">
							<thead>
								<tr>
									<th>dataType</th>
									<th>descrição</th>
									<th>Formatos</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>Date</td>
									<td>Datas</td>
									<td>
										<strong>Input, Sheer</strong><br />
										<span>d/m/Y</span><br />
										<strong>Primitive</strong><br />
										<span>Y-m-d</span>
									</td>
								</tr>
								<tr>
									<td>DateTime</td>
									<td>Datas com horário. Estes são aceitos contendo só H:i, sem a necessidade dos segundos.</td>
									<td>
										<strong>Input, Sheer</strong><br />
										<span>d/m/Y H:i:s</span><br />
										<strong>Primitive</strong><br />
										<span>Y-m-d  H:i:s</span>
									</td>
								</tr>
								<tr>
									<td>Decimal</td>
									<td>Números decimais. Similar à <span class="code">FieldFloat</span></td>
									<td></td>
								</tr>
								<tr>
									<td>Dinheiro</td>
									<td>Mapeia o formato de dinheiro em real. Este irá aceitar um dado contábil em portugues e consegue realizar contas.</td>
									<td>
										<strong>Input, Sheer</strong><br />
										<span>99.999.999,00</span><br />
										<strong>Primitive</strong><br />
										<span>99999999.00</span>
									</td>
								</tr>
								<tr>
									<td>Email</td>
									<td>String representativa de um email</td>
									<td>
										<strong>Input, Sheer, Input</strong><br />
										<span>meunome@meudominio.com.br</span>
									</td>
								</tr>
								<tr>
									<td>File</td>
									<td>
										Arquivos gerais a serem anexados. Estes podem ser quaisquer arquivos por enquanto. Não temos como ainda efetuar travas.
										As travas gerais são o tamanho máximo de arquivo de upload.
									</td>
									<td>
										<strong>Input</strong><br />
										<span>array $_FILE do php.</span><br />
										<strong>Sheer</strong><br />
										<span>object do módulo <span class="code">fileDocument</span>.</span><br />
										<strong>Primitive</strong><br />
										<span>string de 36 caracteres que representa o primaryKey do <span class="code">fileDocument</span>.</span>
									</td>
								</tr>
								<tr>
									<td>Float</td>
									<td>Número decimal de alta precisão.</td>
									<td>
										<strong>Input, Sheer, Primitive</strong><br />
										<span>999999.99999999</span>
									</td>
								</tr>
								<tr>
									<td>Html</td>
									<td>Campo para armazenar Html. Ele vai ser igual ao <span class="code">text</span> mas irá renderizar como richtext.</td>
									<td>
										<strong>Input, Primitive, Sheer</strong><br />
										<span>string comum aceitando tags html</span>
									</td>
								</tr>
								<tr>
									<td>Image</td>
									<td>Arquivos de imagens para serem registrados pelo Sheer. Podemos declarar os maps <span class="code"><a href="#pictureMap">pictureMap</a></span> como filha da tag</td>
									<td>
										<strong>Input</strong><br />
										<span>array $_FILE do php.</span><br />
										<strong>Sheer</strong><br />
										<span>object do módulo <span class="code">filePicture</span>.</span><br />
										<strong>Primitive</strong><br />
										<span>string de 36 caracteres que representa o primaryKey do <span class="code">filePicture</span>.</span>
									</td>
								</tr>
								<tr>
									<td>Integer</td>
									<td>Número inteiro comum</td>
									<td>
										<strong>Input, Primitive, Sheer</strong><br />
										<span>999.999.999</span>
									</td>
								</tr>
								<tr>
									<td>String</td>
									<td>string comum</td>
									<td>
										<strong>Input, Primitive, Sheer</strong><br />
										<span>string comum</span>
									</td>
								</tr>
								<tr>
									<td>Text</td>
									<td>String de maior tamanho a ser renderizado por um textarea</td>
									<td>
										<strong>Input, Primitive, Sheer</strong><br />
										<span>String de maior tamanho</span>
									</td>
								</tr>
							</tbody>
						</table>
										
						<!-- Falando sobre os addons -->
						<p><strong>Configurações adicionais para o dataSource</strong></p>
				
						<p>Além dos fields podemos determinar algumas configurações de controle avançadas por conteúdo através da propriedade <span class="code">addons</span></p>
						<p>Nesta propriedades poderão ser descritos as seguintes configurações></p>
						<ul>
							<li>
								<span class="code">(boolean) imageRepository</span> - Determina se serão permitidos repositório de imagens por conteúdo.
								Para o <span class="code"><a href="#pictureMap">imageRepository</a></span> é possível configurar os mapas de imagens <span class="code"><a href="#pictureMap">pictureMap</a></span> para utilização.
							</li>
							<li><span class="code">(boolean) publicationHistory</span> - Este era um log do Sheer para qualquer operação que era realizada de ação. Mas acredito que foi desabilitado, iria ser transformado para arquivo pois estava pesando mutio. Ao desabilitar essa configuração esses dados deixam de ser registrados.</li>
							<li><span class="code">(boolean) publicationMetadata</span> - Log que mapeia os dados de alteração por conteúdo. Mapeia quem e quando se cria, edita ou remove um primaryKey. Ao desabilitar essa configuração esses dados deixam de ser registrados.</li>
						</ul>
			
			
						<h3 id="dataProviders">Data Providers</h3>
			
						<p>---------------------------</p>
			
						<h3 id="renderables">Renderables</h3>
			
						<p>---------------------------</p>
			
						<h3 id="actionHandlers">Action Handlers</h3>
			
						<p>---------------------------</p>
			
						<h3 id="jobs">Jobs</h3>
			
						<p>---------------------------</p>
			
						<h2>Entendendo a estrutura de pastas</h2>
						<div>
							<p>
								A estrutura de pastas padrão para o Sheer é a seguinte (começando
								por uma pasta root):<br /> <strong>/sheer</strong> e <strong>/www</strong>.<br />
								<strong>/www</strong> é o path publico, onde deverão estar os
								arquivos a serem acessados pelo projeto, explicarei sobre ele
								depois.
							</p>
				
							<h3>Sheer</h3>
							<p>Dentro de Sheer encontramos duas pastas, "core" e "projeto".
								Dentro de "core" temos todos os arquivos do core do Sheer, não
								devemos alterar nenhum arquivo desse path pois eles são
								completamente substituidos quando o Sheer é atualizado. Dentro de
								projeto encontraremos as bibliotecas e configurações próprias do
								projeto, estas não são afetadas pela atualização do Sheer (exceto
								alguns arquivos da pasta setup).</p>
							<p>Ambas core e projeto seguem a mesma estrutura para módulos,
								navigation e templates que serão explicados a baixo</p>
								
								
								
						</div>
						
			
						<h4>Módulos</h4>
						<p>Essa é a pasta que guarda os módulos do sistema. Esses módulos
							serão explicados posteriormente.</p>
			
						<h4>Navigation</h4>
						<p>Esta irá armazenar as páginas acessíveis por url. Esta seguirá a
							estrutura de "perfilAcesso/path". Explicação posterior.</p>
			
						<h4>Templates</h4>
						<p>Estes são os arquivos que servirão de templates a serem utilizados
							na renderização dos navigations. Explicação posterior.</p>
			
			
						<h3>WWW</h3>
						<p>Path publico para acesso a ferramenta. Nesta pasta encontraremos
							diversos arquivos que serão explicados rapidamente.</p>
						<ul>
							<li>index.php -> Arquivo para renderização de páginas por chamada
								http</li>
							<li>renderer.php -> Arquivo para a renderização de renderables sem
								dependencia de página</li>
							<li>action.php -> Arquivo para execução de um actionHandler padrão
								do sistema</li>
							<li>dp.php -> Arquivo para execução de um dataProvider customizado</li>
							<li>ph.php -></li>
							<li>cron.php -></li>
							<li>cronMl.php -></li>
							<li>dfp.php -></li>
							<li>dfd.php -></li>
						</ul>
			
						<h4>data</h4>
						<p>Pasta onde serão armazenados os arquivos subidos por upload pelos
							usuários</p>
			
						<h4>resources</h4>
						<p>Pasta onde ficarão todos os resources que o Sheer pode vir a
							incluir. Dentro desta existirão as imagens utilizadas, folhas de
							estilos e arquivos de scripts.</p>
			
						<h4>scripts</h4>
						<p>Pasta onde o Sheer espera que sejam alocados as bibliotecas de
							scripts externas para uso.</p>
			
						<hr />
						
						
						
						
						
						
						<!-- 
						
						Como instanciar o Sheer
						
						 -->
						<h2>Como instanciar o Sheer</h2>
						<div>
							<p>O primeiro passo precisamos criar um banco de dados mysql para utilização do Sheer. Poderá utilizar o nome de sua preferência, mas deverá seguir a seguinte ordem de importação:</p>
							
							<ul>
								<li><span class="code">sheer.localidades.dbchanges.sql</span> - Este deve ser o primeiro a ser importado, contém a base de dados de localidades.</li>
								<li><span class="code">sheer.dbchanges.sql</span> - Banco de dados integral do sheer.</li>
							</ul>
							
							<p>Depois você deverá importar cada dbchanges do projeto que está utilizando.</p>
				
							<p>Após o banco devemos configurar o Sheer para ele ter o endereço do banco de dados e também configurar as suas urls de execução, para isso utilizaremos o arquivo <span class="code">project/setup/config.json</span>.</p>
							
							<div class="row">
							
								<div class="col-xs-12">
									<p>Este arquivo estará dividido em alguns blocos de configurações, o primeiro é a configuração da instancia:</p>
									
									<div>
										<pre>
											<code class="javascript">
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
										<li> <span class="code">project/id</span> devemos configurar um identificador para este projeto, este deve ser único entre todas as instancia do mesmo.</li>
										<li> <span class="code">project/name</span> iremos determinar um nome para o projeto.</li>
										<li> <span class="code">project/domain</span> teremos todos os ip:porta aceito para conexão com essa instância, eles deverão ser separados por ";".</li>
										<li> <span class="code">project/domainPath</span> qual será o path após o domínio em que a base estará hosteada</li>
										<li> <span class="code">project/description</span> Uma descrição para o projeto</li>
									</ul>
								</div>
						
								<div class="col-xs-12">
									<p><strong>Configurando conexão com servidor smtp</strong></p>
							
									<div>
										<pre>
											<code class="javascript">
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
								
									<ul>
										<li> <span class="code">mailer/default</span> Esta é a instancia de smtp-server padrão.</li>
										<li> <span class="code">mailer/{id}</span> Uma conexão própria criada.</li>
										<li> <span class="code">mailer/{id}/host</span> Endereço do servidor com o qual iremos nos comunicar</li>
										<li> <span class="code">mailer/{id}/username</span> Nome do usuário para autenticação</li>
										<li> <span class="code">mailer/{id}/password</span> Senha utilizada para autenticação</li>
									</ul>
								</div>
								
								<div class="col-xs-12">
									<p><strong>Configurando conexão com banco de dados mysql</strong></p>
							
									<div>
										<pre>
											<code class="javascript">
	"database" : {
		"default" : {
			"driver"	: "mysql",
			"host"		: "{host}",
			"username"	: "{username}",
			"password"	: "{password}",
			"database"	: "{schema_name}"
		}
	}
											</code>
										</pre>
									</div>
									
									<ul>
										<li> <span class="code">database/default</span> Esta é a instancia de banco de dados.</li>
										<li> <span class="code">database/{id}</span> Uma conexão própria criada.</li>
										<li> <span class="code">database/{id}/driver</span> Indicador de qual banco de dados irá utilizar, atualmente somente mysql</li>
										<li> <span class="code">database/{id}/host</span> Endereço do servidor com o qual iremos nos comunicar</li>
										<li> <span class="code">database/{id}/username</span> Nome do usuário para autenticação</li>
										<li> <span class="code">database/{id}/password</span> Senha utilizada para autenticação</li>
										<li> <span class="code">database/{id}/database</span> Qual o banco de dados a ser utilizado</li>
									</ul>
								</div>
								
								<div class="col-xs-12">
									<p><strong>Seguindo a mesma ideia configuramos o Facebook, GMail e Instagram</strong></p>
								</div>
							
								<!-- GMail -->
								<div class="col-xs-12 col-sm-6">
									<p><strong>GMAIL</strong></p>
									<p>Configurando conexão com o GMail, seta as propriedades para conexão, só utilizar se realmente o projeto for utilizar devido a performance. Serviço ainda em construção</p>
									<div>
										<pre>
											<code class="javascript">
"google" : {
	"enable"	: false,
	"appId"		: "{your-facebook-app-id}",
	"appSecret"	: "{your-facebook-app-id}",
	"apiKey"	: "{your-facebook-app-id}"
}
											</code>
										</pre>
									</div>
								</div>
								
								<!-- Facebook -->
								<div class="col-xs-12 col-sm-6">
									<p><strong>Facebook</strong></p>
									<p>Configurando conexão com o facebook, seta as propriedades para conexão, só utilizar se realmente o projeto for utilizar devido a performance.</p>
									<div>
										<pre>
											<code class="javascript">
"facebook" : {
	"enable"	: false,
	"appId"		: "{appId}",
	"appSecret"	: "{appSecret}"
}
											</code>
										</pre>
									</div>
								
								</div>
								
								<!-- Instagram -->
								<div class="col-xs-12 col-sm-6">
									<p><strong>Instagram</strong></p>
									<p>Configurando conexão com o Instagram, seta as propriedades para conexão, só utilizar se realmente o projeto for utilizar devido a performance. Serviço ainda em construção</p>
									<div>
										<pre>
											<code class="javascript">
"instagram" : {
	"enable"	: false,
	"appId"		: "{your-facebook-app-id}",
	"appSecret"	: "{your-facebook-app-id}"
}
											</code>
										</pre>
									</div>
								</div>
							</div>
					
						</div>
						
					</div>
				</section>
			
			</div>
			
			<!-- Menu de navegação -->
			<div class="col-xs-12 col-sm-2 col-md-3 col-lg-3">
				<section class="sh-box">
					<div class="sh-box-content">
						<ul class="list-unstyled">
							<li><a href="introducao">Introdução</a></li>
							<li>
								<a href="configuracao-inicial">Configuração inicial</a>
							</li>
							<li>
								<a href="estrutura-dos-modulos">Estrutura dos módulos</a>
								<ul class="">
									<li><a href="estrutura-dos-modulos/introducao">Introdução</a></li>
									<li><a href="estrutura-dos-modulos/modulos">Módulos</a></li>
									<li><a href="estrutura-dos-modulos/dataSources">Data Sources</a></li>
									<li><a href="estrutura-dos-modulos/dataProviders">Data Providers</a></li>
								</ul>
							</li>
						</ul>
					</div>
				</section>
				
			</div>
		
		</div>
	
	</div>

	

	<script>require(['sheer/docs'], function () {});</script>

</body>







	
	<body>
	
		<section class="sh-box">
			<div class="sh-box-content">
				<h1>sheer.js</h1>
		
				<h2>Entendendo o funcionamento do sheer.js</h2>
				
				<h3>sheer.adapters.js</h3>
				<p>Este javascript deverá ser incluido sempre antes da inclusão dos outros scripts do sheer. Ele irá definir os módulos necessários para o Sheer funcionar corremente.
					Irá importar as seguintes bibliotecas caso ainda não incluidas:
				</p>
				<ul>
					<li>RequireJS - <a href="http://github.com/jrburke/requirejs">http://github.com/jrburke/requirejs</a></li>
					<li>jQuery - <a href="http://jquery.org">http://jquery.org</a></li>
					<li>jQuery.colorbox - <a href="http://www.jacklmoore.com/colorbox">http://www.jacklmoore.com/colorbox</a></li>
					<li>jQuery.inputMask - <a href="http://github.com/RobinHerbots/jquery.inputmask">http://github.com/RobinHerbots/jquery.inputmask</a></li>
					<li>jQuery.autosize - <a href="http://www.jacklmoore.com/autosize">http://www.jacklmoore.com/autosize</a></li>
				</ul>
				
				<h3>sheer.js</h3>
				<p>Script principal do sheer. Este funcionará em qualquer projeto, ele não irá gerar incompatibilidades e não trará problemas para o html já produzido</p>
				<p>Este irá conter diversas sub-bibliotecas e definições de módulos próprios que poderão ser utilizados pelo desenvolvedor e são dependencias do Sheer.js</p>
				<p>Aqui enumero e explico alguns dos módulos contidos:</p>
				
				<h4>sheer/dataProvider</h4>
				<p>Método que permite o carregamento de dados da plataforma. Utiliza "sheer/ajax" para envio da requisição.</p>
				<pre><code class="javascript">
function (dataProviderId, getParameters, postParameters, beforeSend);
				</code></pre>
				
				<h4>sheer/renderable</h4>
				<p>Método que permite o carregamento de um renderable renderizado. Utiliza "sheer/ajax" para envio da requisição.</p>
				<pre><code class="javascript">
function (renderableId, getParameters, postParameters, beforeSend);
				</code></pre>
				
				<h4>sheer/action</h4>
				<p>Método que permite a execução de um action no servidor. Utiliza "sheer/ajax" para envio da requisição.</p>
				<pre><code class="javascript">
function (actionId, getParameters, postParameters, beforeSend);
				</code></pre>
				
				<h4>sheer/ajax</h4>
				<p>Forma rápida de se efetuar uma requisição ajax pelo Sheer.</p>
				<pre><code class="javascript">
function ( url, method, data, beforeSend );
				</code></pre>
				<p>Este irá retornar um jQuery.promise para mapear a requisição.</p>
				
				<h4>sheer/form</h4>
				<p>???????????</p>
				
				<h4>sheer/ui</h4>
				<p>???????????</p>

				<h4>sheer/auth</h4>
				<p>???????????</p>

				<h4>sheer/fb</h4>
				<p>???????????</p>

				<h4>sheer/gapi</h4>
				<p>???????????</p>

				<h4>sheer/notify</h4>
				<p>???????????</p>

				<h4>sheer/components</h4>
				<p>???????????</p>

				<h4>sheer/route</h4>
				<p>???????????</p>
				
				<h4>sheer/lib</h4>
				<p>???????????</p>

			</div>
		</section>
		
		<script>require(['sheer/docs'], function () {});</script>
	
	</body>
	
</html>