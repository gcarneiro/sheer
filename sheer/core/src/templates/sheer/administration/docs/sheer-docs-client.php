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
						
						<h3 id="sh-is">sheer/instantSearch <small>plugin para buscas instantâneas</small></h3>
						
						<p>O InstantSearch é poderoso em buscar renderables e substituir parte do seu html em resposta em um pedaço da página para permitir buscas instantanes, ordenações, paginação e outros.</p>
						
						<p>
							O IS antes do Sheer 1.9.0 era realizado de uma forma onde se era necessários um wrapper configurado no json da página e um template para o IS.
							Remodelamos o IS para permitir buscas internas dentro de um próprio renderable, sem a necessidade daquela engenharia anterior.<br />
							Para isso tivemos que implementar algumas mudanças e iremos depreciar o IS antigo no Sheer 3.
						</p>
						
						<p>O InstantSearch se tornou um módulo independente do Sheer e agora não necessita dele para ser utilizado. As suas dependências são:</p>
						
						<ul class="">
							<li>jquery</li>
							<li>sheer/validation</li>
							<li>sheer/notify</li>
						</ul>
						
						<h4>Estrutura html</h4>
						
						<p>Para termos um IS funcional iremos precisar de um formulário e uma div <small>(ou outro elemento qualquer - este será o holder onde o conteúdo será substituido)</small>.</p>
						<p>
							O formulário será o responsável por determinar os disparos para busca de conteudo, ele deverá conter todos os campos que devem ser submetidos para a busca.
							Este deve ser marcado com <span class="code">sh-is="inside"</span>. O envio do valor <span class="code">inside</span> dentro da tag agora é <strong>obrigatório</strong>.
							<br />
							<br />
							Neste também devemos inserir o marcardor <span class="code">sh-is-control="isIdentifier"</span>. Este irá determinar o identificador do IS. Este identificador deve ser 
							inserido nos dois elementos para que seja possível o vínculo entre os dois.
							<br />
							<br />
							O atributo <span class="code">action</span> deve descrever qual a url de destino que iremos capturar o renderable.
						</p>
						<p>A div será o local onde o html será depositado. Esta deve ser identificada com <span class="code">sh-is-replacable="isIdentifier"</span>, onde ser inserido o identificador do IS</p>
						
						<p>Após a configuração do <span class="code">sh-is="inside"</span>, <span class="code">sh-is-control="isIdentifier"</span> e <span class="code">sh-is-replacable="isIdentifier"</span>
							o IS já está pronto para o seu funcionamento.
						</p>
						
						<p>São permitidos outras configurações através do <strong>sh-api</strong>.
						
						<h4>sh-api</h4>
						
						<table class="table">
							<thead>
								<tr>
									<th class="sh-w-200">atributo</th>
									<th>uso</th>
									<th>Info</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><span class="code">sh-is</span></td>
									<td>Obrigatório</td>
									<td>Também deve ser enviado o valor <code>inside</code> para este atributo.</td>
								</tr>
								<tr>
									<td><span class="code">sh-is-control</span></td>
									<td>Obrigatório</td>
									<td>O valor a ser passado neste será o <strong>identificador único</strong> do IS. Deve ser descrito no formulário e no replacable</td>
								</tr>
								<tr>
									<td><span class="code">sh-is-replacable</span></td>
									<td>Obrigatório</td>
									<td>O valor a ser passado neste será o <strong>identificador único</strong> do IS. Deve ser descrito no formulário e no replacable</td>
								</tr>
								<tr>
									<td><span class="code">sh-is-order</span></td>
									<td>Opcional</td>
									<td>
										Este deve ser inserido em qualquer elemento descendente do <span class="code">sh-is-replacable</span> e irá determinar a ordenação runtime do IS.
										<br /><br />
										O seu valor poderá ser enviado de duas formas <strong>idField</strong> ou <strong>relationPath:idField</strong><br /><br />
										A ordenação sempre irá começar de forma ascendente, e quando for pedido para reordenar pelo mesmo campo da ultima vez a sua ordenação é alterada.
									</td>
								</tr>
								<tr>
									<td><span class="code">sh-is-ignore</span></td>
									<td>Opcional</td>
									<td>
										Este determina que aquele campo será ignorado na escuta de keyup para envio instantaneo.
									</td>
								</tr>
							</tbody>
						</table>
						
						<p><span class="code">sh-is</span></p>
						
						
						
						
						
						
						
						
						
						
						
						
						
						
						
						
						
						
			
						<h3 id="sh-validation">sheer/validation <small>Validação de formulários e campos</small></h3>
			
						
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