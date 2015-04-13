define('routeTest', ['sheer'], function (sheer) {
	
	//Mapear rotas que já foram executadas
	var holder = null;
	
	sheer.route.addRoute('');
	
	sheer.route.addRoute('quem-sou', {
		initRoute: function () {
			alert('init route');
		},
		initDOMElements: function () {
			alert('dome elemtnso');
			$('button').on('click', function () {
				alert('COOORRREEEEEE!');
			});
		}
	});
	
	//Criando rotas padrões
	sheer.route.addRoute('noticias');
	sheer.route.addRoute('historia');
	sheer.route.addRoute('contato');
	
	//Setando o holder padrão para o "sheer.route"
	sheer.route.setDefaultHolder('#holder');
	//Setando loader padrão
	sheer.route.setDefaultLoader('#loader');
	
	//Iniciando processamento default
	sheer.route.processInitialRoute();
	
});
require(['routeTest'], function () {});