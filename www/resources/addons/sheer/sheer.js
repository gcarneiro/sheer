
//var sheer_RequireJS_exist = (typeof define === 'function' && define.amd);

//CONFIGURANDO O REQUIREJS
require.config({
    baseUrl: '',
    paths: {
    	'sheer'				: 'resources/addons/sheer',
    	
    	'ckeditor'			: 'resources/addons/ckeditor/ckeditor',
    	'datepicker'		: 'resources/addons/datepicker/datepicker',
    	'datepicker-ptbr'	: 'resources/addons/datepicker/locales/bootstrap-datepicker.pt-BR',
    	'select2'			: 'resources/addons/jquery.select2/select2.full.min',
    	'select2-i18n'		: 'resources/addons/jquery.select2/i18n',
    	'autosize'			: 'resources/addons/jquery.autosize/jquery.autosize.min',
    	'moment'			: 'resources/addons/moment/moment',
    	'qtip'				: 'resources/addons/qtip/jquery.qtip.min',
    	
    	'facebook'			: '//connect.facebook.net/pt_BR/all',
    	'google-api'		: '//apis.google.com/js/client:plus.js?onload=OnLoadCallback&lang=pt_BR&parsetags=explicit',
    	'google-api' 		: {
			exports: 'gapi'
		}
    },
	shim: {
		'datepicker' 										: ['jquery'],
		'datepicker-ptbr' 									: ['jquery', 'datepicker'],
		'autosize' 											: ['jquery'],
		'qtip'												: ['jquery'],
		'select2-i18n'										: ['select2']
		'facebook' : {
			exports: 'FB'
		}
	},
	waitSeconds: 0
});

/*

Sheer - Basics
Esta é a biblioteca básica do sheer. Dentro dela iremos comportar alguns conceitos descritos abaixos

	sheer/dataProvider
	sheer/renderable
	sheer/action
	sheer/pages
	sheer/ajax
	sheer/form/validation
	sheer/ui/mask
	sheer/route
	
	Dependencias existentes e que são automaticamente tratadas caso o requireJS exista
	jquery.inputMask
		inputMaskDate, inputMaskNumeric
	facebook

*/
define('sheer', ['jquery'], function () {

	
	var sheer = {};

	/*
	 * Método para recuperar dados do Sheer através de um DataProvider
	 *
	 */
	sheer.dataProvider = function (dataProviderId, getParameters, postParameters, beforeSend) {
		//calculando url
		var url = 'dp.php?dp='+dataProviderId;
		if( getParameters ) {
			url += '&'+$.param(getParameters);
		}
		//efetuando requisição
		var jqPromise = sheer.ajax(url, 'POST', postParameters, beforeSend);

		//tratando falha
		jqPromise.fail(function () {
			console.warn('DataProvider - Erro de comunicação com o servidor');
		});
		return jqPromise;
	};

	/*
	 * Método para renderizar um renderable através dos seus identificadores
	 *
	 */
	sheer.renderable = function (renderableId, getParameters, postParameters, beforeSend) {
		//calculando url
		var url = 'renderer.php?rd='+renderableId;
		if( getParameters ) {
			url += '&'+$.param(getParameters);
		}
		//efetuando requisição
		var jqPromise = sheer.ajax(url, 'POST', postParameters, beforeSend);

		return jqPromise;
	};

	/*
	 * Método para enviar uma ação para o sheer
	 * Este irá trabalhar com o idActionHandler, com parametros get, post
	 *
	 */
	sheer.action = function (actionId, getParameters, postParameters, beforeSend) {

		//calculando url
		var url = 'action.php?ah='+actionId;
		if( getParameters ) {
			url += '&'+$.param(getParameters);
		}
		//efetuando requisição
		var jqPromise = sheer.ajax(url, 'POST', postParameters, beforeSend);

		return jqPromise;

	};

	/*
	 * Objeto para controlar as páginas do sheer.
	 * Se devemos efetuar um redirect ou posteriormente controlar tudo por ajax
	 */
	sheer.pages = (function () {

		/**
		 * Método para fazer redirect de páginas a partir do javascript
		 */
		function redirectPage ( url ) {
			window.location.href = url;
		}

		/**
		 * Método para recarregar a página atual
		 */
		function reloadPage () {
			window.location.reload();
		}

		return {
			redirect: redirectPage,
			reload: reloadPage
		};


	})();

	/**
	 * AJAX - Método para enviar requisições ajax de forma padrão pelo sheer
	 *
	 * @param url
	 *          String que determina qual será a url do ajax
	 * @param method [String]
	 *          [GET, POST]
	 * @param data
	 * @param beforeSend
	 *
	 * @return $.promise
	 */
	sheer.ajax = function ( url, method, data, beforeSend ) {

		//CRIANDO DADOS DO AJAX
		var ajaxConfig = {
			url: url,
			type: method,
			data: data,
			beforeSend: function (jqXHR, plainObject) {
				if( isFunction(beforeSend) ) {
					beforeSend(jqXHR, plainObject);
				}
			}
		};

		//TRATAMENTO ESPECIAL PARA FORM.DATA
		if( data instanceof FormData ) {
			ajaxConfig.processData = false;
			ajaxConfig.contentType = false;
		}

		//efetuando requisicao
		var jqXHR = $.ajax(ajaxConfig);

		//Gerando objeto de promessa para resposta
		var xhrPromise = jqXHR.promise();
		xhrPromise.abort = jqXHR.abort;

		return xhrPromise;

	};

	/*
	 * Objeto responsável pelo controle dos formulários a serem processados com o sheer "sh-form"
	 */
	sheer.form = (function () {

		/*
		 * Controle de validação de formulários
		 * Este é utilizado normalmente pelo sheer/form
		 */
		var _validation = (function () {
			/*
			 * Validadores
			 */
			var validadores = (function () {

				/**
				 * Método para validar se a entrada é um número
				 */
				function validateNumber (string, node) {

					//removendo . e trocando a ,
					string = string.replace(/\./g, '');
					string = string.replace(/\,/g, '.');

					//determinando o float a partir da entrada
					if( !isNumber(string) ) {
						return false;
					}
					var number = parseFloat(string);

					//verificando maximo
					if( node.getAttribute('data-number-max') ) {
						var max = parseFloat(node.getAttribute('data-number-max'));
						if( number > max ) { return false; }
					}
					//verificando minimo
					if( node.getAttribute('data-number-min') ) {
						var min = parseFloat(node.getAttribute('data-number-min'));
						if( number < min) { return false; }
					}

					return true;
				}

				/**
				 * Método para validar se a entrada é um número
				 */
				function validateInteger (string, node) {

					//validando numero
					var validated = validateNumber(string, node);
					if( !validated ) { return false; }

					//removendo . e trocando a ,
					string = string.replace(/\./g, '');
					string = string.replace(/\,/g, '.');
					var number = parseFloat(string);

					//determinando se é inteiro
					if ( number % 1 != 0 ) { return false; }

					return true;
				}

				/**
				 * Método para validar entradas textuais
				 * Verifica o minimo e máximo de caracteres
				 */
				function validateText (string, node) {

					var lengthMinimo = null;
					var lengthMaximo = null;
					//verificando se temos mínimo ou máximo de caracteres
					lengthMinimo = parseInt(node.getAttribute('data-text-min'));
					lengthMaximo = parseInt(node.getAttribute('data-text-max'));

					//tratando tamanho minimo
					if( lengthMinimo && lengthMinimo > 0 && string.length < lengthMinimo ) {
						return false;
					}
					//tratando tamanho maximo
					else if( lengthMaximo && lengthMaximo > 0 && string.length > lengthMaximo ) {
						return false;
					}
					else {
						return !!string.length;
					}

				}

				/**
				 * Método para validar CPFs
				 */
				function validarCPF (string, node) {
					//VALIDAÇÃO SIMPLES INICIAL
					var validado = !!string.toString().match(/^[0-9]{3}\.?[0-9]{3}\.?[0-9]{3}-?[0-9]{2}$/);
					if( !validado ) { return false; }

					string = string.replace(/[^0-9]/g, '');

					var Soma, Resto, i;

					Soma = 0;
					if (string == "00000000000") {
						return false;
					}

					for (i = 1; i <= 9; i++) {
						Soma = Soma + parseInt(string.substring(i - 1, i)) * (11 - i);
					}
					Resto = (Soma * 10) % 11;

					if ((Resto == 10) || (Resto == 11)) {
						Resto = 0;
					}

					if (Resto != parseInt(string.substring(9, 10))) {
						return false;
					}

					Soma = 0;
					for (i = 1; i <= 10; i++) {
						Soma = Soma + parseInt(string.substring(i - 1, i)) * (12 - i);
					}

					Resto = (Soma * 10) % 11;
					if ((Resto == 10) || (Resto == 11)) {
						Resto = 0;
					}

					if (Resto != parseInt(string.substring(10, 11))) {
						return false;
					}

					return true;
				}

				//TODO VALIDAR DATE, DATETIME E TIME DE ACORDO COM O START-DATE E END-DATE

				var self = {
					"text": validateText,
					"number" : validateNumber,
					"integer" : validateInteger,
					"cpf" : validarCPF,
					"rg" : function (string) {
						if( string.length > 5 && string.length < 16 ) {
							return true;
						}
						return false;
					},
					"cep" : function (string) {
						return !!string.toString().match(/^[0-9]{2}\.[0-9]{3}-[0-9]{3}$/);
					},
					"email" : function (string) {
						return !!string.toString().match(/^[0-9a-zA-Z][0-9a-zA-Z_.-]+@[0-9a-zA-Z][0-9a-zA-Z_.-]+\.[a-zA-Z]{2,4}$/);
					},
					"telefone": function (string) {
						return !!string.toString().match(/^\([0-9]{2}\) [0-9]{4,5}\.[0-9]{4,5}$/);
					},
					"url": function (string) {
						return !!string.toString().match(/^(http|https):\/\/(www\.)?[a-zA-Z0-9-][a-zA-Z0-9-\.]*\.[a-zA-Z]{1,3}$/);
					},
					"date": function (string) {
						return !!string.toString().match(/^(0[0-9]|[0,1,2][0-9]|3[0,1])\/(0[0-9]|1[0,1,2])\/[0-9]{4}$/);
					},
					"datetime": function (string) {
						var date = string.toString().slice(0, 10);
						var time = string.toString().slice(11);
						var validated = true;

						validated = validated && !!date.match(/^(0[0-9]|[0,1,2][0-9]|3[0,1])\/(0[0-9]|1[0,1,2])\/[0-9]{4}$/);
						validated = validated && !!time.match(/^([0-1][0-9]|[2][0-3])(\:[0-5][0-9])(\:[0-5][0-9])?$/);
						return validated;
					},
					"time": function (string) {
						var validated = true;
						validated = validated && !!string.match(/^([0-1][0-9]|[2][0-3])(\:[0-5][0-9])(\:[0-5][0-9])?$/);
						return validated;
					}
				}

				return self;

			})();

			/*
			 * Callbacks padrões
			 */
			var _callbacks = {
				/*
				 * Sucesso de um node
				 */
				success: function (dataNode) {
					if( dataNode.getAttribute('type') == 'file' ) {
						$(dataNode).closest('div.sh-input-file').removeClass('sh-val-failed');
					}
					else {
						$(dataNode).closest('div').removeClass('sh-val-failed');
					}

				},
				/*
				 * Sucesso do set inteiro
				 */
				setSuccess: function (dataset, validated, invalids) {

				},
				/*
				 * Falha de um node
				 */
				error: function (dataNode) {
					if( dataNode.getAttribute('type') == 'file' ) {
						$(dataNode).closest('div.sh-input-file').addClass('sh-val-failed');
					}
					else {
						$(dataNode).closest('div').addClass('sh-val-failed');
					}
				},
				/*
				 * Falha do set inteiro
				 */
				errorSet: function (dataset, validated, invalids) {

				},
				customValidators: []
			};

			/**
			 * Método principal de validação
			 * @param Node dataset Nó html que possui Campos para validar como filhos
			 * @param Object userCallBacks Objeto que contém os callbacks para a validação
			 */
			var validarDados = function (dataset, userCB) {
				
				//Verificando se existe o atributo que cancela a validação.
				if ( typeof dataset == 'object' && typeof dataset.getAttribute == 'function' && (dataset.getAttribute('sh-form-noValidate') || dataset.getAttribute('sh-form-novalidate')) ) {
					return true;
				}
				
				//tratando e obtendo os callbacks finais
				var callbacks = {};
				$.extend(true, callbacks, _callbacks, userCB);

				//variaveis de controle
				var nodesInvalid = [];
				var nodesValid = [];
				var errors = 0;
				var jqDataset = $(dataset);

				//atualizando valores do ckeditor
				if( typeof CKEDITOR != 'undefined'  ) {
					for ( instance in CKEDITOR.instances ) {
						CKEDITOR.instances[instance].updateElement();
					}
				}

				//buscando dataNodes
				var dataNodes = jqDataset.find('input[name], textarea[name], select[name]');
				var validationId = getUniqueId();

				//vamos fazer a validação de cada um deles
				dataNodes.each(function () {

					var validated = validarCampo(this, callbacks);

					if( validated === false ) {
						nodesInvalid.push(this);
						errors++;
						callbacks.error(this, validationId);
					}
					else if ( validated ) {
						nodesValid.push(this);
						callbacks.success(this, validationId);
					}
				});

				/*
				 * Verificando ocorrencia de erros
				 */
				if( errors > 0 ) {
					callbacks.errorSet(dataset, nodesValid, nodesInvalid);
					return false;
				}
				callbacks.setSuccess(dataset, nodesValid, nodesInvalid);
				return true;
			}

			/**
			 * Método para validar um campo único
			 */
			function validarCampo ( dataNode, callbacks ) {

				//capturando o valor do campo
				var value = $(dataNode).val();
				if(value == '<br />' || value == '<br/>') { value = ''; }

				//determinando modelo de validacao
				var validationType = undefined;
				if( dataNode.getAttribute('validationtype') && dataNode.getAttribute('validationtype').length ) {
					validationType = dataNode.getAttribute('validationtype');
				}
				else if ( dataNode.getAttribute('validationType') && dataNode.getAttribute('validationType').length ) {
					validationType = dataNode.getAttribute('validationType');
				}
				else {
					validationType = 'text';
				}

				//iniciando variaveis de apoio
				var validated = false;
				var required = false;

				//verificamos se ele é obrigatorio
				if( dataNode.getAttribute('required') !== null ) {
					required = true;
				}
				//não sendo, verificamos se o input possui valor
				else {
					//se for input
					if( dataNode.tagName.toLowerCase() == 'input' ) {
						//só considero os do tipo texto
						var inputType = dataNode.getAttribute('type');
						if( !inputType ) { inputType = 'text'; }

						if( inputType == 'text' && value.length > 0 ) {
							required = true;
						}
					}
				}

				//verificando se ele é required
				if( !required  ) {
					return null;
				}

				//precisamos olhar se ele é do tipo radio ou checkbox, se for temos que fazer uma validacao completamente diferente
				if(dataNode.tagName.toLowerCase() == 'input' && ( dataNode.getAttribute('type') == 'radio' || dataNode.getAttribute('type') == 'checkbox' ) ) {
					validated = false;
					//FIXME acredito que a concatenacao do nome do imput ali dentro pode dar problema devido a []
					if( $(dataNode).closest('form').find('input[name="'+dataNode.getAttribute('name')+'"]:checked').length > 0 ) {
						validated = true;
					}
				}
				else {

					var validationFunction = undefined;

					//irei verificar se existe o validador
					if( isFunction(validadores[validationType]) ) {
						validationFunction = validadores[validationType];
					}
					else {
						validationFunction = validadores["text"];
					}

					//Validação padrão
					validated = validationFunction(value, dataNode);

				}

				//VALIDAÇÃO CUSTOMIZADA
				if( callbacks.customValidators[dataNode.getAttribute('id')] ) {
					newValidated = callbacks.customValidators[dataNode.getAttribute('id')]($(dataNode));
					if(newValidated != null) { validated = newValidated; }
				}

				return validated;

			}

			return validarDados;

		})();

		/*
		 * Controlador de formulários do sheer
		 * Este irá escutar todos os submits dos forms "sh-form" e irá trata-los de forma padrão
		 *      -> Impedir que sejam submetidos mais de um processo
		 *      -> Validar todos os dados
		 *
		 */
		var _form = (function () {

			//Variáveis de controle
			var inicializado = false;

			/**
			 * Método inicializador dos formularios
			 */
			function init() {
				if( inicializado ) { return; }
				inicializado = true;

				//escutando os envios de formularios sh-form
				$(document).on('submit', 'form[sh-form]', function (evt) {
					evt.preventDefault();
					efetuarRequisicao(this);
				});
			}

			/**
			 * Método de envio do formulário
			 * @param form
			 * 		Parametro deve trazer o elemento do formulário em JS (não usar JQUERY)
			 * @param config
			 * 		validateForm: boolean
			 * 			campo para informar se é necessário ou não validar o formulário (true = valida, false = não valida)
			 * 		userCB: callback
			 * 			success:
			 * 			error:
			 * 			campos para informar o que acontece caso a validação do formulário de certo (success) ou errado(error).
			 */
			function efetuarRequisicao ( form , config ) {

				//VARIAVEIS DE CONTROLE
				var jqForm = $(form);
				var validado = true;
			
				//se não existir o objeto, cria-se um do formato esperado
				if( typeof(config) !== 'object'){
					config = {
						validateForm: true,
						userCB: null
					};
				}
				
				//Valido em qualquer momento em que validateForm não tenha sido enviado ou tenha sido enviado != false
				if( config.validateForm !== false ) {
					validado = _validation(form, config.userCB);
				}
				
				//Verificando validacao
				if( !validado ) {return;}
				
				//verificando configurações do formulário
				if( !form.getAttribute('action') ) {
					console.warn('Formulário não possui action definido. Abortando operação.');
					return;
				}
				else if ( !form.getAttribute('method') ) {
					console.warn('Formulário não possui method definido. Assumindo POST.');
					form.setAttribute('method', 'POST');
				}

				//crio os controladores de submissao do formulario
				if( form.shForm == undefined ) {
					form.shForm = {
						sending: false,
						useFormData: true
					};
					
					//Determino se o formulário deve utilizar formData para o envio
					if( form.querySelectorAll('input[type="file"][name]').length > 0 ) {
						form.shForm.useFormData = true;
					}
					
				}

				//verifico se o formulário já não está em processo de envio
				if( form.shForm.sending ) { return; }
				
				//CRIANDO OBJETO DE CONFIGURAÇÃO DO AJAX
				var xhrOptions = {
					url: form.getAttribute('action'),
					type: form.getAttribute('method'),
					//metódo de controle pré requisicao
					beforeSend: function (jqXHR, plainObject) {
						form.shForm.sending = true;
					},
					dataType: 'json'
				};
				if( form.shForm.useFormData ) {
					//criando formData do formulário
					var formData = new FormData(form);
					xhrOptions.data 		= formData;
					xhrOptions.processData 	= false;
					xhrOptions.contentType 	= false;
				}
				else {
					xhrOptions.data = $(form).serialize();
				}
				
				//efetuando a requisição ajax
				var xhr = $.ajax(xhrOptions);
				//verificando envio da requisição
				if( !xhr ) {
					return;
				}
				
				//capturando a promessa da ação
				var xhrPromise = xhr.promise();
				xhrPromise.abort = xhr.abort;

				//DETERMINANDO PROCESSADOR DE RESPOSTA
				//verificando processador de resposta customizado
				var responseHandler = form.getAttribute('sh-form-responseHandler') || form.getAttribute('sh-form-rh');
				if( responseHandler && responseHandler.length > 0 ) {

					//CASO EU REALMENTE POSSUA UM RESPONSE HANDLER IREI EXECUTA-LO
					//busco o nome do módulo e da função
					var tmp = responseHandler.split('][');
					var module = tmp[0].replace('[', '');
					var fn = tmp[1].replace(']', '');

					//chamando o modulo pelo requirejs e executando a função desejada
					require([module], function (context) {
						var response = executeFunctionByName(fn, context, xhrPromise, jqForm);
						if( response == undefined ) {
							console.warn('O responseHandler pode não ter sido encontrado.');
						}
					});

				}
				//nao tendo customizado utilizamos o padrao
				else {
					rhDefaultEnviarForm(xhrPromise, jqForm);
				}
				
				//INSERINDO LOADING PADRAO SE FOR PARA INSERIR
				var loadingPadrao = form.getAttribute('sh-form-loading');
				if( loadingPadrao != 'false' ) {
					if( require.s.contexts._.registry['sheer/adm'] || require.s.contexts._.defined['sheer/adm'] ) {
						require(['sheer/adm'], function (sheerAdm) {
							sheerAdm.showSectionLoading(xhrPromise, form);
						});
					}
				}
				
				//sempre libero o formulário para novo envio
				xhrPromise.always(function () {
					form.shForm.sending = false;
				});

				return xhrPromise;
			}
			
			/**
			 * ResponseHandler padrão para os formulários do sheer
			 * 
			 * Este passa a verificar o parametro "sh-form-successRedirect" para redirecionar para a página desejada
			 * 
			 */
			function rhDefaultEnviarForm (promise, node) {
				
				promise.done(function (data) {
					if(data.status) {
						
						//Redirecionando conforme vontade do desenvolvedor
						if( !!node[0].getAttribute('sh-form-successRedirect') ) {
							window.location.href = node[0].getAttribute('sh-form-successRedirect');
						}
						else {
							window.location.reload();
						}
					}
					else {
						sheer.notify.create(data.message, 'fail');
					}
				});

				promise.fail(function (data) {
					sheer.notify.create('Erro de comunicação com o servidor. Tente novamente mais tarde.', 'fail');
				});
			}

			//iniciando
			init();
			
			return {
				send: efetuarRequisicao
			};

		})();

		/*
		 * Retorno do sheer/form
		 */
		return {
			validation: {
				validar: _validation
			},
			send: _form.send
		};

	})();

	/*
	 * Iniciando as Definições de userInterface para o Sheer
	 * A partir deste bloco tudo relacionado a userInterface automatizado será descrito abaixo
	 *      Masks
	 *
	 */
	sheer.ui = {};

	sheer.ui.masks = (function () {

		var inicializado = false;
		var mascarasAplicadas = false;
		var customMasks = {};

		var _paramName = 'sh-mask';
		var _paramNameOld = 'mask';

		/**
		 * Método de inicialização - Este só deve ser chamado após o DOM ter carregado
		 */
		function init () {

			if( inicializado ) {
				return;
			}
			inicializado = false;
			
			//Configurando e iniciando mascaras
			require(['inputMask'], function () {
				config();
			});
		}

		/**
		 * Método para aplicar as máscaras ao nosso DOM sem destinção
		 */
		function applyToContext (context) {

			if( context === undefined || !context ) {
				context = document;
			}
			else if (context instanceof jQuery) {
				context = context[0];
			}

			//Carregando nós
			var nodes = context.querySelectorAll('['+_paramName+'], ['+_paramNameOld+']');
			$.each(nodes, function () {
				applyToNode(this);
			});

		};

		/**
		 * Método para aplicar as mascara dados sobre um nó
		 * O Problema desta abordagem é que a mascara só será aplicada se o usuário selecionar o campo. Ela não entrará sempre aplicada e com isso podemos ter valores mal formatados
		 */
		function applyToNode ( node ) {

			//Carregando a dependencia do inputMask
			require(['inputMask'], function () {

				//retiro quem já teve mascara aplicada
				if( node.maskApplied !== undefined ) {
					return;
				}

				//Crio o objeto jquery
				var jqNode = $(node);
				var mascara = node.getAttribute(_paramName) || node.getAttribute(_paramNameOld);
				node.maskApplied = true;

				//Determino qual mascara aplicar
				if( mascara == 'numero' || mascara == 'number' ) {
					jqNode.inputmask({
						mask:'9',
						repeat: 30,
						greedy: false,
						rightAlign: false
					});
				}
				else if ( mascara == 'numero-separado' ) {
					jqNode.inputmask({
						mask: 'n',
						repeat: 30,
						greedy: false,
						definitions: {
							'n': {
								"validator": "[0-9\-\./]",
								"cardinality": 1
							}
					    },
					    rightAlign: false
					});
				}
				else if ( mascara == 'numero-pontuado' ) {
					jqNode.inputmask({
						mask: 'n',
						repeat: 30,
						greedy: false,
						definitions: {
							'n': {
								"validator": "[0-9\.]",
								"cardinality": 1
							}
					    },
					    rightAlign: false
					});
				}
				else if ( mascara == 'inteiro' || mascara == 'integer' ) {
					jqNode.inputmask('decimal', {
						digits: 0,
						autoGroup: true, 
						groupSeparator: ".", 
						radixPoint: ",",
						groupSize: 3,
					    rightAlign: false
					});
				}
				else if ( mascara == 'dinheiro' || mascara == 'money' || mascara == 'decimal' ) {
					jqNode.inputmask('numeric', {
						digits: 2,
						autoGroup: true, 
						groupSeparator: ".", 
						radixPoint: ",",
						groupSize: 3,
					    rightAlign: false
					});
				}
				else if ( mascara == 'float' ) {
					jqNode.inputmask('numeric', {
						radixPoint: ".",
						digits: 8,
					    rightAlign: false
					});
				}
				else if ( mascara == 'telefone' || mascara == 'phone' ) {
					jqNode.inputmask({
						mask: '(99) 9999.9999[9]',
						greedy: false
					});
				}
				else if ( mascara == 'cpf' ) {
					jqNode.inputmask('999.999.999-99');
				}
				else if ( mascara == 'cnpj' ) {
					jqNode.inputmask('99.999.999/9999-99')
				}
				else if ( mascara == 'rg' ) {
					jqNode.inputmask({
						mask: 'n',
						repeat: 15,
						greedy: false,
						definitions: {
							'n': {
								"validator": "[a-zA-Z0-9\-\./]",
								"cardinality": 1
							}
					    },
					    rightAlign: false
					});
				}
				else if ( mascara == 'cep' ) {
					jqNode.inputmask('99.999-999')
				}
				else if ( mascara == 'date' ) {
					jqNode.inputmask('date',{'placeholder' : 'dd/mm/aaaa'});
				}
				else if ( mascara == 'datetime' ) {
					jqNode.inputmask('datetime');
				}
				else if ( mascara == 'time' ) {
					jqNode.inputmask('h:s');
				}
				else if ( mascara == 'cns' ) {
					jqNode.inputmask('999 9999 9999 9999');
				}
				else if ( mascara == 'pis' ) {
					jqNode.inputmask('999.99999.99-9');
				}
				else if ( mascara == 'cartaoCreditoPadrao'){
					jqNode.inputmask('9999 9999 9999 9999');
				}
				else if ( mascara == 'cartaoAmEx'){
					jqNode.inputmask('9999 999999 99999');
				}
				else {
					if( customMasks[mascara] !== undefined &&  typeof customMasks[mascara] == 'function' ) {
						customMasks[mascara](node);
					}
				}
			});

		}

		/**
		 * Método para configuração inicial das mascaras
		 */
		function config () {
			
			if( typeof $.inputmask == 'undefined' ) {
				window.setTimeout(config, 500);
				return;
			}
			
			//Escutando os campos
			$(document).on('focus', '['+_paramName+'], ['+_paramNameOld+']', function () {
				applyToNode(this);
			});

			//Quando a página carregar executo
			$(document).ready(function () {
				applyToContext();
				mascarasAplicadas = true;
			});
			$.extend($.inputmask.defaults, {
				autoUnmask: true,
		        showMaskOnHover: false,
		        showMaskOnFocus: false, 
		        placeholder: "_",
		        removeMaskOnSubmit: false,
		        onUnMask: function (masked, unmasked) {
		            var lastTypedChar = unmasked[unmasked.length-1];
		            var lastIndex = masked.lastIndexOf(lastTypedChar)+1;
		            var finalMasked = masked.substring(0, lastIndex);
		            return finalMasked;
		        }
			});
		}

		/**
		 * Método para inserir mascaras customizadas
		 */
		function insertCustomMask (name, func) {
			customMasks[name] = func;

			//Aplico esta máscara customizada ao DOM somente se as máscaras já tiverem sido aplicadas uma única vez
			if( mascarasAplicadas ) {
				applyToContext();
			}
		}

		//Iniciando a aplicação
		$(document).ready(function () {
			init();
		});

		return {
			apply: applyToContext,
			insertCustomMask : insertCustomMask
		};
	})();

    /*
     * Controle de autenticação do Sheer
     * Este irá determinar qual o usuário logado e suas informações
     * Para capturar as informações do usuário deve ser utilizado o "sheer.auth.info()"
     */
    sheer.auth = (function () {

        /*
         * Controles gerais
         */
        var userInfo = {
            authenticated: false
        };
        tempoEsperaBuscarInfo = 0;
        
        /**
         * Processo que irá verificar se o usuário está logado e foi deslogado pelo servidor ou alguma outra ação.
         * Se isso acontecer ele irá recarregar página enviando para a página inicial
         */
        var idTemporizador = null;
        function iniciaTemporizadorVerificadorAutenticacao () {
        	//Verificando autenticação - se não estiver logado nem procedo
        	if( userInfo.authenticated == false || idTemporizador ) {
        		return;
        	}
        	
        	idTemporizador = window.setInterval(temporizadorVerificadorAutenticacao, (1000*60*1) );
        }
        /**
         * Método para efetuar a verificação de autenticação
         * Se ele não estiver logado irei redireciona-lo
         */
        function temporizadorVerificadorAutenticacao () {
        	var xhrPromise = loadUserInfo();
        	
        	xhrPromise.done(function (data) {
        		data = JSON.parse(data);
                if ( (data && data.authenticated) ) {
                }
                else{
                	window.location.href="index.php";
                }
        	});
        }

        /**
         * Método responsável por buscar as informações do usuário autenticado
         * Este tem a inteligência de buscar novamente caso não funcione a requisição
         */
        function loadUserInfo () {

            var promise = $.get('dp.php?dp=user/getAuthenticatedUserInfo');
            //requisição concluida
            promise.done(function (data) {
                data = JSON.parse(data);
                if (data && data.authenticated) {
                    userInfo = data;
                    //Iniciando temporizador
                    iniciaTemporizadorVerificadorAutenticacao();
                }
                else{
                	userInfo = false;
                }
            });
            //requisição falhou
            promise.fail(function () {
                window.setTimeout(loadUserInfo, tempoEsperaBuscarInfo);
            });

            //recalculando tempo para nova busca
            if (tempoEsperaBuscarInfo == 0) {
                tempoEsperaBuscarInfo = 500;
            }
            else {
                tempoEsperaBuscarInfo = tempoEsperaBuscarInfo * 2;
            }
            
            /*
             * Retorno a promessa do carregamento
             */
            return promise;

        }

        //BUSCAR AS INFORMAÇÕES NO SHEER
        loadUserInfo();

        /*
         * Retorno do módulo
         */
        return {
            info : function () {
                if ( userInfo.authenticated ) {
                    return userInfo;
                }
                return false;
            },
            /*
             * Método para recarregar as informações do usuário logado
             */
            reload : function () {
            	return loadUserInfo();
            }
        };

    })();

    /**
     * Objeto de controle do Facebook
     */
    sheer.fb = (function () {

        /**
         * Criando deferred para controle de carregamento
         */
        var readyDeferred 	= $.Deferred();
        var ready 			= false;
        var fbConfig 		= null;

        /**
         * Método para aplicar o módulo como disponível
         */
        function setReady () {
            ready = true;
            readyDeferred.resolve();
        }
        /**
         * Método para aplicar o módulo como disponível mas com o facebook disabilitado
         */
        function setReadyDisabled () {
            ready = true;
            readyDeferred.reject();
        }

        /**
         * Inicializador do Facebook
         */
        var inicializado = false;
        var init = (function () {

            if(inicializado) {return;}
            inicializado = true;

            var promiseFbConfig = sheer.dataProvider('social/facebook_config');

            //em caso de sucesso
            promiseFbConfig.done(function (data) {
                data = JSON.parse(data);
                //estando habilitado o facebook iremos chamar a funcao responsavel pela inicializacao
                if( data.enable ) {
                    inicializarFacebook(data);
                }
                else {
                    setReadyDisabled();
                }

            });

            //em caso de erro na busca das informações rejeito o fb
            promiseFbConfig.fail(function () {
                setReadyDisabled();
            });

        });

        /**
         * Método para iniciar o facebook
         */
        var inicializadoFb = false;
        var inicializarFacebook = function (fbAppConfig) {

            if(inicializadoFb) {return;}
            inicializadoFb = true;
            
            //Se o facebook não estiver configurado, não o executamos
            if( !fbAppConfig.enable ) {
            	setReadyDisabled();
            	return false;
            }
            
            //informando carregamento externo
            console.log('Carregando apiExterna facebook');
            
            //CARREGANDO BIBLIOTECA DO FACEBOOK
            require(['facebook'], function () {

                //definindo as configuracoes do facebook
                fbConfig = fbAppConfig;

                //iniciando o facebook
                FB.init({
                    appId      : fbAppConfig['appId'],
                    cookie     : true,  // enable cookies to allow the server to access
                    xfbml      : true,  // parse social plugins on this page
                    version    : 'v2.1' // use version 2.1
                });
                //setando o módulo como pronto
                setReady();
            });

        };
        init();

        return {
            /**
             * Método para retornar a promessa de carregamento do fb
             */
            getReadyPromise: function () {
                return readyDeferred.promise();
            },
            /**
             * Método para indicar que o módulo já carregou
             */
            isReady: function () {
                return ready;
            },
            /**
             * Método para indicar se o Facebook está disponível para o projeto
             */
            isAvailable: function () {
            	if( !fbConfig ) {
            		return false;
            	}
                return fbConfig.enable;
            }
        };

    })();
    
    sheer.gapi = (function () {
    	
    	/**
         * Criando deferred para controle de carregamento
         */
        var readyDeferred 	= $.Deferred();
        var ready 			= false;
        var googleConfig 	= null;

        /**
         * Método para aplicar o módulo como disponível
         */
        function setReady () {
            ready = true;
            readyDeferred.resolve();
        }
        /**
         * Método para aplicar o módulo como disponível mas com o facebook disabilitado
         */
        function setReadyDisabled () {
            ready = true;
            readyDeferred.reject();
        }

        /**
         * Inicializador do Facebook
         */
        var inicializado = false;
        var init = (function () {

            if(inicializado) {return;}
            inicializado = true;

            var promiseGapiConfig = sheer.dataProvider('social/google_config');

            //em caso de sucesso
            promiseGapiConfig.done(function (data) {

                data = JSON.parse(data);
                //estando habilitado o facebook iremos chamar a funcao responsavel pela inicializacao
                if( data.enable ) {
                    inicializarGapi(data);
                }
                else {
                    setReadyDisabled();
                }

            });

            //em caso de erro na busca das informações rejeito o fb
            promiseGapiConfig.fail(function () {
                setReadyDisabled();
            });

        });

        /**
         * Método para iniciar o facebook
         */
        var inicializadoGapi = false;
        var inicializarGapi = (function (gpaiAppConfig) {

            if(inicializadoGapi) {return;}
            inicializadoGapi = true;
            
            //definindo as configuracoes do facebook
            googleConfig = gpaiAppConfig;
            
            //informando carregamento externo
            console.log('Carregando apiExterna google');
            
            require(['google-api'], function () {
            	
            	//Tratando carregamento do google
            	var timeoutGStart = 10;
            	var startGoogleClient = function () {
            		if( typeof gapi != 'object' || typeof gapi.client != 'object'  ) {
            			timeoutGStart = timeoutGStart*2;
            			window.setTimeout(startGoogleClient, timeoutGStart);
            			return;
            		}
            		gapi.client.setApiKey(googleConfig['apiKey']);
	            	gapi.auth.getToken();
	                setReady();
            		
            		
            	}
            	window.setTimeout(startGoogleClient, timeoutGStart);
            });

        });
        init();
        
        return {
            /**
             * Método para retornar a promessa de carregamento do fb
             */
            getReadyPromise: function () {
                return readyDeferred.promise();
            },
            /**
             * Método para indicar que o módulo já carregou
             */
            isReady: function () {
                return ready;
            },
            /**
             * Método para indicar se o Facebook está disponível para o projeto
             */
            isAvailable: function () {
                return googleConfig.enable;
            },
            getConfig: function () {
            	return googleConfig
            }
        };
    	
    })();
    
    /**
     * Notify
     */
    sheer.notify = (function () {
    	/*
		 * 
		 * timeout [5000]
		 * 		Modo para decidir em quanto tempo ou o responsável por encerrar a notificação
		 * 		integer -> Tempo em milisegundos para ser encerrado
		 * 		object -> Objeto jQuery.Promise que irá encerrar no always
		 * 
		 * style {
		 * 		classes
		 * 			string -> Classes a serem aplicadas
		 * 		backgroundColor
		 * 			string -> Hex da Cor de plano de fundo a ser utilizada
		 * 		borderColor
		 * 			string -> Hex da Cor da borda
		 * 		color
		 * 			string -> Hex da cor do texto
		 * }
		 * 
		 * position {
		 * 		x [center]
		 * 			string left | center | right 
		 * 		y [top]
		 * 			string top | bottom 
		 * }
		 * 
		 * message 
		 * 		string -> Mensagem a ser exibida na notificação
		 */
		
		/*
		 * Propriedades default do Sheer.Notify
		 */
		var _defaults = {
			timeout: 5000,
			style: {
				classes : null,
				backgroundColor: null,
				borderColor: null,
				color: null
			},
			position: {
				x: 'center',
				y: 'top'
			},
			message: null
		};
		
		/*
		 * Controladores Sheer.Notify
		 */
		var instances = {};
		var holderNode = null;
		
		var inicializado = false;
		
		/**
		 * Método criador de um novo notify
		 * 
		 * @param unknown a
		 * 		string
		 * 			Será interpretada como a mensagem a ser exibida no notify
		 * 		object
		 * 			Objeto completo de configuração. Não iremos identificar parametros b e c
		 * 
		 * @param string b
		 * 		Será interpretado como as classes para ser inseridas na notificação
		 * 		Só será interpretado caso "a" seja string
		 * 		
		 */
		function notify ( a, b ) {
			
			//preparando configuracoes
			var settings = {};
			$.extend(true, settings, _defaults);
			
			try {
				//definindo o tipo de entrada utilizada
				if ( typeof a == 'string' ) {
					//message
					settings.message = a;
					//classes
					if( typeof b == 'string' && b.length > 0 ) {
						settings.style.classes = b;
					}
				}
				else if ( typeof a == 'object' ) {
					
					if( typeof a.message != 'string' ) {
						throw 'Notify configuration is invalid [message]';
					}
					
					//tratando posicao
					if( a.position != undefined ) {
						if ( typeof a.position != 'string' || a.length == 0 ) {
							throw 'Notify configuration is invalid [position]';
						}
						var tmp = a.position.split(' ');
						//x
						if( tmp[1] == undefined || typeof tmp[1] != 'string' ) {
							console.log('Notify position.x is invalid, assuming default.');
							tmp[1] = _defaults.position.x;
						}
						else  {
							tmp[1] = tmp[1].toLowerCase();
							if( tmp[1] != 'left' && tmp[1] != 'center' && tmp[1] != 'right' ) {
								console.log('Notify position.x is invalid, assuming default.');
								tmp[1] = _defaults.position.x;
							}
							settings.position.x = tmp[1];
						}
						//y
						if( tmp[0] == undefined || typeof tmp[0] != 'string' ) {
							console.log('Notify position.y is invalid, assuming default.');
							tmp[0] = 'top';
						}
						else  {
							tmp[0] = tmp[0].toLowerCase();
							if ( tmp[0] != 'top' && tmp[0] != 'bottom' ) {
								console.log('Notify position.y is invalid, assuming default.');
								tmp[0] = _defaults.position.y;
							}
						}
						settings.position.y = tmp[0];
						delete a.position;
						
						//verificando se a combinacao é bottom center
						if( settings.position.y == 'bottom' && settings.position.x == 'center' ) {
							console.log('Notify position bottom center is not implemented. Assuming top center');
							settings.position.y = _defaults.position.y;
						}
					}
					//tratando style
					if( a.style != undefined && typeof a.style == 'string' ) {
						a.style = {
							classes : a.style
						}
					}
					else if ( a.style != undefined && typeof a.style != 'object' ) {
						throw 'Notify configuration is invalid [style]';
					}
					//tratando timeout
					if ( typeof a.timeout == 'object' ) {
						if( typeof a.timeout.always != 'function' ) {
							console.log('Notify timeout is invalid. Assuming default.');
							a.timeout = _defaults.timeout;
						}
					}
					
	
					$.extend(true, settings, a);
					
				}
				else {
					throw 'Notify configuration is invalid [1]';
					return;
				}
				
				//criando notify
				return create(settings);
			}
			catch(err) {
				console.warn(err);
			}
		}
		
		/**
		 * Método de criação do notify a partir do objeto de configurações
		 */
		function create(settings) {
			
			//CRIAÇÃO DE DOM ELEMENT
			//criando objeto da notificacao
			var notification = document.createElement('div');
			var notificationId = getUniqueId();
			notification.setAttribute('id', notificationId);
			if( settings.style.classes ) {
				notification.setAttribute('class', settings.style.classes);
			}
	
			//criando template da notificacao
			var template = '<span class="sh-notify-close">x</span>';
				template += '<div class="sh-notify-content">'+settings.message+'</div>';
			notification.innerHTML = template;
			
			//CRIANDO A INSTANCIA DO NOTIFY
			var notify = (function () {
				
				//controles
				var idNotify = notificationId;
				var node = notification;
				/**
				 * Método responsável por encerrar o notify
				 */
				function close () {
					removeInstance(idNotify);
				}
				
				/*
				 * Mapeando a instancia
				 */
				var instance = {
					node : node,
					close: close,
					setContent: function ( html ) {
						node.querySelector('.sh-notify-content').innerHTML = html;
					}
				};
				
				//CONTROLE DE AUTOCLOSE
				//por tempo
				if( typeof settings.timeout == 'number' ) {
					
					var proximoFechamento = Date.now() + settings.timeout;
					
					var autoClose = function () {
						var dateNow = Date.now();
						//verificando se não devemos fechar
						if( dateNow <= proximoFechamento || node.classList.contains('sh-notify-keep') ) {
							proximoFechamento = dateNow + settings.timeout + 1
							window.setTimeout(autoClose, settings.timeout + 1);
						}
						else {
							close();
						}
					}
					autoClose();
					
				}
				else {
					settings.timeout.always(function () {
						close();
					});
				}
				
				return instance;
				
			})();
			
			//CONTROLES DE EVENTOS
			var jqNotificationNode = $(notification);
			//mousein mouseout
			jqNotificationNode.on('mouseenter', function (evt) {
				this.classList.add('sh-notify-keep');
			});
			jqNotificationNode.on('mouseleave', function (evt) {
				this.classList.remove('sh-notify-keep');
			});
			//closebutton
			jqNotificationNode.on('click', '.sh-notify-close', function () {
				notify.close();
			});
			
			//INSERINDO NO DOM
			var notifyHolderId = 'sh-notify-'+settings.position.y.substring(0,1)+settings.position.x.substring(0,1);
			var holder = holderNode.querySelector('#'+notifyHolderId);
			holder.appendChild(notification);
			
			//MAPEANDO A INSTANCIA
			instances[notificationId] = notify;
			
			return notify;
			
		}
		
		/**
		 * Método para remover instancia do notify
		 */
		function removeInstance ( idNotify ) {
			if(instances[idNotify]!= undefined){
				instances[idNotify].node.remove();
				delete instances[idNotify];
			}
		}
		
		/**
		 * Método de inicialização
		 */
		function init () {
			
			if( inicializado ) {return;}
			inicializado = true;
			
			$(document).ready(function () {
				//buscando para ver se já temos o sh-notify
				holderNode = document.getElementById('sh-notify');
				if( !holderNode ) {
					holderNode = document.createElement('div');
					holderNode.setAttribute('id', 'sh-notify');
					document.body.insertBefore(holderNode, document.body.firstElementChild);
				}
				holderNode.innerHTML = '<div id="sh-notify-tl"></div><div id="sh-notify-tc"></div><div id="sh-notify-tr"></div><div id="sh-notify-bl"></div><div id="sh-notify-br"></div>';
			});
		}
		
		init();
		
		//Criando objeto de plugin
		var notifyPlugin = {
			create: notify
		};
		
		//Inserindo controlador window.notify caso não exista
		if( typeof window.notify == 'undefined' ) {
			window.notify = notifyPlugin;
		}
		
		//Definindo o notify como modulo do require para não termos problemas
		define('sheer/notify', notifyPlugin);
		
		return notifyPlugin;
    })();
    
    
    /**
     * Components do Sheer
     * TODO - Refazer tudo isso aqui!
     */
    sheer.components = (function () {
    	
    	/**
		 * Método para buscar os conteudos relacionados ao componente
		 * Serão aceitos duas formas de busca de conteudos para um component
		 * 		-> Método padrão com sh-comp-content e sh-comp-contentNode 
		 * 			Determina o total de dados a serem capturados (content) de um nó (contentNode) que irá armazenar esses conteudos em checkbox[sh-check]
		 * 
		 * 		-> Método customizado por função sh-comp-contentFunction que deverá ser da forma padrão [requireModule][path]
		 * 
		 * @return
		 * 		Este método irá retornar um objeto da seguinte form
		 * 		{
		 * 			status: boolean -> Determina se os dados devem ser aceitos
		 * 			code: string -> Codigo de erro 
		 * 			message: string -> Mensagem descritiva caso ocorra algum erro
		 * 			data: array -> Objeto com os conteudos recuperados
		 * 			dataLength: 0|1|2
		 * 			promise: promise que irá retornar os conteudos
		 * 		}
		 */
		function getContents (node, configuration) {
			
			/*
			 * Determinando configurações customizadas
			 */
			var config = {
				contentLength : 'single',
				dataLength: 1
			};
			$.extend(true, config, configuration);
			
			//ContentFunction
			var contentFunction 	= node.getAttribute('sh-comp-contentFunction');
			//ContentLength
			var contentLength 		= config.contentLength;
			var contentLengthAttr 	= node.getAttribute('sh-comp-content');
			//ContentNode
			var contentNode 		= null;
			var contentNodeAttr 	= node.getAttribute('sh-comp-contentNode');
			
			//DEFININDO RETORNO PADRAO
			var contentResponse = {
				status: false,
				code: null,
				message: null,
				data: null,
				dataLength: config.dataLength,
				promise: null
			};
			
			try {
				//TODO DEVO REMOVER ESTA FUNÇÃO TRAZIDA POR REQUIRED E ASSUMIR UMA FUNÇÃO QUE SEJA PREVIAMENTE REGISTRADA NO SHEER.COMPONENT
				//DETERMINO QUAL O TRATAMENTO DE CONTEUDOS QUE DEVO ASSUMIR
				//assumindo funcao customizada
				if( contentFunction ) {
					
					console.warn('Este funcionamento será depreciado em breve. 14.12.02');
					
					//CASO EU REALMENTE POSSUA UM RESPONSE HANDLER IREI EXECUTA-LO
					//busco o nome do módulo e da função
					var tmp = contentFunction.split('][');
					var module = tmp[0].replace('[', '');
					var fn = tmp[1].replace(']', '');
					
					//CRIANDO DEFERRED PARA A AÇÃO
					var deferred = $.Deferred();
					
					//chamando o modulo pelo requirejs e executando a função desejada
					require([module], function (context) {
						var contents = executeFunctionByName(fn, context, this.node);
						deferred.resolve(contents);
					});
					
					//retorno o controlador com a promessa
					contentResponse.status = true;
					contentResponse.promise = deferred.promise();
					return contentResponse;
					
				}
				//assumindo tratamento padrao
				else {
					//determinando modelo de conteudo
					if( !!contentLengthAttr ) {
						switch(contentLengthAttr) {
							case 'single':
							case '1':
								contentLength = 'single';
								contentResponse.dataLength = 1;
								break;
							case 'multiple':
							case 'n':
								contentLength = 'multiple';
								contentResponse.dataLength = 2;
								break;
							case 'none':
							case '0':
								contentLength = 'none';
								contentResponse.dataLength = 0;
								break;
							default: 
								contentLength = config.contentLength;
								contentResponse.dataLength = config.dataLength;
								break;
						}
					}
					
					//DETERMINANDO O PROVEDOR DOS DADOS E DEFININDO CONTEUDOS
					var conteudos = [];
					
					//verificando se possuimos provedor de dados
					if( contentLength != 'none' && !!contentNodeAttr) {
						var tmp = $('#'+contentNodeAttr);
						if( tmp.length > 0 ) {
							contentNode = tmp[0];
						}
						if( !contentNode ) {
							throw {
								code: 3,
								message: "ContentNode não encontrado."
							};
						}
					}
					//caso de dado unico sem provedor de dados utilizamos o dado como o "data-id" mais proximo
					else if ( contentLength == 'single' ) {
						var closestDataId = $(node).closest('[data-id]');
						if( closestDataId.length == 1 ) {
							conteudos.push(closestDataId.attr('data-id'));
						}
						else {
							throw {
								code: 3,
								message: "Dado único não encontrado."
							};
						}
					}
					//outros casos
					else {
						console.warn('ContentNode não determinado, assumindo total de conteudos como 0');
						contentLength = 'none';
						contentResponse.dataLength = 0;
					}
					
					//buscando conteudos selecionados
					if( contentNode ) {
						var selecionados = $(contentNode).find('input[type="checkbox"][sh-check]:checked');
						if( contentLength == 'single' && selecionados.length != 1 ) {
							throw {
								code: 0,
								message: "Você deve selecionar somente um item."
							};
						}
						else if ( contentLength == 'multiple' && selecionados.length < 1 ) {
							throw {
								code: 1,
								message: "Você deve selecionar pelo menos um item."
							};
						}
						else {
							selecionados.each(function () {
								conteudos.push(this.value);
							});
						}
					}
					
					//gerando resposta
					contentResponse.status = true;
					contentResponse.data = conteudos;
	
					return contentResponse;
					
				}
				
			}
			catch (error) {
				contentResponse.code = error.code;
				contentResponse.message = error.message;
				return contentResponse;
			}
			
		}
    	
    	/*
    	 * Escuta principal para definição do component
    	 */
    	$(document).on('click', '[sh-component], [sh-comp]', function (evt) {
    		//Eliminando processo default
    		evt.preventDefault();
    		
    		var node = this;
    		
			//criando node.sheer caso nao exista
			if( !node.sheer ) {
				node.sheer = {
					id: getUniqueId(),
					locked: false
				};
			}
    		
			//Capturando o tipo do component
    		var componentType = this.getAttribute('sh-component') || this.getAttribute('sh-comp');
    		//Processando o component
    		switch(componentType.toLowerCase()) {
    			case 'overlaylink':
    				_overlayLink(node, evt);
    				break;
    			case 'navigation':
    				_navigation(node, evt);
    				break
    			case 'action':
    				_action(node, evt);
    				break
    		}
    	});
    	
    	/**
    	 * Component de Ação
    	 */
    	var _action = (function () {
    		
    		/**
			 * Método responsável por criar o pedido de confirmação da mensagem.
			 * Retorna uma promessa de resposta da confirmação
			 * @return jQuery.Promise 
			 */
			function requisitaConfirmacao ( node ) {
				
				var confirmMessage = node.getAttribute('sh-comp-confirmMessage') || 'Deseja realmente executar essa ação?';
				var buttonMessageConfirm = node.getAttribute('sh-comp-btnMessage-confirm') || 'SIM';
				var buttonMessageCancel = node.getAttribute('sh-comp-btnMessage-cancel') || 'NÃO';
				
				//CRIANDO DEFERRED QUE VAI DEFINIR O ENVIO OU CANCELAMENTO
				var deferred = $.Deferred();
				
				//html da tela de confirmação
				var html = '';
				html += '<section class="sh-box sh-box-laranja sh-w-600 sh-margin-x-auto">';
					html += '<header>';
						html += '<div><span data-icon="a"></span></div>';
						html += '<h1>CONFIRMAR AÇÃO</h1>';
					html += '</header>';
					
					html += '<div class="sh-box-content">';
					
						html += '<p><strong>'+confirmMessage+'</strong></p>';
					
						html += '<div class="sh-btn-holder">';
							html += '<button id="sh-comp-confirmNao" class="sh-btn-cinza-i">'+buttonMessageCancel+'</button>';
							html += '<button id="sh-comp-confirmSim" class="sh-btn-laranja-i">'+buttonMessageConfirm+'</button>';
						html += '</div>';
						
					html += '</div>';
					
				html += '</section>';
				
				//ABRINDO O COLORBOX DE CONFIRMACAO
				$.colorbox({
					html: html, 
					fixed: true,
					overlayClose: false,
					escKey: false,
					closeButton: false,
					onComplete: function () {
						
						var section = $('#cboxContent').find('section');
						var btnSim = section.find('#sh-comp-confirmSim');
						var btnNao = section.find('#sh-comp-confirmNao');
						
						//atribuindo o click no botão sim
						btnSim.on('click', function (evt){
							evt.preventDefault();
							deferred.resolve();
						});
						
						//atribuindo click no botão não
						btnNao.on('click', function(evt){
							evt.preventDefault();
							deferred.reject();
						});
					}
				});
				
				//ao finalizar fecho o colorbox
				deferred.always(function () {
					$.colorbox.close();
				});
				
				//retornando a promessa
				return deferred.promise();
				
			}
    		
    		/**
			 * Método para efetuar a operação após confirmação
			 */
			function efetuaActionPosConfirmacao (node) {
				
				//VOU BUSCAR OS CONTEUDOS DA NOVA FORMA E CORRETA
				var contents = getContents(node);
				var conteudos = [];
				
				//VERIFICANDO OS CONTEUDOS OBTIDOS
				if ( !contents.status ) {
					sheer.notify.create(contents.message, 'fail');
					return;
				}
				
				//CAPTURANDO CONTROLES
				//determinando target
				var targetUrl = node.getAttribute('sh-component-target') || node.getAttribute('href');
				
				//FAÇO TRATAMENTO DOS CONTEUDOS PARA EFETUAR A AÇÃO
				var postParameters = {};
				if( contents.dataLength == 1 ) {
					postParameters = {
						id: contents.data.pop()
					}
				}
				else if ( contents.dataLength == 2 ) {
					postParameters = {
						shId: contents.data
					}
				}
				
				//EFETUANDO A AÇÃO
				var promise = sheer.ajax(targetUrl, 'POST', postParameters, function () {
					node.sheer.locked = true;
				});
				
				//DETERMINANDO E EXECUTANDO PROCESSADOR DE RESPOSTA
				//tendo um responseHandler customizado
				var responseHandler = node.getAttribute('sh-component-responseHandler') || node.getAttribute('sh-comp-responseHandler') || node.getAttribute('sh-comp-rh'); 
				if( !!responseHandler ) {
					//FIXME passar a considerar somente responseHandlers registrados
					
					//CASO EU REALMENTE POSSUA UM RESPONSE HANDLER IREI EXECUTA-LO
					//busco o nome do módulo e da função
					var tmp = responseHandler.split('][');
					var module = tmp[0].replace('[', '');
					var fn = tmp[1].replace(']', '');
					
					//chamando o modulo pelo requirejs e executando a função desejada
					require([module], function (context) {
						executeFunctionByName(fn, context, promise, node);
					});
				}
				//nao tendo customizado utilizamos o padrao
				else {
					rhDefaultActionComponent(promise, node);
				}
				
				//Removendo o lock sempre
				promise.always(function () {
					node.sheer.locked = false;
				});
				
			}
			
			/**
			 * ResponseHandler padrão para o component de ação
			 */
			function rhDefaultActionComponent (promise, node) {
				
				//EXIBINDO O LOADER PADRÃO
				//caso venha de um navigation vamos inserir o gif loading nele
				if( $(node).closest('.sheer-nav-inline').length == 1 ) {
					var listElementNode = $(node).closest('li');
					if( listElementNode.length == 1 ) {
						listElementNode[0].classList.toggle('sh-loading', true);
					}
					promise.always(function () {
						if( listElementNode.length == 1 ) {
							listElementNode[0].classList.toggle('sh-loading', false);
						}
					});
				}
				//caso venha de um local qualquer TODO devo implementar o notify informando do processamento?
				else {
					sheer.notify.create({
						message: 'Processando...',
						timeout: promise
					});
				}
				
				//processando sucesso
				promise.done(function (data) {
					data = JSON.parse(data);
					//sucesso
					if( data.status ) {
						sheer.pages.reload();
					}
					else {
						sheer.notify.create(data.message,'fail');
					}
				});
				
				//unlock element
				promise.always(function () {
					node.sheer.locked = false;
				});
				//Falha no envio da requisição
				promise.fail(function (data) {
					alert('Erro de comunicação com o servidor. Tente novamente mais tarde.');
				});
			}
    		
    		
    		/*
    		 * Função de execução do componnet
    		 */
    		function executar(node, event) {
    			
    			//Verificando se component esta lockado
    			if( node.sheer.locked ) {
    				console.warn('Componente está lockado');
    				return false;
    			}
    			
				//PROCESSANDO PEDIDO DE CONFIRMAÇÃO
				var confirm = node.getAttribute('sh-comp-confirm');
				var confirmPromise = null;
				//caso tenha confirmação necessaria requisito a mesma
				if( confirm != null ) {
					confirmPromise = requisitaConfirmacao(node);
				}
				//caso não seja necessária simulo uma promessa previamente resolvida
				else {
					confirmPromise = $.Deferred();
					confirmPromise.resolve();
					confirmPromise = confirmPromise.promise();
				}
				
				//caso a confirmação tenha sido aceita
				confirmPromise.done(function () {
					efetuaActionPosConfirmacao(node);
				});
    			
	    	}
    		
    		return executar;
    		
    	})();
    	
    	/**
    	 * Component Navigation
    	 */
    	var _navigation = (function () {
    		
    		/*
    		 * Função para fechar os navegadores abertos
    		 */
    		function fechaNavegadores () {
				$(document.body).children('.sh-comp-nav').remove();
			}
    		
    		/*
    		 * Método de parser do html do template.
    		 * Este irá substituir a ocorrencia de 
    		 * 		{id} => data-id
    		 * TODO
    		 * 		{x}
    		 */
			function parseNavigationHtml (node, navHtml) {
				
				//vamos buscar as informações daquele component
				var contentId = node.getAttribute('data-id') || $(node).closest('[data-id]').attr('data-id');
				if( contentId === undefined || contentId === null ) {
					contentId = '';
				}
				navHtml = navHtml.replace(/{id}/g, contentId);
				return navHtml;
			};
			
			/*
			 * Método para calcular o posicionamento dos navegadores
			 * TODO implementar o position a partir do sh-comp-position
			 */
			function reposition ( nodeComponent, nodeNavigation, jsEvent ) {
				
				var xBegin, xEnd, yBegin, yEnd = 0;
				
				//PRIMEIRO TENTO POSICIONAR BOTTOM RIGHT
				var xMin = parseInt($(nodeComponent).offset().left);
				var xMax = xMin + parseInt($(nodeComponent).outerWidth());
				var yMin = parseInt($(nodeComponent).offset().top);
				var yMax = yMin + parseInt($(nodeComponent).outerHeight());
				
				//X
				//RIGHT 0 em relacao ao component - crescendo para a esquerda
				var leftInicial = xMax - parseInt($(nodeNavigation).outerWidth());
				if( leftInicial > 0 ) {
					nodeNavigation.style.left = leftInicial+'px';
				}
				//LEFT 0 em relacao ao component - crescendo para a direita
				else {
					nodeNavigation.style.left = xMin+'px';
				}
				
				//Y
				//BOTTOM 0
				var topFinal = yMax + parseInt($(nodeNavigation).outerHeight());
				var topTelaFinal = window.pageYOffset + $(document.body).outerHeight();
				if( topFinal <= topTelaFinal ) {
					nodeNavigation.style.top = yMax+'px';
				}
				else {
					nodeNavigation.style.top = (yMin-parseInt($(nodeNavigation).outerHeight()))+'px';
				}
			}
			
			//AO CLICAR EM QUALQUER LUGAR FECHAMOS O OS MENUS DE CONTEUDO
			$(document).on('click', function (event) {
				//verificando se não veio da abertura de um navigation
				if( event.sheerNavigation ) {
					return false;
				}
				
				var elementoClicado = event.explicitOriginalTarget;
				if( $(elementoClicado).closest('.sh-comp-nav').length == 0 ) {
					fechaNavegadores();
				}
			});
    		
    		/*
    		 * Função de execução do componnet
    		 */
    		function executar(node, event) {
    			
				//Marcando que o evento foi gerado a partir da abertura de um navigation
    			event.sheerNavigation = true;
				//fechando outros navegadores abertos
				fechaNavegadores();
				
				//buscando template
				var templateId = node.getAttribute('sh-component-target') || node.getAttribute('href');
				var templateNode = document.getElementById(templateId);
				
				//Verificando se o template node é nulo ou undefined
				if(!templateNode || templateNode === undefined){
					console.warn('Não foi possível localizar o template de navegação');
					return false;
				}
				
				var templateHtml = templateNode.innerHTML;
				 
				//verificando se é do tipo template
				if( templateNode.nodeName.toLowerCase() != 'template' ) {
					console.warn('Navigation não aponta para template válido');
					return false;
				}
				
				//efetuando parser no html do menu
				templateHtml = parseNavigationHtml(node, templateHtml);
				
				//criando div para envolver e processarmos string em html
				var divHolder = document.createElement('div');
				divHolder.innerHTML = templateHtml;
				
				//INSERINDO INFORMAÇÕES DE ORIGEM
				divHolder.setAttribute('sh-component-id', node.sheer.id);
				divHolder.classList.add('sh-comp-nav');
				divHolder.style.position = 'absolute';
				divHolder.style.zIndex = 65000;
				
				//inserindo dentro do DOM da página
				document.body.appendChild(divHolder);
				
				//reposicionando elemento de navegacao
				reposition(node, divHolder, event);
    			
	    	}
    		
    		return executar;
    	})();
    	
    	/**
    	 * Component OverlayLink
    	 * Este component irá abrir um overlay com a url desejada
    	 */
    	var _overlayLink = (function () {
    		
    		/*
    		 * Função de execução do componnet
    		 */
    		function executar(node, event) {
    			
    			//verificando se existe o atributo para modificar o fixed do colorbox
    			var attr = node.getAttribute('sh-comp-cboxfixed') || node.getAttribute('sh-comp-cboxFixed');
    			//se existir e for false ou 0
    			var fixed = true;
    			if (typeof(attr) !== 'undefined' && (attr === 'false' || attr === '0')) {
    				fixed = false;
    			}
    		
	    		//Capturando conteudos
				var contents = getContents(node, {contentLength: 'none', dataLength:0});
				
				//verificando erro de captura de conteudos
				if( !contents.status ) {
					sheer.notify.create(contents.message, 'fail');
					return;
				}
				
				//buscando url de target
				var targetUrl = node.getAttribute('sh-component-target') || node.getAttribute('href');
				
				require(['colorbox'], function () {
					$.colorbox({
						href: targetUrl + '&htmlResponse=1',
						fixed: fixed,
						data: {
							shId: contents.data
						}
					});
				});
	    	}
    		
    		return executar;
    		
    	})();
    	
    	
    })();
    
    /**
     * Sheer/Route
     * 
     * Controle de rotas do Sheer
     * 
     * @var {}
     * 		$defaults {}
     * 		processInitialRoute ()
     * 			Método para fazer com que o sheer.route processe a rota inicial logo apos o document.ready
     * 		addRoute (route, options, handler)
     * 			Método para inserir uma rota como conhecida no sheer
     * 		goRoute (route)
     * 			Método para forçar o sheer a carregar uma rota específica
     *		getCurrentRoute
     *			Método para recuperar a rota corrente
     */
    sheer.route = (function () {
    	
    	/*
    	 * Objeto geral controlador
    	 */
		var shRoute = {};
		/*
		 * Marcador de rota corrente
		 */
		var currentRoute = null;
		/*
		 * Mapa de rotas executadas
		 */
		var executedRoutes = {};
		/*
		 * Criando opções default para uma rota
		 */
		var $defaults = {
			timeout: 10000,
			initRoute: null,
			initDOMElements: null,
			holder: null
		};
		/*
		 * Objeto de routes
		 * route => [route, options, handler]
		 */
		var $routes = [];
		var $routeDefault = null;
		
		/*
		 * Método para setar o holder de forma padrão para utilização de todos os renderables que utilizarão holder
		 */
		function setDefaultHolder ( selector ) {
			$defaults.holder = selector;
		}
		
		/*
		 * Método para setarmos um loader default para ocorrer enquanto estamos trocando de rotas.
		 * @param string selector
		 * 		Seletor do html default que deverá ser inserido dentro do holder enquanto buscamos o html da nova página
		 */
		var jqDefaultLoader = null;
		function setDefaultLoader ( selector ) {
			
			$(document).ready(function () {
				//Verificando a existencia do loader
				var tmpLoader = $(selector);
				if( tmpLoader.length != 1 ) {
					return;
				}
				//Inserindo o loader no objeto controlador
				jqDefaultLoader = tmpLoader;
			});
			
		}
		
		function conversorRotaIndex ( routeUrl ) {
			
			if( routeUrl == '.' ) {
				routeUrl = '';
			}
			return routeUrl;
			
		}
		
		/*
		 * Rota default do sheer para processamento de páginas 
		 * 
		 * Este considera a opção "initDOMElements" e também o defaultLoader para substituição do html do holder
		 */
		function shPageHandler (deferred, url, state, route) {
			
			//Definindo variavel de controle para troca do conteudo do holder
			var loaderStarted 	= false;
			var oldContent 		= null;
			
			//Para utilizarmos esse handler precisamos de um holder definido nas configurações da rota
			var jqHolder = null;
			var holderToLoad = false;
			
			try {
				
				//verificando se está indefinido
				if( route['options']['holder'].length == 0 ) {
					//Caso não exista holder padrão
					if( !$defaults.holder ) {
						throw {
							code: null,
							message: 'Não encontramos um holder válido para esta rota.'
						}
					}
					holderToLoad = $defaults.holder;
				}
				//Tendo uma definicao iremos chamar o jQuery com aquele valor para buscar um nó no DOM
				else {
					holderToLoad = route['options']['holder'];
				}
				//Carregando holder
				jqHolder = $(holderToLoad);
				if( jqHolder.length != 1 ) {
					throw {
						code: null,
						message: 'Não encontramos um holder válido para esta rota.'
					}
				}
				
				//BUSCAR A PÁGINA PELO SHEER USANDO O PH
				//criando as configurações para a url de busca
				var paramPage = '';
				var paramGet = '';
				//Preciso verificar se possuimos o delimitador get e processar os parametros separadamente
				var delimIndex = url.indexOf('?');
				if( delimIndex > -1 ) {
					paramPage = url.substring(0, delimIndex);
					paramGet = url.substring(delimIndex+1);
				}
				else {
					paramPage = url;
				}
				
				//Determinando a query string da url
				var queryString = '';
				if( paramPage.length > 0 && paramPage!='.' ) {
					queryString += '?p='+paramPage;
				}
				if( paramGet.length > 0 ) {
					if( queryString.length > 0 ) { queryString += '&' }
					else { queryString += '?'; }
					queryString += paramGet;
				}
				//Determinando url final
				$urlFinal = 'ph.php'+queryString;
				
				//Efetuando requisição para buscar do sheer
				var pagePromise = sheer.ajax($urlFinal, 'GET', undefined, function () {
					//Caso não tenhamos um loader padrão, iremos pular este processo
					if( !jqDefaultLoader ) {
						return;
					}
					//marcando o inicio do loader
					loaderStarted = true;
					
					//Gravando e removendo conteudo anterior
					oldContent = jqHolder.children();
					oldContent.detach();
					
					//Inserindo o loader padrão no holder
					jqHolder.html(jqDefaultLoader.html());
					
				});

				//Tendo sucesso na busca da página
				pagePromise.done(function (data) {
					
					data = JSON.parse(data);
					
					if( data.status ) {
						//Inserindo html na página
						var htmlFinal = '';
						for ( var i in data['holders'] ) {
							htmlFinal += data['holders'][i];
						}
						jqHolder.html(htmlFinal);
						
						//Rodando o initDOMElements quando os objetos já tiverem terminados de serem inseridos
						if( typeof route['options'] == 'object' ) {
							//Inicializando DOM Elements
							if( typeof route['options'].initDOMElements == 'function' ) {
								route['options'].initDOMElements();
							}
						}
						
						//Definindo titulo da pagina
						var title = url;
						if( data.title && typeof data.title == 'string' ) {
							title = data.title;
						}
						
						//Finalizando a execução do route
						deferred.resolve({
							title: title
						});
					}
					else {
						throw {
							code: null,
							message: 'Falha ao carregar os dados da rota'
						}
					}
				});
				
				//Tendo falha na busca da página
				pagePromise.fail(function () {
					throw {
						code: null,
						message: 'Erro ao tentar comunicação com o servidor. Tente novamente mais tarde'
					}
				});
				
			}
			catch (error) {
				
				//Verificando se o loader substituiu o html atual
				if ( loaderStarted ) {
					//Inserindo o loader padrão no holder
					jqHolder.html('');
					jqHolder.append(oldContent);
				}
				
				//Setando o deferred como falha
				deferred.reject(error);
				return false;
			}
			
		}
		
		/*
		 * Inserindo listeners de cliques
		 */
		//COLOCANDO O EVENTO DE CLIQUE EM QUALQUER "[SH-ROUTE]"
		$(document).on('click', '[sh-route]', function (evt) {
			evt.preventDefault();
			
			//Definindo o caminho da rota
			var route = this.getAttribute('sh-route') || this.getAttribute('href');
			if( !route || route.length == 0 ) { route = ''; }
			
			//Processando rota
			goRoute(route);
		});
		
		//ESCUTANDO MUDANÇA DE ESTADO NO BROWSER
		$(window).on('popstate', function (evt, state) {
			evt.preventDefault();
			
			//Capturando route padrão
			var state = history.state;
			if( !state || !state['route'] ) {
				//Vou para a rota ''
				goRoute('');
				return false;
			}
			//Executando novamente o route
			returnRoute(state['route'], state);
		});
		
		/**
		 * Método para executar uma nova rota, este irá inserir um novo state no browser
		 */
		function goRoute(route) {
			return executeRoute(route, true);
		}
		
		/*
		 * Método generico para executar uma rota sendo voltando o state ou avançando
		 * Caso deseje que insira um novo state no browser basta passar o parametro createState = true
		 */
		function executeRoute (url, createState, state) {
			
			//Processando o createState convertendo em boolean
			createState = (createState!=undefined && !!createState);
			
			//DETERMINANDO OBJETO ROUTE A SER UTILIZADO
			//Verificando e determinando simpleUrl
			var simpleUrl = url.split('?');
			
			//Processando que certas rotas deverão virar a rota ''.
			simpleUrl[0] = conversorRotaIndex(simpleUrl[0]);
			
			//iterando por todas as rotas para achar uma valida
			//Nesta somente comparo texto simples, sem utilização de regexp
			var finalRoute = null;
			for( var i in $routes ) {
				var rt = $routes[i];
				//Verificando string diretamente
				if(rt['route'] == simpleUrl[0]) {
					finalRoute = rt;
					break;
				}
			}
			//Se não encontrei nenhum rota adequada vou busca-las novamente considerando expressão regular
			if( !finalRoute ) {
				//foreach para as rotas considerando regexp
				for( var i in $routes ) {
					var rt = $routes[i];
					var regExp = new RegExp(rt['route']);
					var matched = regExp.test(simpleUrl[0]);
					if( matched ) {
						finalRoute = rt;
						break;
					}
				}
			}
			//Se ainda não encontrei nenhuma rota, vou assumir a default
			if( !finalRoute ) {
				finalRoute = $routeDefault;
			}
			//Finalizando a busca da rota
			route = finalRoute;
			
			//CRIAR CONTROLADOR DE ROTAS EXECUTADAS
			//Processando a url
			var urlProcessada = url;
			if( url.length == 0 || url == '.' ) { urlProcessada = 'shHomeRoute'; }
			//Criando controlador
			if( typeof executedRoutes[urlProcessada] == 'undefined' ) {
				executedRoutes[urlProcessada] = {
					times: 0,
					dom: null
				}
			}
			
			/*
			 * Primeiramente crio um novo state para o browser
			 * Este novo statement será substituido após o route terminar sua execução
			 * 
			 * Somente faço isso se for um novo state
			 * 
			 */
			//Definindo a url para o state
			var stateUrl = url;
			if(url.length == 0) {
				stateUrl = '.';
			}
			if ( createState ) {
				history.pushState({
					route: null,
					html: null,
					title: null
				}, undefined, stateUrl);
			}
			
			//EXECUTAR A FUNÇÃO HANDLER DO ROUTE
			//Crio a promessa para enviar para o route informar para nós quando ele se encerrou
			var routeDeferred = $.Deferred();
			
			//Executar o handler do route
			route['handler'](routeDeferred, url, state, route);
			
			/*
			 * Criando controlador de timeout
			 */
			var routeTimeoutId = window.setTimeout(function () {
				routeDeferred.reject({
					code: null,
					message: 'A rota "'+url+'" não pode ser carregado devido a timeout.'
				});
			}, route['options']['timeout']);
			
			/*
			 * Quando a rota finalizar com sucesso
			 *  	-> Alterar o título da página
			 *  	-> Alterar o state do browser
			 */
			routeDeferred.done(function (routeInfo) {
				//Limpo o timeout para o route setado anteriormente
				window.clearTimeout(routeTimeoutId);
				
				//Substituindo o titulo caso esteja setado
				if( typeof routeInfo == 'object' && routeInfo['title'] && typeof routeInfo['title'] == 'string' && routeInfo['title'].length > 3 ) {
					document.title = routeInfo['title'];
				}
				
				//Substituo o state atual para receber as informações corretas
				if ( createState ) {
					//Alterando o state atual
					history.replaceState($.extend({}, true, {route:url}, routeInfo), undefined, stateUrl);
				}
				
				//SENDO A PRIMEIRA EXECUÇÃO DA ROTA
				if( executedRoutes[urlProcessada].times == 0 ) {
					//Executando as funcoes de inicializacao 
					if( typeof route['options'] == 'object' ) {
						//Inicializando rota
						if( typeof route['options'].initRoute == 'function' ) {
							route['options'].initRoute();
						}
					}
				}
				
				//Marcando a rota como executada mais uma vez
				executedRoutes[urlProcessada].times++;
				
				//Modificando rota corrente
				currentRoute = url;
			});
			
			/*
			 * Quando a rota finalizar com falha,
			 * A mais comum delas será o timeout
			 */
			routeDeferred.fail(function (error) {
				window.clearTimeout(routeTimeoutId);
				//logo o erro
				console.error(error.message);
			});
			
			return routeDeferred.promise();
			
		}
		
		/**
		 * Método responsáel por retornar uma rota no browser
		 * Este irá chamar o handler da rota mas não gerará o pushState
		 */
		function returnRoute (route, state) {
			executeRoute(route, false, state);
		}
		
		/**
		 * Método para obter a rota corrente
		 */
		function getCurrentRoute () {
			return currentRoute;
		}
		
		/**
		 * Método que permite adicionar rotas ao controlador do sheer
		 * 
		 * @param route 
		 * 			Nome da rota a ser registrada
		 * @param options 
		 * 			Configurações especiais da rota
		 * @param handler
		 * 			Método que define a rota e efetua os processamentos necessário
		 * 
		 * TODO ACEITAR DEFINIR UM HANDLER PARA VÁRIOS ROUTES, Receber array de routes
		 * 
		 */
		function addRoute(route, options, handler) {
			
			//PROCESSANDO O PARAMETRO "ROUTE"
			//SE O PRIMEIRO PARAMETRO FOR FUNÇÃO, ENTENDEMOS QUE O PRIMEIRO PARAMETRO JÁ É LOGO O HANDLER
			if( typeof route == 'function' ) {
				handler = route;
				options = {};
				route = '';
			}
			//Se foi passado um objeto para o route, vamos entender que toda a configuração foi realizada por somente um parametro
			else if ( typeof route == 'object' ) {
				route = route.route;
				options = route.options;
				handler = route.handler;
			}
			//Se o primeiro parametro não for string iremos encerrar a execução com erro
			else if ( typeof route != 'string' ) {
				console.warn('Router configurado de forma inválida');
				return;
			}
			
			//PROCESSANDO "OPTIONS"
			//usuário envia opções customizadas, não faço nada pois lá na frente mesclo
			else if( typeof options == 'object' ) {
				options = options;
			}
			//caso seja uma função, aceito como handler e defino os options para {}
			else if ( typeof options == 'function' ) {
				handler = options;
				options = {};
			}
			
			//Finalizando as options da route
			options = $.extend({}, true, $defaults, options);
			
			//PROCESSANDO HANDLER
			//O Handler que temos agora não é uma função, devemos assumir o handler default
			if ( typeof handler != 'function' ) {
				handler = shPageHandler;
				//Preciso verificar que foi passado um holder correto dentro dos configs, pois o default só funciona com um handler bem definido
				if( typeof options['holder'] == undefined || typeof options['holder'] == null ) {
					console.error('Router configurado de forma inválida');
					return;
				}
			}
			
			//Criando objeto da rota
			var routeObject = {
				route: route,
				options: options,
				handler: handler
			};
			
			//Determinando se é rota default
			if( route ) {
				//Definindo a rota
				$routes.push(routeObject);
			}
			else {
				$routeDefault = routeObject;
			}
			
		}
		
		/*
		 * Verificar e processar rota atual
		 * Utilizamos esta função para processar a rota inicial assim que uma página carrega
		 */
		function processInitialRoute () {
			/*
			 * Ao ter o DOM totalmente carregado eu devo verificar se nos foi passado alguma rota especial
			 */
			$(document).ready(function () {
				
				var baseUrl = document.getElementsByTagName('base');
				//Não consegui encontrar o base url
				if( baseUrl.length == 0 ) {
					return;
				}
				baseUrl = baseUrl.item(0).getAttribute('href');
				//Não consegui buscar o href do base url
				if( baseUrl.length < 4 ) {
					return;
				}
				//Determinando a rota
				var currentLocation = window.location.href;
				var url = currentLocation.replace(baseUrl, '');
				
				
				/*
				 * INICIALIZAÇÃO ROTA SEM CARREGA-LA
				 */
				
				//DETERMINANDO OBJETO ROUTE A SER UTILIZADO
				//Verificando e determinando simpleUrl
				var simpleUrl = url.split('?');
				
				//iterando por todas as rotas para achar uma valida
				//Nesta somente comparo texto simples, sem utilização de regexp
				var finalRoute = null;
				for( var i in $routes ) {
					var rt = $routes[i];
					//Verificando string diretamente
					if(rt['route'] == url || rt['route'] == simpleUrl[0]) {
						finalRoute = rt;
						break;
					}
				}
				//Se não encontrei nenhum rota adequada vou busca-las novamente considerando expressão regular
				if( !finalRoute ) {
					//foreach para as rotas considerando regexp
					for( var i in $routes ) {
						var rt = $routes[i];
						var regExp = new RegExp(rt['route']);
						var matched = regExp.test(url);
						if( matched ) {
							finalRoute = rt;
							break;
						}
					}
				}
				//Se ainda não encontrei nenhuma rota, vou assumir a default
				if( !finalRoute ) {
					finalRoute = $routeDefault;
				}
				//Finalizando a busca da rota
				route = finalRoute;
				
				//CRIAR CONTROLADOR DE ROTAS EXECUTADAS
				//Processando a url
				var urlProcessada = url;
				if( url.length == 0 || url == '.' ) { urlProcessada = 'shHomeRoute'; }
				//Criando controlador
				if( typeof executedRoutes[urlProcessada] == 'undefined' ) {
					executedRoutes[urlProcessada] = {
						times: 0,
						dom: null
					}
				}
				
				//SENDO A PRIMEIRA EXECUÇÃO DA ROTA
				if( executedRoutes[urlProcessada].times == 0 ) {
					//Executando as funcoes de inicializacao 
					if( typeof route['options'] == 'object' ) {
						//Inicializando rota
						if( typeof route['options'].initRoute == 'function' ) {
							route['options'].initRoute();
						}
					}
					//Rodando o initDOMElements quando os objetos já tiverem terminados de serem inseridos
					if( typeof route['options'] == 'object' ) {
						//Inicializando DOM Elements
						if( typeof route['options'].initDOMElements == 'function' ) {
							route['options'].initDOMElements();
						}
					}
				}
				
				//Marcando a rota como executada mais uma vez
				executedRoutes[urlProcessada].times++;
				
				//Modificando rota corrente
				currentRoute = url;
				
			});
		}
		
		/*
		 * Finalizando
		 */
		shRoute.$defaults = $defaults;
		shRoute.processInitialRoute = processInitialRoute;
		//Método para inserirmos o mapeamento de mais uma rota
		shRoute.addRoute = addRoute;
		//Método para irmos para uma rota na mão
		shRoute.goRoute = goRoute;
		//Método para capturar a rota corrente
		shRoute.getCurrentRoute = getCurrentRoute;
		//Setando método para definição de holder padrão
		shRoute.setDefaultHolder = setDefaultHolder;
		//Setando método para definição de loader padrão
		shRoute.setDefaultLoader = setDefaultLoader;
		
		return shRoute;

    })();
    
    /*
     * AJUSTE DE COMPATIBILIDADE COM MODULOS SHEER ANTIGO
     */
    sheer.lib = {
		/*
		 * Vamos pegar o microtime
		 */
		microtime: microtime,
		
		/*
		 * Método para remover os acentos de uma string
		 */
		removeAcentos: removeAcentos,
		
		/*
		 * Vamos gerar um identificador unico
		 */
		getUniqueId: getUniqueId,
		
		
		html: {
			
			/*
			 * Método para criar options a partir de um array json
			 */
			optionsFromJson : function (data, keyName, valueName, blankOption) {
				
				var html = '';
				if( !!blankOption ) {
					html += '<option value="">'+blankOption+'</option>';
				}
				
				if(data) {
					for( var i in data ) {
						html += '<option id="'+data[i][keyName]+'" value="'+data[i][keyName]+'">'+data[i][valueName]+'</option>';
					}
				}
				
				return html;
			}
			
		}
	};
    
    /*
	 * Método seguro para o Sheer importar módulos configurados nas páginas
	 * Este irá aceitar um array de módulo e eles serão inicializados automaticamente
	 * 	Caso o módulo já esteja definido e tenha sua função init existente, iremos executa-la
	 * 	Caso ele só tenha sido registrado, mas não definido, iremos defini-lo, consideramos o curred
	 * 
	 * @Deprecated Não devemos mais utilizar o sheer para carregar dependencias de JS e utilizar o requireJS
	 */
	sheer.import = (function () {
		
		function loadModule ( modules ) {
			
			if( typeof modules == 'string' ) {
				modules = [modules];
			}
			
			//incluindo o módulo
			require(modules, function (  ) {
				
				//Iniciando cada um dos módulos puxados
				for(var i in modules) {
					//capturando a definicao do modulo
					var definedModule = require.s.contexts._.defined[ modules[i] ];
					
					if( definedModule != undefined && isFunction(definedModule.init) ) {
						definedModule.init();
					}
					
				}
				
				//vendo se o "currentPage" foi definido e inicializamos caso sim
				if( require.s.contexts._.registry['currentPage'] ) {
					require(['currentPage'], function (currentPage) {
						if( currentPage != undefined && isFunction(currentPage.init) ) {
							currentPage.init();
						}
					});
				}
				
			});
		}
		
		return loadModule;
	})();
    
    return sheer;

});


/*
 * Funções gerais
 */
/*
 * PROTOTYPES DEFINIDOS PELO SHEER
 */
String.prototype.lpad = function(padString, length) {
	var str = this;
	while (str.length < length) {
		str = padString + str;
	}
	return str;
}

Number.prototype.formatMoney = function(decPlaces, thouSeparator, decSeparator) {
	var n = this.toString(), decPlaces, decSeparator, thouSeparator, sign, i, j;

	decPlaces = isNaN(decPlaces = Math.abs(decPlaces)) ? 2 : decPlaces;
	decSeparator = decSeparator == undefined ? "." : decSeparator;
	thouSeparator = thouSeparator == undefined ? "," : thouSeparator;
	sign = n < 0 ? "-" : "";
	i = parseInt(n = Math.abs(+n || 0).toFixed(decPlaces)) + "";
	j = (j = i.length) > 3 ? j % 3 : 0;
	return sign + (j ? i.substr(0, j) + thouSeparator : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thouSeparator) + (decPlaces ? decSeparator + Math.abs(n - i).toFixed(decPlaces).slice(2) : "");
}


/*
 * Verificar se objeto é number
 */
function isNumber(n) {
	return !isNaN(parseFloat(n)) && isFinite(n);
}
/*
 * Verificar se parametro é funcao
 */
function isFunction(functionToCheck) {
	var getType = {};
	return functionToCheck && getType.toString.call(functionToCheck) == '[object Function]';
};

/*
 * Método para recuperar o valor de um parametro da URL
 */
function getUrlParameters(url) {
	if(url === undefined) { url = window.location.href; }
	urlParameters = [];
	url.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) { urlParameters[key] = value; });
	return urlParameters;
}

/*
 * Método para Gerar Identificador unico
 */
function getUniqueId() {
	var unique = microtime(false).substring(2,5).toString() + Math.floor((Math.random()*100)+1).toString();
	unique = unique.replace(/\s/g, '');
	return unique.replace(' ', '');
}
/*
 * Método para pegar um parametro na URL
 */
function getUrlParam(paramName) {
	var queryString = window.location.search;
	queryString = queryString.substring(1,queryString.length);
	queryString = queryString.split('&');
	var data = [], temp;
	for (var i in queryString) {
		temp = queryString[i].split('=');
		if(paramName == temp[0]) {
			return temp[1];
		}
	}
	return null;
}


/*
 * Capturando o microtime atual
 */
function microtime (get_as_float) {
	var now = new Date().getTime() / 1000;
	var s = parseInt(now, 10);
	return (get_as_float) ? now : (Math.round((now - s) * 1000) / 1000) + ' ' + s;
}

/*
 * Método para executar uma função a partir de uma string
 */
function executeFunctionByName(functionName, context) {
	//determinando contexto
	if( context == undefined ) {
		context = window;
	}

	var args = [].slice.call(arguments).splice(2);
	var namespaces = functionName.split(".");
	var func = namespaces.pop();
	for(var i = 0; i < namespaces.length; i++) {
		//verificando contexto invalido
		if( context[namespaces[i]] === undefined ) {
			return undefined;
		}
		context = context[namespaces[i]];
	}
	//verificando funcao invalida
	if( context[func] === undefined ) {
		return undefined;
	}
	return context[func].apply(this, args);
}

/*
 * Método para buscar uma variabel pelo nome
 */
function getVariableByName(functionName, context) {
	//determinando contexto
	if( context == undefined ) {
		context = window;
	}

	var args = [].slice.call(arguments).splice(2);
	var namespaces = functionName.split(".");
	var func = namespaces.pop();
	for(var i = 0; i < namespaces.length; i++) {
		//verificando contexto invalido
		if( context[namespaces[i]] === undefined ) {
			return undefined;
		}
		context = context[namespaces[i]];
	}
	return context[func];
}

function removeAcentos (palavra) {
	com_acento = 'áàãâäéèêëíìîïóòõôöúùûüçÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÖÔÚÙÛÜÇ';
	sem_acento = 'aaaaaeeeeiiiiooooouuuucAAAAAEEEEIIIIOOOOOUUUUC'; 
	nova='';
	for(var i=0; i<palavra.length; i++) {
		if (com_acento.indexOf(palavra.substr(i,1))>=0) {
			nova += sem_acento.substr(com_acento.indexOf( palavra.substr(i,1) ), 1 );
		}
		else {
			nova+=palavra.substr(i,1);
		}
	}
	return nova;
}