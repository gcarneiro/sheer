define('sheer/validation', ['sheer'], function ( sheer ) {
	
	/*
	 * Objeto de controle
	 */
	var shValidation = {};
	
	/*
	 * Objeto de controle dos validadores registrados
	 */
	shValidation.validadores = (function () {
		
		//Objeto de controle
		var self = {};
		
		//Variavel de registro dos validadores
		var validadores = {};
		
		/**
		 * Método público para registrar um novo validador
		 * 
		 * @param id identificador do validador
		 * @param fn funcao responsavel por validar o dado
		 */
		self.push = function (id, fn) {
			
			//verificando 
			if( typeof id != 'string' || typeof fn != 'function' ) {
				throw 'Validador inválido';
			}
			
			//verificando se já possuimos um validador com esse id
			if( typeof validadores[id] !== 'undefined' ) {
				throw 'Validador "'+id+'" já definido';
			}
			
			//Setando validador
			validadores[id] = fn;
			
		};
		
		/**
		 * Método que irá recuperar e devolver um validador
		 * 
		 * @param id Identificador do validador
		 * @return boolean
		 */
		self.get = function (id) {
			if ( !self.isValidador(id) ) {
				return null;
			}
			return validadores[id];
		}
		
		/**
		 * Método verificar se o validador está registrado
		 * 
		 * @param id Identificador do validador
		 * @return boolean
		 */
		self.isValidador = function ( id ) {
			if( typeof validadores[id] === 'function' ) {
				return true;
			}
			return false;
		}
		
		return self;
		
	})();
	
	/**
	 * Método público para validar um dado
	 * 
	 * @param id Identificador do validador
	 * @param data dado a ser validado
	 * @param configs Dados e configurações adicionais que podem ser utilizadas pelo validador
	 * 
	 * @return boolean
	 */
	shValidation.validateData = function (id, data, node) {
		
		//Verificando se possuimos o validador
		if( !shValidation.validadores.isValidador(id) ) {
			//seto como texto
			id = 'text';
		}
		
		//Verificando o dado
		var validator = shValidation.validadores.get(id);
		return validator(data, node);
		
	};
	
	/**
	 * Método para processar o objeto de configurações que é recebido pelas funções de validação
	 */
	function processarConfigs ( configs ) {
		
		//TRATANDO O CONFIGS
		if( typeof configs != 'object' ) {
			configs = {
				executeCallbacks: true,
				callback: 'default',
				validate: true
			};
		}
		//Execução de Callbacks
		if( typeof configs['executeCallbacks'] != 'boolean' ) {
			configs['executeCallbacks'] = true;
		}
		//Identificador dos Callbacks
		if( typeof configs['callback'] != 'string' ) {
			configs['callback'] = 'default';
		}
		//Validação dos campos
		if( typeof configs['validate'] != 'boolean' ) {
			configs['validate'] = true;
		}
		
		return configs;
	}
	
	/**
	 * Método público para validar um nó de input 
	 * 
	 * @param data dado a ser validado
	 * @param configs Dados e configurações adicionais que podem ser utilizadas pelo validador
	 * 				Descrevemos aqui as utilziadas pela própria função
	 * 					executeCallbacks (boolean) true
	 * 					callback (string) identificador do set de callbacks
	 * 
	 * @return boolean
	 */
	shValidation.validateNode = function (node, configs) {
		
		//Processando configuracoes
		configs = processarConfigs(configs);
		
		//VARIAVEIS DE CONTROLE
		var value, validationType;
		var jqNode = $(node);
		var validated = required = false;
		
		//capturando o valor do campo
		var value = node.value;
		if(value == '<br />' || value == '<br/>' || value == '<br>') { value = ''; }

		//determinando modelo de validacao
		if( node.getAttribute('validationtype') && node.getAttribute('validationtype').length ) {
			validationType = node.getAttribute('validationtype');
		}
		else if ( node.getAttribute('validationType') && node.getAttribute('validationType').length ) {
			validationType = node.getAttribute('validationType');
		}
		else {
			validationType = 'text';
		}
		
		//DETERMINANDO OBRIGACAO
		//verificamos se ele é obrigatorio
		if( node.getAttribute('required') !== null ) {
			required = true;
		}
		//não sendo, verificamos se o input possui valor e se possuir valor digo que ele é obrigatorio
		else {
			//se for input
			if( node.tagName.toLowerCase() == 'input' ) {
				//só considero os do tipo texto
				var inputType = node.getAttribute('type');
				if( !inputType ) { inputType = 'text'; }

				if( inputType == 'text' && value.length > 0 ) {
					required = true;
				}
			}
		}
		
		//se o campo não for obrigatório retorno null (Não opino sobre a validacao do dado)
		if( !required  ) {
			return null;
		}
		
		//PRECISAMOS OLHAR SE ELE É DO TIPO RADIO OU CHECKBOX, 
		//SENDO RADIO ou CHECK
		if( node.tagName.toLowerCase() == 'input' && ( node.getAttribute('type') == 'radio' || node.getAttribute('type') == 'checkbox' ) ) {
			validated = false;
			//FIXME acredito que a concatenacao do nome do imput ali dentro pode dar problema devido a []
			if( jqNode.closest('form').find('input[name="'+node.getAttribute('name')+'"]:checked').length > 0 ) {
				validated = true;
			}
		}
		//SENDO INPUT NORMAL
		else {
			//Valido o dado neste momento
			validated = shValidation.validateData(validationType, node.value, node);
		}
		
		
		//EXECUTAR CALLBACKS SINGULARES DESSE CAMPO
		if( configs['executeCallbacks'] ) {
			//Capturando callbacks
			var cb = shValidation.callbacks.get(configs['callback']);
			//Se foi validado
			if( validated ) {
				cb.success(node);
			}
			//Não foi validado
			else {
				cb.error(node);
			}
		}
		
		//Agora devo retornar se ele foi validado ou não
		return validated;
	};
	
	/**
	 * Método público para validar um set de nós a partir de um nó de controle
	 * 
	 * //TODO
	 */
	shValidation.validateNodeSet = function (dataset, configs) {
		
		//Verificando se existe o atributo que cancela a validação.
		if ( 
			typeof dataset == 'object' && typeof dataset.getAttribute == 'function' 
			&& (dataset.getAttribute('sh-form-noValidate') || dataset.getAttribute('sh-form-novalidate')) 
		) {
			return true;
		}
		
		//Tratando as configurações
		configs = processarConfigs(configs);
		
		//tratando e obtendo os callbacks finais
		var cb = shValidation.callbacks.get(configs['callback']);
		
		//verificando a necessidade da validação
		if( !configs['validate'] ) {
			return true;
		}

		//variaveis de controle
		var nodesInvalid = [];
		var nodesValid = [];
		var errors = 0;
		var jqDataset = $(dataset);

		//atualizando valores do ckeditor
		if( typeof window.CKEDITOR != 'undefined' ) {
			for ( instance in CKEDITOR.instances ) {
				CKEDITOR.instances[instance].updateElement();
			}
		}

		//BUSCANDO TODOS OS NODES QUE DEVEM SER VALIDADOS
		var dataNodes 		= jqDataset.find('input[name], textarea[name], select[name]');
		//determinando id para essa validação
		var validationId 	= getUniqueId();

		//Itero por todos os campos que necessitam ser validados e valido cada um desses
		dataNodes.each(function () {
			
			//PRECISO DEFINIR QUAL O TIPO DE VALIDAÇÃO QUE DEVEMOS REALIZAR

			var validated = shValidation.validateNode(this, configs);

			if( validated === false ) {
				nodesInvalid.push(this);
				errors++;
			}
			else if ( validated ) {
				nodesValid.push(this);
			}
		});

		/*
		 * Verificando ocorrencia de erros e chamando os callbacks para o set
		 */
		if( configs['executeCallbacks'] ) {
			//Capturando callbacks
			var cb = shValidation.callbacks.get(configs['callback']);
			
			//Verificando se temos erros
			if( errors > 0 ) {
				cb.setError(dataset, nodesValid, nodesInvalid);
				return false;
			}
			//Não tendo erros
			else {
				cb.setSuccess(dataset, nodesValid, nodesInvalid);
				return true;
			}
		}
		
		//Verificando se temos error
		if( errors > 0 ) {
			return false;
		}
		return true;
	};
	
	//REGISTRANDO OS VALIDADORES
	{
		/*
		 * Validando número generico
		 */
		shValidation.validadores.push('number', function (string, node) {

			//removendo . e trocando a ,
			string = string.replace(/\./g, '');
			string = string.replace(/\,/g, '.');

			//determinando o float a partir da entrada
			if( !isNumber(string) ) {
				return false;
			}
			var number = parseFloat(string);

			//verificando maximo
			if( node && node.getAttribute('data-number-max') ) {
				var max = parseFloat(node.getAttribute('data-number-max'));
				if( number > max ) { return false; }
			}
			//verificando minimo
			if( node && node.getAttribute('data-number-min') ) {
				var min = parseFloat(node.getAttribute('data-number-min'));
				if( number < min) { return false; }
			}

			return true;
		});
		
		/*
		 * Validando inteiro
		 */
		shValidation.validadores.push('integer', function (string, node) {

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
			
		});
		
		/*
		 * Validando texto
		 */
		shValidation.validadores.push('text', function (string, node) {

			var lengthMinimo = null;
			var lengthMaximo = null;
			
			//verificando se temos mínimo ou máximo de caracteres
			if( node && typeof node.getAttribute == 'function' ) {
				lengthMinimo = parseInt(node.getAttribute('data-text-min'));
				lengthMaximo = parseInt(node.getAttribute('data-text-max'));
			}

			//tratando tamanho minimo
			if( lengthMinimo && lengthMinimo > 0 && string.length < lengthMinimo ) {
				return false;
			}
			//tratando tamanho maximo
			else if( lengthMaximo && lengthMaximo > 0 && string.length > lengthMaximo ) {
				return false;
			}
			else if( string === null || typeof string == 'undefined'){
				return false;
			}
			else {
				return !!string.length;
			}
		});
		
		/*
		 * Validando cpf
		 */
		shValidation.validadores.push('cpf', function (string, node) {

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
		});
		
		/*
		 * Validando de RG
		 * Iremos validar se o rg possui entre 5 e 16 digitos
		 */
		shValidation.validadores.push('rg', function (string, node) {
			if( string.length > 5 && string.length < 16 ) {
				return true;
			}
			return false;
		});
		
		/*
		 * Validando CEP
		 * 
		 */
		shValidation.validadores.push('cep', function (string, node) {
			return !!string.toString().match(/^[0-9]{2}\.[0-9]{3}-[0-9]{3}$/);
		});
		
		/*
		 * Validando Email
		 * 
		 */
		shValidation.validadores.push('email', function (string, node) {
			return !!string.toString().match(/^[0-9a-zA-Z][0-9a-zA-Z_.-]+@[0-9a-zA-Z][0-9a-zA-Z_.-]+\.[a-zA-Z]{2,4}$/);
		});
		
		/*
		 * Validando Telefone
		 * 
		 */
		shValidation.validadores.push('telefone', function (string, node) {
			return !!string.toString().match(/^\([0-9]{2}\) [0-9]{4,5}\.[0-9]{4,5}$/);
		});

		/*
		 * Validando Url
		 * 
		 */
		shValidation.validadores.push('url', function (string, node) {
			return !!string.toString().match(/^(http|https):\/\/(www\.)?[a-zA-Z0-9-][a-zA-Z0-9-\.]*\.[a-zA-Z]{1,3}$/);
		});
		
		/*
		 * Validando Date
		 * 
		 * TODO Devemos implementar uma inteligencia melhor aqui que consigamos verificar limites de data e outros
		 * 
		 */
		shValidation.validadores.push('date', function (string, node) {
			return !!string.toString().match(/^(0[0-9]|[0,1,2][0-9]|3[0,1])\/(0[0-9]|1[0,1,2])\/[0-9]{4}$/);
		});
		
		/*
		 * Validando datetime
		 * 
		 * TODO Devemos implementar uma inteligencia melhor aqui que consigamos verificar limites de data e outros
		 * 
		 */
		shValidation.validadores.push('datetime', function (string, node) {
			var date = string.toString().slice(0, 10);
			var time = string.toString().slice(11);
			var validated = true;

			validated = validated && !!date.match(/^(0[0-9]|[0,1,2][0-9]|3[0,1])\/(0[0-9]|1[0,1,2])\/[0-9]{4}$/);
			validated = validated && !!time.match(/^([0-1][0-9]|[2][0-3])(\:[0-5][0-9])(\:[0-5][0-9])?$/);
			return validated;
		});
		
		/*
		 * Validando time
		 * 
		 */
		shValidation.validadores.push('time', function (string, node) {
			var validated = true;
			validated = validated && !!string.match(/^([0-1][0-9]|[2][0-3])(\:[0-5][0-9])(\:[0-5][0-9])?$/);
			return validated;
		});
	}
	
	/*
	 * Callbacks
	 */
	shValidation.callbacks = (function () {
		
		//OBJETO DE CONTROLE
		var self = {};
		
		//REPOSIOTIO DE CALLBACKS
		var callbacks = {};
		
		/**
		 * Método que permite o registro de novos callbacks
		 * 
		 * @param id Identificador dos callbacks
		 * @param obj Objeto que contem o set com as funcoes de callback
		 * 
		 * 
		 */
		self.push = function (id, obj) {
			
			//verificando 
			if( typeof id != 'string' || typeof obj != 'object' ) {
				throw 'Callbacks inválidos';
			}
			
			//verificando se já possuimos um validador com esse id
			if( typeof callbacks[id] !== 'undefined' ) {
				throw 'Callbacks já definidos';
			}
			
			//Preenchendo as posicoes vazias
			var objFinal = {};
			$.extend(true, objFinal, {
				setSuccess: function () {},
				success: function () {},
				error: function () {},
				setError: function () {},
			}, obj);
			
			//Setando callback
			callbacks[id] = objFinal;
		}
		
		/**
		 * Método que permite recuperar o set de callbacks para uso
		 * 
		 * @param id Identificador dos callbacks
		 * 				Se não enviado assumimos o default
		 * 
		 * @return object
		 */
		self.get = function (id) {
			
			//utilizando o default como padrao 
			if( typeof id != 'string' ) {
				id = 'default';
			}
			
			if( typeof callbacks[id] !== 'object' ) {
				throw 'Callbacks inválidos';
			}
			
			return callbacks[id];
		}
		
		
		
		
		//GERANDO O CALLBACK PADRAO
		var defaultCB = {
			/**
			 * Callback padrão para sucesso do set inteiro
			 * Ele não fará nada. Os parametros recebidos são
			 * 
			 * @param baseNode Elemento js que representa o nó base
			 * @param validated objeto com todos os inputs validados
			 * @param failed objeto com todos os inputs que falharam
			 */
			setSuccess : function (baseNode, validated, failed ) {
				
			},
			/**
			 * Callback padrão para sucesso
			 * Irá subir para a primeira div acima dele e irá inserir a classe 'has-success' removendo a 'has-error'
			 * 
			 */
			success : function (node) {
				
				var jqDiv = $(node).closest('div');
				jqDiv.removeClass('has-error').removeClass('sh-val-failed');
				jqDiv.addClass('has-success');
				
			},
			/**
			 * Callback padrão para erro do set inteiro
			 * Ele não fará nada. Os parametros recebidos são
			 * 
			 * @param baseNode Elemento js que representa o nó base
			 * @param validated objeto com todos os inputs validados
			 * @param failed objeto com todos os inputs que falharam
			 */
			setError : function (baseNode, validated, failed ) {
				
			},
			/**
			 * Callback padrão para erro
			 * Irá subir para a primeira div acima dele e irá inserir a classe 'has-error' removendo a 'has-success'
			 * 
			 */
			error : function (node) {
				
				var jqDiv = $(node).closest('div');
				jqDiv.addClass('has-error').addClass('sh-val-failed');
				jqDiv.removeClass('has-success');
				
			}
			
		};
		//Registrando o default
		self.push('default', defaultCB);
		
		return self;
		
	})();
	
	/**
	 * Agora irei criar a adaptação do sistema antigo, permitindo o uso das funções antigas
	 */
	//Validar set de dados
	shValidation.validarDados 	= function (dataset, userCB) {
		
		var idCb 		= null;
		
		//verificando o uso de cb
		if( 
			userCB && typeof userCB == 'object' 
			&& ( typeof userCB.success == 'function' || typeof userCB.error == 'function' || typeof userCB.errorSet == 'function' || typeof userCB.successSet == 'function'   )
		) {
			//Gerando id para o callback
			idCb = getUniqueId();
			//Registrando o set
			shValidation.callbacks.push(idCb, userCB);
		}
		
		var configs = {
			callback: idCb
		};
		
		//Validando no novo modelo e retornando
		var validated = shValidation.validateNodeSet(dataset, configs);
		return validated;
		
	};
	shValidation.validar 	= shValidation.validarDados;
	
	//Validar um só node
	shValidation.validarCampo = function (dataNode, callbacks) {
		
		var idCb 		= null;
		
		//verificando o uso de cb
		if( 
			userCB && typeof userCB == 'object' 
			&& ( typeof userCB.success == 'function' || typeof userCB.error == 'function' || typeof userCB.errorSet == 'function' || typeof userCB.successSet == 'function'   )
		) {
			//Gerando id para o callback
			idCb = getUniqueId();
			//Registrando o set
			shValidation.callbacks.push(idCb, userCB);
		}
		
		var configs 	= {
			callback: idCb
		};
		
		//Validando no novo modelo e retornando
		var validated = shValidation.validateNode(dataNode, configs);
		return validated;
		
	};
	
	
	return shValidation;
});
require(['sheer/validation']);