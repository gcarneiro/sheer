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