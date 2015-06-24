/**
 * Iniciando loader de página
 */
var sheer_pageLoader_visible = false;
var sheer_pageLoader_started = false;
var sheerPageLoader = (function () {
	
	function inserirDivLoader () {
		//criando divLoading
		divAguardando = document.createElement('div');
			divAguardando.classList.add('sheer-page-loading');
			divAguardando.style.display = 'block';
			divAguardando.style.position = 'fixed';
			divAguardando.style.width = '100%';
			divAguardando.style.height = '100%';
			divAguardando.style.top = '0%';
			divAguardando.style.left = '0%';
			divAguardando.style.zIndex = '99999';
			divAguardando.style.backgroundColor = '#fff';
			divAguardando.style.opacity = '0.7';
		//inserindo na body
		window.document.body.appendChild(divAguardando);
		
		//criando texto de carregamento
		var textoAguardando = document.createElement('p');
		textoAguardando.innerHTML = 'Carregando...';
			textoAguardando.style.position = 'absolute';
			textoAguardando.style.left = '15px';
			textoAguardando.style.bottom = '0';
		divAguardando.appendChild(textoAguardando);
		
		//marcando o laoder como visivel
		sheer_pageLoader_visible = true;
	}
	
	//Função para chamar a insercao do loader
	function runLoader () {
		//marcando o loader como iniciado
		sheer_pageLoader_started = true;
		
		if( document.body ) {
			inserirDivLoader();
		}
		else {
			window.setTimeout(runLoader, 100);
		}
		
	}
	runLoader();
	
})();

/*
 * Este será responsável pelo controle de interface da administracao do Sheer
 * 
 * Ele irá tratar
 * 		sheer/instantSearch
 * 		sheer/imageRepository
 * 		sheer/interface
 * 		sheer/ui
 * 
 */
//Comecando sheer com requireJS
define('sheer/adm', ['jquery', 'sheer'], function(jq, sheer) {
	
	var sheerAdm = {};
	
	/*
	 * Criando o Section Loading a ser utilizado por todos
	 * Este deve ser chamado para exibirmos o loading de uma seção, trocando o texto do botão e também o título da seção
	 */
	sheerAdm.showSectionLoading = (function () {
		
		/*
		 * Método para exibir o loading enquanto o IS ainda é processado
		 */
		function exibirLoading(promise, node) {
			
			//Gerando o jqueryObject
			var jqNode = $(node);
			
			//Iniciando variaveis
			var section, title, icon, imgLoader, submitButton;
			//determina se é exibicao normal do Loading... Somente se tiver o titulo do sh-box
			var exibicaoNormal = false;
			
			//DETERMINANDO EXIBICAO NORMAL E CARREGANDO VARIAVEIS
			//buscando a sessão
			section = jqNode.closest('section.sh-box');
			if( section.length > 0 && section.has('[sh-noLoader]') == false ) {
				title = section.children('header').children('h1, h2, h3, h4, h5, h6').eq(0);
				if( title.length ) {
					//marcando a exibicao normal
					exibicaoNormal = true;
					//buscando a imagem
					icon = title.prev();
					if( !icon.children('[data-icon]').length ) {
						icon = null;
					}
				}
			}
			
			//EXIBINDO LOADING INICIAL
			if( exibicaoNormal ) {
				//trocando o título da header da seção por buscando
				title[0].originalContent = title.text();
				title.text('Enviando...');
				//trocando a imagem do data-icon para o de carregando
				if( icon ) {
					if( icon.children('.sh-is-loader').length == 0 ) {
						icon.append('<img class="sh-is-loader" src="resources/images/loaders/branco_bg_transparent_16.gif" />');
						imgLoader = icon.children('.sh-is-loader');
					}
					else {
						imgLoader = icon.children('.sh-is-loader').eq(0);
					}
					icon.children('[data-icon]').hide();
					imgLoader.show();
				}
			}
			//ALTERANDO VALOR DO BOTAO DE SUBMIT
			jqNode.find('button, input[type="submit"]').each(function () {
				var submitNode = $(this);
				if( !(this.tagName.toLowerCase() == 'input' || (this.tagName.toLowerCase() == 'button' && this.type.toLowerCase() == 'submit')) ) {
					return;
				}
				this.originalContent = submitNode.text();
				submitNode.text('Enviando...');
				
			});
			
			promise.always(function () {
				if( exibicaoNormal ) {
					title.text(title[0].originalContent);
					if( icon ) {
						imgLoader.hide();
						icon.children('[data-icon]').show();
					}
				}
				
				//ALTERANDO VALOR DO BOTAO DE SUBMIT
				jqNode.find('button, input[type="submit"]').each(function () {
					if( !(this.tagName.toLowerCase() == 'input' || (this.tagName.toLowerCase() == 'button' && this.type.toLowerCase() == 'submit')) ) {
						return;
					}
					$(this).text(this.originalContent);
				});
			});
		}
		
		/*
		 * Retornnado a função de exibição
		 */
		return exibirLoading;
		
	})();
	
	/********************************************************************************************************************
	 * UI
	 */
	/*
	 * Controlador de dicas de conteudo. Basta colocar o atributo qtip="Texto que deseja que seja apresentado"
	 */
	var _goFullScreen = (function () {
		
		var inicializado = false;
		
		function init() {
			
			if( inicializado ) { return; }
			inicializado = true;
			
			$(document).on('click', '.sh-goFullscreen', function (evt) {
				evt.preventDefault();
				
				goFullScreen();
			})
			
		}
		
		/*
		 * Método para ir para o modo fullscreen
		 */
		function goFullScreen () {
			var isInFullScreen = (document.fullScreenElement && document.fullScreenElement !== null)
				||
				(document.mozFullScreen || document.webkitIsFullScreen);
	
			var docElm = document.documentElement;
			if (!isInFullScreen) {
				
				if (docElm.requestFullscreen) {
					docElm.requestFullscreen();
				} 
				else if (docElm.msRequestFullscreen) {
					docElm.msRequestFullscreen();
				} 
				else if (docElm.mozRequestFullScreen) {
					docElm.mozRequestFullScreen();
				} 
				else if (docElm.webkitRequestFullscreen) {
					docElm.webkitRequestFullscreen();
				}
			}
			else {
				if (docElm.exitFullscreen) {
					docElm.exitFullscreen();
				} 
				else if (docElm.msExitFullscreen) {
					docElm.msExitFullscreen();
				} 
				else if (docElm.mozCancelFullScreen) {
					docElm.mozCancelFullScreen();
				} 
				else if (docElm.webkitExitFullscreen) {
					docElm.webkitExitFullscreen();
				}
			}
		}
		init();
	})();
	
	/**
	 * QTIP, atribui automaticamente o qtip a nos html que possuam qtip como parametro
	 */
	var _tips = (function () {
		
		var inicializado = false;
		
		function init() {
			
			if( inicializado ) { return; }
			inicializado = true;
			
			//Carregando qtip
			require(['qtip'], function () {
				//iniciando configuracao
				config();
			});
		}
		
		/*
		 * Método de configuração inicial para o qtip
		 */
		function config () {
			
			//Caso o qtip ainda não esteja totalmente carregado, seto um timeout para tentar novamente e finalizo
			if( typeof $.fn.qtip == 'undefined' ) {
				window.setTimeout(function () {
					config();
				}, 500);
				return;
			}
			
			//Inserindo listener para escolha do arquivo
			$(document).on('mouseenter', '[qtip]', function (evt) {
				evt.preventDefault();
				
				//verificando se existe valor válido
				if( !this.getAttribute('qtip') || this.getAttribute('qtip').length < 3 ){ return; }
				
				//definindo posicionamento
				var position = determinaPosicionamento(this);
				
				//criando qtip2
				$(this).qtip({
					overwrite: false,
					show: { ready: true },
					position: position,
					style: {
						classes: 'qtip-light qtip-shadow qtip-rounded'
				    },
					content: {
						text: this.getAttribute('qtip')
					}
				});
			});
		}
		
		function determinaPosicionamento ( node ) {
	
			var qtipPosition = node.getAttribute('qtip-position') || node.getAttribute('qtip-pos');
			var qtipPosMy = node.getAttribute('qtip-pos-my');
			var qtipPosAt = node.getAttribute('qtip-pos-at');
			
			//Definindo posição default
			var position = {
				my: 'top center',
				at: 'bottom center'
			};
			
			if( qtipPosMy && qtipPosAt ) {
				position = {
					my: qtipPosMy,
					at: qtipPosAt
				};
			}
			else if ( qtipPosition ) {
				switch (qtipPosition) {
				case 'top': 
					position.my = 'bottom center';
					position.at = 'top center';
					break;
				case 'right': 
					position.my = 'center left';
					position.at = 'center right';
					break;
				case 'left': 
					position.my = 'center right';
					position.at = 'center left';
					break;
				}
			}
			
			return position;
			
		}
		init();
	})();
	
	/*
	 * Controlador para transformar textareas em textareas autosize.
	 * Basta inserir o marcador autosize
	 */
	var _autosize = (function () {
		
		var inicializado = false;
		
		function init() {
			
			if( inicializado ) { return; }
			inicializado = true;
			
			require(['autosize'], function () {
				//iniciando configuracao
				config();
			});
		}
		
		/*
		 * Método de configuração inicial para o qtip
		 */
		function config () {
			
			//Inserindo listener para escolha do arquivo
			$(document.body).on('click', 'textarea[autosize]', function (evt) {
				evt.preventDefault();
				
				//criando qtip2
				$(this).autosize({
					callback: asCallBack
				});
			});
		}
		
		function asCallBack (textarea) {
			var jqTextArea = $(textarea);
			
			if( jqTextArea.closest('#cboxContent').length==1 ) {
				$.colorbox.resize();
			}
		}
		
		init();
	})();
	
	/*
	 * Tabs
	 */
	var _tabs = (function () {
		
		var inicializado = false;
		
		function init() {
			
			if( inicializado ) { return; }
			inicializado = true;
			
			//Inserindo listener para escolha do arquivo
			$(document).on('click', '.sh-tab > h1, .sh-tab > h2, .sh-tab > h3, .sh-tab > h4, .sh-tab > h5, .sh-tab > h6', function (evt) {
				evt.preventDefault();
				
				if(evt.target.tagName.toLowerCase() == 'a'){
					return;
				}
				
				if ( this.parentNode.classList.contains('sh-tab-open') ) {
					this.parentNode.classList.remove('sh-tab-open');
				}
				else {
					this.parentNode.classList.add('sh-tab-open');
				}
			});
			
		}
		init();
	})();
	
	/*
	 * Input file
	 */
	var _inputFile = (function () {
		
		var inicializado = false;
		function init() {
			
			if( inicializado ) { return; }
			inicializado = true;
			
			//Inserindo listener para escolha do arquivo
			$(document).on('click', '.sh-input-file [data-input-trigger]', function (evt) {
				evt.preventDefault();
				$(this).closest('.sh-input-file').find('input[type="file"]').trigger('click');
			});
			
			//Inserindo listener de selecao de arquivo
			$(document).on('change', '.sh-input-file input[type="file"]', function (evt) {
				evt.preventDefault();
				arquivoSelecionado(this);
			});
			
			//Listerner de marcação de remoção de arquivo
			$(document).on('change', '.sh-input-file input[type="checkbox"][data-op="remove"]', function (evt) {
				//controles
				var inputFile = $(this).closest('.sh-input-file');
				var fileText = inputFile.find('> div > div > a');
				var fileInput = inputFile.find('input[type="file"]');
				
				//limpando input file
				fileInput.val('');
				
				//removendo link
				fileText.attr('href', '#');
				
				//CASO SEJA PARA REMOVER
				if( this.checked == true ) {
					//escrevendo que é remoçao do arquivo
					fileText.html('<span style="color: red;">Arquivo será removido</span>');
				}
				else {
					fileText.html('&nbsp;');
				}
			});
		}
		
		/**
		 * Método para controlar a seleção de arquivo
		 */
		function arquivoSelecionado (node) {
			var file = node.files[0];
			var fileText = $(node).closest('.sh-input-file').find('> div > div > a');
			if( file ) {
				fileText.attr('href', '#');
				fileText.html(file['name']);
			}
			else {
				fileText.attr('href', '#');
				fileText.html('&nbsp;');
			}
			
		}
		init();
	})();
	
	/*
	 * Input Case [upper] [small]
	 */
	var _inputCase = (function () {
		var inicializado = false;
		function init() {
			
			if( inicializado ) { return; }
			inicializado = true;
			
			//UPPERCASE
			$(document.body).on('focusout', '[data-uppercase]', function (evt) {
				$(this).val( $(this).val().toUpperCase() );
			});
			
			//LOWERCASE
			$(document.body).on('focusout', '[data-lowercase]', function (evt) {
				$(this).val( $(this).val().toLowerCase() );
			});
			
		}
		init();
	})();
	
	/*
	 * DatePickers
	 */
	var _datePicker = (function () {
		
		var inicializado = false;
		
		/**
		 * Método de inicialização
		 */
		function init () {
			
			if( inicializado ) {
				return;
			}
			inicializado = true;
			
			//iniciando configuracao
			require(['datepicker', 'datepicker-ptbr'], function () {
				config();
			});
		}
		
		/**
		 * Método para configuração inicial do datePicker
		 */
		function config () {
			
			//Iniciando observador do DOM
			observeDomChanges();
			
			//DETERMINANDO CONFIGURACOES DO DATEPICKER PARA PORTUGUES E SETANDO FORMATO PADRAO
			$.fn.datepicker.defaults.autoclose = true;
			$.fn.datepicker.defaults.format = "dd/mm/yyyy";
			$.fn.datepicker.defaults.language = 'pt-BR';
			$.fn.datepicker.defaults.todayHighlight = true;
			
			//Iniciando todos os presentes que são datePicker
			$('[datepicker], [datePicker]').each(function () {
				//Aplicando o select ao target
				apply(this);
			});
			
		}
		
		/**
		 * Método para aplicar o datePicker dados sobre um node
		 */
		function apply (node) {
			
			//Tratando para ver se o node já possui o datePicker
			if( node.classList.contains('has-datePicker') ) {
				return;
			}
			
			//capturando tipo de datePicker
			var datePickerAttr = node.getAttribute('datepicker');
			if( !datePickerAttr ) {
				datePickerAttr = node.getAttribute('datePicker');
			}
			
			//removendo autocomplete
			node.setAttribute('autocomplete', 'off');
			node.classList.add('has-datePicker');
			
			//DETERMINANDO CASOS CUSTOMIZADOS
			/*
			 * firstDay - Este irá devolver o primeiro dia do mes
			 * 
			 * Para controle ele irá colocar o input como readonly para que o usuário não consiga alterar o valor na mão
			 */
			if( datePickerAttr == 'firstDay' ) {
				node.setAttribute('readonly', 'readonly');
				$(node).datepicker({
					viewMode: 'months',
					minViewMode: 'months'
				});
			}
			/*
			 * lastDay - Este irá devolver o último dia do mes
			 * 
			 * Para controle ele irá colocar o input como readonly para que o usuário não consiga alterar o valor na mão
			 */
			else if( datePickerAttr == 'lastDay' ) {
				node.setAttribute('readonly', 'readonly');
				$(node).datepicker({
					viewMode: 'months',
					minViewMode: 'months'
				});
				$(node).on('changeDate', function () {
					var value = this.value;
					//criando data no ultimo dia do mes
					var date = new Date(value.substring(6), value.substring(3,5), 0);
					var ano = date.getFullYear();
					var mes = (date.getMonth()+1).toString().lpad('0', 2);
					var dia = date.getDate().toString().lpad('0', 2);
					
					this.value = dia+'/'+mes+'/'+ano;
				});
			}
			else if ( datePickerAttr == 'year' ) {
				node.setAttribute('readonly', 'readonly');
				$(node).datepicker({
					format: 'yyyy',
					viewMode: 'years',
					minViewMode: 'years'
				});
			}
			/*
			 * Modo normal, sem comportamentos específicos
			 */
			else {
				$(node).datepicker();
			}
			
			require(['moment'], function (moment) {
				//SETANDO startDate
				if( node.getAttribute('datePicker-startDate') || node.getAttribute('datepicker-startDate') ) {
					var startDate = node.getAttribute('datePicker-startDate') || node.getAttribute('datepicker-startDate');
					var a = moment(startDate, 'DD/MM/YYYY');
					$(node).datepicker('setStartDate', a.toDate());
				}
				
				//SETANDO endDate
				if( node.getAttribute('datePicker-endDate') || node.getAttribute('datepicker-endDate') ) {
					var endDate = node.getAttribute('datePicker-endDate') || node.getAttribute('datepicker-endDate');
					var a = moment(endDate, 'DD/MM/YYYY');
					$(node).datepicker('setEndDate', a.toDate());
				}
			});
		};
		
		/**
		 * Criando observador para mudanças no dom com aplicação automatica
		 */
		function observeDomChanges () {
			
			//Criando objeto observador
			var observer = new MutationObserver(function(mutations) {
				//Operando por todas as mutações
				mutations.forEach(function(mutation) {
					
					//Se tivemos nodes adicionados
					if( mutation.addedNodes.length > 0 ) {
						
						//vamos buscar os filhos que devem receber o datePicker
						$(mutation.target).find('[datepicker], [datePicker]').each(function () {
							//Aplicando o select ao target
							apply(this);
						});
						
					}
				});
			});
			
			//Seto o observador para ouvir as mudanças no document
			observer.observe(document, { childList: true, subtree: true });
			
		}
		
		//Iniciando
		$(document).ready(function () {
			init();
		});
		
	})();
	
	/*
	 * Controle dos ckEditors
	 */
	var _ckEditor = (function () {

		/**
		 * Método de inicialização
		 */
		var inicializado = false;
		function init () {
			
			if( inicializado ) {
				return;
			}
			inicializado = true;
			
			require(['ckeditor'], function () {
				//iniciando configuracao
				config();
			});
			
		}
		
		/**
		 * Criando observador para mudanças no dom com aplicação automatica
		 */
		function observeDomChanges () {
			
			//Criando objeto observador
			var observer = new MutationObserver(function(mutations) {
				//Operando por todas as mutações
				mutations.forEach(function(mutation) {
					
					//Se tivemos nodes adicionados
					if( mutation.addedNodes.length > 0 ) {
					
						//Aplicando o select ao target
						apply(mutation.target);
						
					}
				});
			});
			
			//Seto o observador para ouvir as mudanças no document
			observer.observe(document, { childList: true, subtree: true });
			
		}
		
		/**
		 * Método para aplicar o select2 dados sobre um context que será assumido como o document
		 */
		function apply ( context ) {
			
			if( context == undefined || !context ) {
				context = document.body;
			}
			
			$(context).find('textarea[richtext]').not('.ckeditor_ini').each(function () {
				
				var node = this;
				
				if( node.getAttribute('richtext') == 'simple' || node.getAttribute('richtext') ) {
					
					this.classList.add('ckeditor_ini');
					
					CKEDITOR.replace( this, {
						//seto este para liberar o uso de qualquer tag
						allowedContent: true,
						toolbar : [
						    { items: [ 'Bold', 'Italic', 'Underline', '-', 'Subscript', 'Superscript', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
						    { items: [ 'NumberedList', 'BulletedList' , 'shImage'] },
						    { items: [ 'Link', 'Unlink', 'Anchor' ] },
							{ items: [ 'Undo', 'Redo' ] },
							{ items: [ 'Maximize', 'Source'] },
						
						],
						extraPlugins : 'shImage',
						skin: 'bootstrapck'
					});
				}
			});
			
		}
		
		/**
		 * Método para atualizar os valores de todos os textareas controlados pelo CKEditor 
		 */
		function updateValues () {
			if( typeof CKEDITOR === 'undefined'  ) { return; }
			
			for ( instance in CKEDITOR.instances ) {
				CKEDITOR.instances[instance].updateElement();
			}
		}
		
		/**
		 * Método para configuração inicial do select2
		 */
		function config () {
			//sempre recalcular tamanho dos colorbox ao abrir um ckeditor
			CKEDITOR.on("instanceReady", function(event) {
				if( typeof $.colorbox == 'function' || typeof $.fn.colorbox == 'function' ) {
					$.colorbox.resize();
				}
			});
			
			//definindo updateValues do CKEDITOR
			CKEDITOR.updateValues = updateValues;
			
			//Quando o DOM terminar de carregar vamos iniciar as instancias
			$(document).ready(function () {
				apply(document.body);
				
				//Iniciando observações do DOM
				observeDomChanges();
			});
		}
		
		//Iniciando
		init();
	})();
	
	/*
	 * Select2
	 */
	var _select2 = (function () {
		
		var inicializado = false;
		
		/**
		 * Método de inicialização
		 */
		function init () {
			
			if( inicializado ) {
				return;
			}
			inicializado = true;
			
			require(['select2'], function () {
				//iniciando configuracao
				config();
			});
			
		}
		
		/**
		 * Criando observador para mudanças no dom com aplicação automatica
		 */
		function observeDomChanges () {
			
			//Criando objeto observador
			var observer = new MutationObserver(function(mutations) {
				//Operando por todas as mutações
				mutations.forEach(function(mutation) {
					
					//Se tivemos nodes adicionados
					if( mutation.addedNodes.length > 0 ) {
					
						//Aplicando o select ao target
						apply(mutation.target);
						
					}
				});
			});
			
			//Seto o observador para ouvir as mudanças no document
			observer.observe(document, { childList: true, subtree: true });
			
		}
		
		/**
		 * Método para aplicar o select2 dados sobre um context que será assumido como o document
		 */
		function apply ( context ) {
			
			//Definindo contexto padrão
			if( context == undefined || !context ) {
				context = document;
			}
			
			var selects = $(context).find('select');
			selects.each(function (index, el) {
				var jqEl = $(el);
				if( typeof el.hasSelect2 == 'undefined' ) {
					jqEl.select2();
					el.hasSelect2 = true;
				}
			});
		}
		
		/**
		 * Método para configuração inicial do select2
		 */
		function config () {
			
			//Caso o select2 ainda não esteja totalmente carregado, seto um timeout para tentar novamente e finalizo
			if( typeof $.fn.select2 == 'undefined' ) {
				window.setTimeout(function () {
					config();
				}, 500);
				return;
			}
			
			//Setando o pt-Br
			require(['select2-i18n/pt-BR'], function () {
				$.fn.select2.defaults.set("language", "pt-BR");
			});
			//Setando configurações default
			$.fn.select2.defaults.set("width", "100%");
			
			//Quando o DOM terminar de carregar vamos iniciar as instancias
			$(document).ready(function () {
				apply(document.body);
				
				//Iniciando observador
				observeDomChanges();
			});
		}
		
		//Iniciando select2
		init();
	})();
	
	/*
	 * Tabela administrativa com seleção de checkbox
	 */
	var _adminTable = (function () {
		
		var inicializado = false;
		
		function init () {
			
			if( inicializado ) {
				return;
			}
			inicializado = true;
			
			var jqDocumentBody = $(document.body);
			
			/**
			 * TRATANDO "SH-TABLE-ADMIN" simples
			 */
			
			//MAPEANDO CLICK NO CHECKBOX dos conteudos
			jqDocumentBody.on('click', 'table.sh-table-admin > tbody > tr > td input[type="checkbox"]', function (evt) {
				
				evt.stopPropagation();
				
				var elCheck = $(this);
				var linha = elCheck.closest('tr');
				
				linha.removeClass('selected');
				if( elCheck.prop('checked') ) {
					linha.addClass('selected');
				}
			});
			
			//MAPEANDO ESCUTA DE DBLCLICK NA LINHA DO CONTEUDO
			jqDocumentBody.on('dblclick', 'table.sh-table-admin > tbody > tr', function () {
				var linha = $(this);
				var checkHolder = linha.children('td').eq(0);
				var check = checkHolder.children('[type="checkbox"]');
				
				linha.removeClass('selected');
				check.trigger('click');
				if( check.is(':checked') ) {
					linha.addClass('selected');
				}
			});
			
			
			//MAPEANDO TROCA DO VALOR DO CHECK PARA DETERMINAR O VALOR DO TODOS
			jqDocumentBody.on('change', 'table.sh-table-admin > tbody > tr > td:first-child input[type="checkbox"]', function () {
				
				var elCheck = $(this);
				var tbody = elCheck.closest('tbody');
				var checkTodos = tbody.parent().children('thead').find('> tr > th:first-child input[type="checkbox"]');
				
				var elementos = tbody.find('> tr:not([data-descriptor]) > td:first-child input[type="checkbox"]');
				var totalItens = elementos.length;
				var marcados = elementos.filter(':checked').length;
				
				if( marcados == 0 ) {
					checkTodos[0].indeterminate = false;
					checkTodos[0].checked = false;
				}
				else if ( marcados != totalItens ) {
					checkTodos[0].indeterminate = true;
					checkTodos[0].checked = false;
				}
				else {
					checkTodos[0].indeterminate = false;
					checkTodos[0].checked = true;
				}
				
			});
			
			
			//MAPEANDO CHECKAGEM DO CHECK DO SELECIONAR TODOS
			jqDocumentBody.on('change', 'table.sh-table-admin > thead > tr > th:first-child input[type="checkbox"]', function () {
				
				var checkPrincipal = $(this);
				var checked = checkPrincipal.prop('checked');
				var tbody = checkPrincipal.closest('table').children('tbody');
				
				
				if( checked ) {
					tbody.find('> tr:not([data-descriptor]) > td:first-child').children('[type="checkbox"]').prop('checked', true);
					tbody.find('> tr').addClass('selected');
				}
				else {
					tbody.find('> tr:not([data-descriptor]) > td:first-child').children('[type="checkbox"]').prop('checked', false);
					tbody.find('> tr').removeClass('selected');
				}
				
			});
			
			/**
			 * TRATANDO O SH-TABLE-DESCRIPTOR
			 */
			jqDocumentBody.on('click', 'table.sh-table-descriptor > tbody > tr:not([data-descriptor])', function (evt) {
				
				if(evt.target.tagName.toLowerCase() == 'a'){
					return;
				}
				//Buscando linha do elemento e o id do conteudo
				var elementRow = $(this);
				var contentId = elementRow.attr('data-id');
				if( contentId.length < 1 ) {
					return;
				}
				//buscando a linha descritora do conteudo
				var descriptorRow = elementRow.closest('tbody').find('> tr[data-descriptor="'+contentId+'"]');
				if( descriptorRow.length < 1 ) {
					return;
				}
				
				
				//exibindo a linha descritora
				descriptorRow.toggleClass('opened');
				
				
			});
		}
		//Ao ter carregado o dom inicio o adminTable
		$(document).ready(function () {
			init();
		});
	})();
	
	
	/********************************************************************************************************************
	 * INTERFACES
	 */
	/*
	 * Controle geral do menu de navegação principal
	 */
	var _mainNavigation = (function () {
		
		var menuPrincipalNode = null;
		
		/*
		 * Controle de inicialização
		 */
		var inicializado = false;
		function init() {
			if(inicializado) {return;}
			inicializado=true;
			
			/*
			 * Listener de clique no item de abertura do menu principal
			 */
			$(document).on('click', '.sheer-main-navigation-trigger', function (event) {
				event.preventDefault();
				event.stopPropagation();
				
				//buscando menu principal
				if( !menuPrincipalNode ) {
					menuPrincipalNode = document.getElementById('sheer-main-navigation');
					if( !menuPrincipalNode ) {
						return;
					}
				}
				
				menuPrincipalNode.classList.toggle('open');
			});
			
			/*
			 * Listener para fechar o menu quando clicar em qualquer elemento que não esteja no próprio elemento
			 */
			$(document).on('click', function (event) {
				if( $(event.target).closest('#sheer-main-navigation').length == 0 ) {
					
					//buscando menu principal
					if( !menuPrincipalNode ) {
						menuPrincipalNode = document.getElementById('sheer-main-navigation');
					}
					if( !menuPrincipalNode ) {
						return;
					}
					
					menuPrincipalNode.classList.toggle('open', false);
				}
			});
			
			/*
			 * Listener de clique em item do menu para abrir/fechar seu submenu
			 */
			$(document).on('click', '#sheer-main-navigation ul li a', function (event) {
				//Se possuir filhos devo abrir/fecha seu submenu
				if( this.parentNode.querySelector('ul') ) {
					event.preventDefault();
					this.parentNode.classList.toggle('open');
				}
			});
			
		}
		init();
	})();
	
	/*
	 * ResponseHandler Logout
	 */
	function rhLogout (promise, node) {
			
		promise.done(function (data) {
			data = JSON.parse(data);
			
			if( data.status ) {
				window.location.href = '.';
			}
			else {
				sheer.notify.create(data.message, 'fail');
			}
		});
	}
		
	/*
	 * ResponseHandler do trocarPerfil
	 */
	function rhTrocarPerfil (promise, node) {
		
		promise.done(function (data) {
			data = JSON.parse(data);
			if( data.status ) {
				window.location.href=".";
			}
			else {
				sheer.notify.create(data.message, 'fail');
			}
		});
		
	}
	
	/*
	 * Fazendo a escuta dos módulos principais para remover o pageLoader
	 * Só irei rodar a função de remoção se foi inciiado o loader
	 */
	if( sheer_pageLoader_started ) {
		$(document).ready(function () {
			require(['qtip', 'datepicker', 'select2', 'colorbox'], function () {
				function removeLoader () {
					if( sheer_pageLoader_visible ) {
						$('.sheer-page-loading').remove();
					}
					else {
						window.setTimeout(function () {
							removeLoader();
						}, 200);
					}
				}
				removeLoader();
			});
		});
	}
	
	/*
	 * ResponseHandler para login no sistema
	 */
	function rhLogin (promise, node) {
		
		promise.done(function (data) {
			
			if( data.status ) {
				window.location.href=".";
			}
			else {
				sheer.notify.create(data.message, 'fail');
			}
			
		});
		
	}
	
	//Criando retorno de responseHandlers
	sheerAdm.rh = {
		logout: rhLogout,
		trocarPerfil: rhTrocarPerfil,
		login: rhLogin
	}
	
	//ImageRepository
	sheerAdm.imageRepository = (function () {
		
		/**
		 * Controlador de inicialização
		 */
		var inicializado = false;
		function init (  ) {
			if(inicializado) {return;}
			inicializado = true;
			
			//iniciando subModulos
			_interface.init();
		}
		
		var _interface = (function () {
			
			//CONTROLADORES
			//Utilizo esta para guardar o identificador do elemento que está sendo arrastado
			var draggingElements = {};
			var dragPlaceDroppable = $('<li class="dropZone"><div><img src="" /></div></li>');
			
			/**
			 * Método para exibir o box de seleção de novos arquivos
			 * Este irá verificar se o input de controle está presente nesse imrep. Se não estiver presente irá criar
			 * Irá também simular o clique neste
			 */
			function abrirBoxSelecaoArquivos ( jqNodeImageRepository ) {
				
				var nodeInputFile = jqNodeImageRepository.find('input.sheer-imrep-inputFile');
				
				//criando o input caso não exista
				if( nodeInputFile.length == 0 ) {
					nodeInputFile = document.createElement('input');
					nodeInputFile.classList.add('sheer-imrep-inputFile');
					nodeInputFile.setAttribute('type', 'file');
					nodeInputFile.setAttribute('multiple', 'multiple');
					nodeInputFile.setAttribute('accept', 'image/*');
					nodeInputFile.style.display = 'none';
					nodeInputFile = $(nodeInputFile);
					
				}
				//capturando o primeiro caso ele ja exista
				else {
					nodeInputFile = nodeInputFile.eq(0);
				}
				jqNodeImageRepository.prepend(nodeInputFile);
				nodeInputFile.trigger('click');
				
			}
			
			/*
			 * Controlador dos callbacks dos eventos de drag&drop
			 */
			var _listenersDragDrop = {
				dragstart: function (evt) {
					var event = evt.originalEvent;
					//determinando id para o item movido
					if( !this.imrepId ) {
						this.imrepId = require('sheer').lib.getUniqueId();
						draggingElements[this.imrepId] = this;
					}
					event.dataTransfer.setData('imrepId', this.imrepId);
					
					//inserindo a imagem no dragPlaceDroppable
					var dropZoneImage = dragPlaceDroppable.find('img');
					dropZoneImage.attr('src', $(this).children('div').children('a').children('img').attr('src'));
					dropZoneImage.css('max-width', '128px');
					dropZoneImage.css('max-height', '96px');
					
					this.classList.add('dragging');
				},
				dragend: function (evt) {
					var event = evt.originalEvent;
					this.classList.remove('dragging');
					dragPlaceDroppable.detach();
				},
				dragenter: function (evt) {
					evt.preventDefault();
					
					var nodeCesto = this;
					
					//capturando elemento sendo arrastado
					var event = evt.originalEvent;
					var imrepId = event.dataTransfer.getData('imrepId');
					var nodeDragging = draggingElements[imrepId];
					
					$(nodeCesto).after(dragPlaceDroppable);
				},
				dragleave: function (evt) {
					evt.preventDefault();
					dragPlaceDroppable.detach();
					
				},
				/**
				 * Crio este para dar o preventDefault. Com ele passo a poder dar drop neste local
				 * docs: https://developer.mozilla.org/en-US/docs/Web/Guide/HTML/Drag_operations
				 */
				dragover: function (evt) {
					evt.preventDefault();
				},
				drop: function (evt) {
					evt.preventDefault();
					//capturando imagem movida
					var event = evt.originalEvent;
					var imrepId = event.dataTransfer.getData('imrepId');
					var nodeImagemMovida = draggingElements[imrepId];
					var nodeImagemAnteriorMovida = nodeImagemMovida.previousElementSibling;
					var nodeImagemReferencia = this;
					
					//reposicionando a imagem
					reposicionar(nodeImagemMovida, nodeImagemReferencia, nodeImagemAnteriorMovida);
				}
			};
			
			/**
			 * Método utilizado para reposicionar uma imagem
			 */
			var reposicionando = false;
			function reposicionar (imagemMovida, imagemReferencia, imagemAnterior) {
				
				//verificando se estamos reposicionando algum objeto 
				if( reposicionando ) {
					notify.create('Estamos processando o último reposicionamento, aguarde para efetuar a operação');
					return;
				}
				
				//criando objeto da requisicao
				var data = {
					idImagem: imagemMovida.getAttribute('data-id'),
					idReferencia: imagemReferencia.getAttribute('data-id'),
					idAnterior: null,
				};
				//faço essa verificacao pois nem sempre tenho o anterior
				if( imagemAnterior ) {
					data.idAnterior = imagemAnterior.getAttribute('data-id');
				}
				else {
					//capturo o proximo da imagem movida, apenas para caso não tenha anterior, conseguir devolver a movida para atras dele em caso de erro na requisicao
					var imagemProximaMovida = imagemMovida.nextElementSibling;
				}
				
				//REORGANIZO AS IMAGENS CONFORME SELECIONADO PELO USUÁRIO
				$(imagemReferencia).after(imagemMovida);

				//EFETUANDO A REQUISICAO
				var promise = sheer.ajax('action.php?ah=imageRepository/reposicionarImagem', 'POST', data, function () {
					reposicionando = true;
				});
				
				//Requisição com sucesso
				promise.done(function (data) {
					data = JSON.parse(data);
					if( !data.status ) {
						notify.create(data.message, 'fail');
						//realocar as imagens
						if( imagemAnterior ) {
							$(imagemAnterior).after(imagemMovida);
						}
						else {
							$(imagemProximaMovida).before(imagemMovida);
						}
					}
				});
				
				//Requisição falhou
				promise.fail(function (data) {
					notify.create('Ocorreu um erro inesperado ao tentar enviar imagem. Tente novamente.', 'fail');
					//realocar as imagens
					if( imagemAnterior ) {
						$(imagemAnterior).after(imagemMovida);
					}
					else {
						$(imagemProximaMovida).before(imagemMovida);
					}
				});
				
				//sempre, desmarco o reposicionando
				promise.always(function () {
					reposicionando = false;
				});
				
				return promise;
			}
			
			/*
			 * ResponseHandler para a definciao de capa
			 */
			function definirCapa (promise, node) {

				promise.done(function (data) {
					data = JSON.parse(data);
					if( data.status ) {
						var capaSelecionada = $(node).closest('[data-id]').find('img');
						var capaExibida = document.getElementById('imagemCapa');
						capaExibida.setAttribute('src', capaSelecionada.attr('src'));
						capaExibida.style.width = capaSelecionada[0].style.width;
						capaExibida.style.height = capaSelecionada[0].style.height;
						capaExibida.style.display = 'block';
					}
					else {
						notify.create(data.message, 'fail');
					}
				});
				
			}
			
			/**
			 * ResponseHandler para a remoção de imagens
			 * TODO RECONTAGEM DAS FOTOS DO ALBUM
			 */
			function removerImagem (promise, node) {

				promise.done(function (data) {
					data = JSON.parse(data);
					if( data.status ) {
						var nodeImagem = $(node).closest('[data-id]');
						nodeImagem.remove();
						//verifico se ela é a capa do album
						if( data['data']['capaDoAlbum'] ) {
							var capaExibida = document.getElementById('imagemCapa');
							capaExibida.setAttribute('src', '');
							capaExibida.style.display = 'none';
						}
					}
					else {
						notify.create(data.message, 'fail');
					}
				});
				
			}
			
			/**
			 * Método para processar os arquivos selecionados pelo usuário
			 */
			function arquivosSelecionados ( nodeInputFile ) {
				//verificando quantidade de arquivos enviados
				if( nodeInputFile.files.length < 1 ) {
					return;
				}
				
				var jqNodeInputFile = $(nodeInputFile);
				
				//PRIMEIRO BUSCO O UL RESPONSAVEL
				var jqNodeUl = jqNodeInputFile.closest('.sheer-imrep').find('ul[data-idRepository]');
				
				
				//OPERANDO POR CADA IMAGEM
				var fileList = nodeInputFile.files;
				var imagem = null;
				for(var i=0; i < fileList.length; i++ ) {
					imagem = fileList[i];
					inserirNovaImagem(jqNodeUl, imagem);
				}
				
			}
			
			/**
			 * Método para processar o pedido de insercao de uma nova imagem
			 */
			function inserirNovaImagem (jqNodeUl, imagem) {
				
				var imageTemplateTemporary = '<div>';
						imageTemplateTemporary += '<div class="sheer-imrep-overlay-loading">';
							imageTemplateTemporary += '<img src="resources/images/loaders/branco_bg_transparent_32.gif" title="Enviando" />';
						imageTemplateTemporary += '</div>';
						imageTemplateTemporary += '<div class="sheer-imrep-overlay" style="display: none!important;">';
							imageTemplateTemporary += '<a href="#" data-icon="I"></a>';
							imageTemplateTemporary += '<a href="#" data-icon="C"></a>';
						imageTemplateTemporary += '</div>';
						imageTemplateTemporary += '<img src="" class="imrep-node-image" />';
					imageTemplateTemporary += '</div>';
					
				var imageTemplateFinal = '<div>';
						imageTemplateFinal += '<div class="sheer-imrep-overlay">';
							imageTemplateFinal += '<a href="renderer.php?rd=imageRepository/alterarLegenda&id={{image.id}}" title="Alterar legenda" data-icon="D" sh-component="overlayLink"></a>';
							imageTemplateFinal += '<a href="action.php?ah=imageRepository/marcarCapa" title="Definir como capa do álbum" data-icon="I" sh-component="action" sh-comp-rh="[sheer/imageRepository][rh.definirCapa]" sh-comp-confirm sh-comp-confirmMessage="Deseja realmente marcar esta imagem como capa?"></a>';
							imageTemplateFinal += '<a href="action.php?ah=imageRepository/picture_delete" title="Remover imagem" data-icon="C" sh-component="action" sh-comp-rh="[sheer/imageRepository][rh.removerImagem]" sh-comp-confirm sh-comp-confirmMessage="Deseja realmente remover esta imagem?"></a>';
						imageTemplateFinal += '</div>';
						imageTemplateFinal += '<a href="{{image.medium.path}}">';
							imageTemplateFinal += '<img src="{{image.tbm.path}}" style="width: {{image.tbm.width}}px; height: {{image.tbm.height}}px;" />';
						imageTemplateFinal += '</a>';
					imageTemplateFinal += '</div>';
					
				//CRIAR HTML PARA SER INSERIDO
				var nodeElement = document.createElement('li');
				nodeElement.innerHTML = imageTemplateTemporary;
				nodeElement.classList.add('sh-imrep-cesto');
				var jqNodeElement = $(nodeElement);
				
				//tratando tamanho da imagem
				var jqNodeImage = jqNodeElement.children('div').children('img');
				jqNodeImage.css('max-width', '128px');
				jqNodeImage.css('max-height', '96px');
				
				//carregar a imagem do computador da pessoa
				var reader = new FileReader();
				reader.onload = function (image) {
					jqNodeImage.attr('src', image.target.result);
					jqNodeUl.append(jqNodeElement);
				};
				reader.readAsDataURL(imagem);
				
				
				//REALIZANDO AGORA REQUISIÇÃO AJAX PARA CADASTRAMENTO DA IMAGEM
				var idRepository = jqNodeUl.closest('[data-idRepository]').attr('data-idRepository');
				//gerando parametros da requisicao
				var formData = new FormData();
				formData.append('idRepository', idRepository);
				formData.append('idPicture', imagem);
				
				var promise = sheer.ajax('action.php?ah=imageRepository/picture_add', 'POST', formData);
				
				//Requisição com sucesso
				promise.done(function (data) {
					data = JSON.parse(data);
					if( data.status ) {
						
						var imageInfo = data['data'];
						
						//efetuando substituicoes
						var htmlFinal = imageTemplateFinal.replace('{{image.medium.path}}', imageInfo['pictures']['sheer']['sh_medium']['downloadLink']);
						htmlFinal = htmlFinal.replace('{{image.id}}', imageInfo['id']);
						htmlFinal = htmlFinal.replace('{{image.tbm.path}}', imageInfo['pictures']['sheer']['sh_tbm']['downloadLink']);
						htmlFinal = htmlFinal.replace('{{image.tbm.width}}', imageInfo['pictures']['sheer']['sh_tbm']['width']);
						htmlFinal = htmlFinal.replace('{{image.tbm.height}}', imageInfo['pictures']['sheer']['sh_tbm']['height']);
						
						//CRIAR HTML FINAL PARA SER INSERIDO
						var nodeElementFinal = document.createElement('li');
						nodeElementFinal.innerHTML = htmlFinal;
						nodeElementFinal.classList.add('sh-imrep-cesto');
						nodeElementFinal.setAttribute('draggable', true);
						nodeElementFinal.setAttribute('data-id', data['data']['id']);
						var jqNodeElementFinal = $(nodeElementFinal);
						//insiro a nova após a temporario e removo a temporaria
						jqNodeElementFinal.find('div a img').on('load', function () {
							jqNodeElement.after(jqNodeElementFinal);
							jqNodeElement.remove();
						}); 
						
					}
					else {
						notify.create('Erro ao enviar imagem: '+data.message, 'fail');
						jqNodeElement.remove();
					}
				});
				
				//Requisição falhou
				promise.fail(function (data) {
					notify.create('Ocorreu um erro inesperado ao tentar enviar imagem. Tente novamente.', 'fail');
					jqNodeElement.remove();
				});
				
				return nodeElement;
			}
			
			function rhAlterarLegenda (promise, node) {
				
				//sucesso da requisicao
				promise.done(function (data) {
					
					if( data.status ) {
						$.colorbox.close();
						notify.create('Legenda salva com sucesso', 'success');
						
						//Colocando a nova legenda no elemento
						var idPicture = data['data']['id'];
						$('.sheer-imrep li[data-id="'+idPicture+'"]').attr('data-legenda', data['data']['legenda']);
					}
					else {
						notify.create(data.message, 'fail');
					}
					
				});
			}
			
			
			/**
			 * Controlador de inicialização
			 */
			var inicializado = false;
			function init ( ) {
				if(inicializado) {return;}
				inicializado = true;
				
				/*
				 * Listener de pedido de inserir mais fotos
				 */
				$(document.body).on('click', '.sheer-imrep .sheer-imrep-adicionarImagens', function (evt) {
					evt.preventDefault();
					//abrindo box de selecao dos arquivos
					abrirBoxSelecaoArquivos( $(this).closest('.sheer-imrep') );
				});
				
				/*
				 * Listener de seleção de novos arquivos
				 */
				$(document.body).on('change', '.sheer-imrep .sheer-imrep-inputFile', function (evt) {
					arquivosSelecionados(this);
				});
				
				/*
				 * Listerner de mouseover para mostrar a legenda com o qtip
				 */
				$(document.body).on('mouseenter', '.sheer-imrep ul[data-idRepository] > li:not(.qtip-initiated)', function () {
					
					var node = this;
					var legenda = this.getAttribute('data-legenda');
					
					if( !legenda || legenda.length == 0 ) {
						return;
					}
					
					//inserindo a classe de controle para qtip
					node.classList.add('qtip-initiated');
					//criando qtip
					$(node).qtip({
						overwrite: false,
						show: { ready: true },
						position: {
							my: 'top center',
							at: 'bottom center'
						},
						style: { classes: 'qtip-light qtip-shadow' },
						content: { 
							text: function () {
								//o this aqui já é um objeto jQuery
								return this.attr('data-legenda');
							} 
						}
					});
					
				});
				
				/*
				 * Listerners para o elemento sendo arrastado
				 */
				$(document.body).on('dragstart', '.sheer-imrep ul[data-idRepository] > li', _listenersDragDrop.dragstart);
				$(document.body).on('dragend', '.sheer-imrep ul[data-idRepository] > li', _listenersDragDrop.dragend);
				/*
				 * Listerners para o elemento que é o cesto
				 */
				$(document.body).on('dragenter', '.sheer-imrep ul[data-idRepository] > li:not(.dragging)', _listenersDragDrop.dragenter);
				$(document.body).on('dragleave', '.sheer-imrep ul[data-idRepository] > li:not(.dragging)', _listenersDragDrop.dragleave);
				$(document.body).on('dragover', '.sheer-imrep ul[data-idRepository] > li', _listenersDragDrop.dragover);
				$(document.body).on('drop', '.sheer-imrep ul[data-idRepository] > li', _listenersDragDrop.drop);
							
			}
			
			return {
				init: init,
				rh : {
					definirCapa: definirCapa,
					removerImagem: removerImagem,
					alterarLegenda : rhAlterarLegenda
				}
			}
			
		})();
		
		return _interface;
	})();
	
	/*
	 * Retorno Geral do Sheer.Adm
	 */
	return sheerAdm;
	
});
//Iniciando módulo
require(['sheer/adm']);

/*
 * Definindo Módulos do RequireJS para módulos do Sheer
 */
//Variavel
define('sheer/modules/variavel', ['sheer'], function (sheer) {
	
	function salvarListaGerenciavel(promise, node) {
		
		promise.done(function (data) {
			if( data.status ) {
				sheer.notify.create('Variáveis salvas com sucesso', 'success');
			}
			else {
				sheer.notify.create(data.message, 'fail');
			}
			
		});
		
	}
	
	return {
		salvarListaGerenciavel: salvarListaGerenciavel
	};
});
//Blog
define('sheer/modules/blog', ['sheer'], function (sheer) {
	return {
		rh: {
			postActionResponse : function (promise, node) {
				
				promise.done(function (data) {
					if( data.status ) {
						sheer.notify.create('Post registrado com sucesso. Aguarde...', 'success');
						window.location.href="index.php?p=blog/posts";
						return;
					}
					else {
						sheer.notify.create(data.message, 'fail');
					}
				});
				
			}
		}
	}
});

/*
 * Definição do módulo de mala direta do sheer
 */
define('sheer/modules/maladireta', ['sheer'], function (sheer) {
	
	function rhSincronizarLista (promise, node) {
		
		//iniciando ação
				var spans = node.parentNode.querySelectorAll('span');
				spans[0].style.display = 'inline-block';
				spans[1].style.display = 'none';
				
				promise.done(function (data) {
					data = JSON.parse(data);
					
					if( data.status ) {
						window.location.reload();
					}
					else {
						sheer.notify.create(data.message, 'fail');
					}
				});
				
				//sempre retorno o ícone e removo o loader
				promise.always(function () {
			spans[0].style.display = 'none';
			spans[1].style.display = 'inline-block';
		});
	}
	
	/**
	 * RH para após a habilitacao/desabilitacao do email
	 */
	function rhHabilitarEmail ( promise, node ) {
		//Mudando o ícone
		var jqNode = $(node);
		//Carregando a linha da tabela
		var jqLinha = jqNode.closest('tr');
		
		//Criando notify de processamento
		var notify = sheer.notify.create({
			message: 'Processando...',
			timeout: promise
		});

		promise.done(function (data) {
			data = JSON.parse(data);
			
			//Conseguiu mudar o status
			if( data.status ) {
				$('form[sh-is]').trigger('submit');
			}
			else {
				sheer.notify.create(data.messsage, 'fail');
			}
		});
		
	}
	
	return {
		rh : {
			sincronizarLista: rhSincronizarLista,
			habilitarEmail: rhHabilitarEmail,
			desabilitarEmail: rhHabilitarEmail,
		}
	};
});
//Localidade
define('sheer/modules/localidade', ['sheer'], function (sheer) {
	
	var shLocalidade = {};
	
	function init () {
		//ADICIONANDO AS ESCUTAS DE ESTADO POR ATRIBUTOS
		$(document).on('change', 'select[sh-localidade-role="estado"]', function () {
			
			//preciso determinar o select de cidades
			var estado = $(this);
			var cidades = estado.closest('form').find('select[sh-localidade-role="cidade"]');
			
			//verificando se existe agrupamento no estado
			var grupo = estado.attr('sh-localidade-rel');
			if( grupo !== undefined ) {
				cidades = cidades.filter('[sh-localidade-rel="'+grupo+'"]');
			}
			
			//verificando existencia do relacionado
			if( cidades.length == 0 ) { return false; }
			
			//busco os dados das cidades
			var xhr = sheer.dataProvider('localidade/listaCidadesPorEstado', {}, {
				idUf: estado.val()
			});
			
			xhr.done(function (data) {
				data = JSON.parse(data);
				var html = '';
				if(data['total'] > 0) {
					html += sheer.lib.html.optionsFromJson(data['results'], 'id', 'nome', 'Cidade');
				}
				else {
					html += '<option value="">Selecione</option>';
				}
				cidades.html(html);
				cidades.val('');
				cidades.trigger('change');
			});
			
		});
		
		//ADICIONANDO AS ESCUTAS DE CIDADE POR ATRIBUTOS
		$(document).on('change', 'select[sh-localidade-role="cidade"]', function () {
			//preciso determinar o select de cidades
			var cidade = $(this);
			var bairros = cidade.closest('form').find('select[sh-localidade-role="bairro"]');
			
			//verificando se existe agrupamento no estado
			var grupo = cidade.attr('sh-localidade-rel');
			if( grupo !== undefined ) {
				bairros = bairros.filter('[sh-localidade-rel="'+grupo+'"]');
			}
			
			//verificando existencia do relacionado
			if( bairros.length == 0 ) { return false; }
			
			//busco os dados dos bairros
			var xhr = sheer.dataProvider('localidade/listaBairrosPorCidade', {}, {
				idCidade: cidade.val()
			});
			
			xhr.done(function (data) {
				data = JSON.parse(data);
				var html = '';
				if(data['total'] > 0) {
					html += sheer.lib.html.optionsFromJson(data['results'], 'id', 'nome', 'Cidade');
				}
				else {
					html += '<option value="">Selecione</option>';
				}
				bairros.html(html);
				bairros.val('');
				bairros.trigger('change');
			});
			
		});
	}
	init();
			
	/*
	 * Função para se incluir as escutas na mão
	 */
	shLocalidade.setChangeListener = function ( estado, cidade, bairro ) {
			
		if( !estado || !cidade ) { return false; }
		
		//CHANGE DO ESTADO
		estado.on('change', function () {
			//busco os dados das cidades
			var xhr = sheer.dataProvider('localidade/listaCidadesPorEstado', {}, {
				idUf: estado.val()
			});
			
			xhr.done(function (data) {
				data = JSON.parse(data);
				var html = '';
				if(data['total'] > 0) {
					html += sheer.lib.html.optionsFromJson(data['results'], 'id', 'nome', 'Cidade');
				}
				else {
					html += '<option value="">Cidade</option>';
				}
				cidade.html(html);
			});
			
		});
		
		if( !bairro ) { return false; }
		
		//CHANGE DO ESTADO
		cidade.on('change', function () {
			//busco os dados dos bairros
			var xhr = sheer.loadContent('localidade/listaBairrosPorCidade', {}, {
				idCidade: cidade.val()
			});
			
			xhr.done(function (data) {
				data = JSON.parse(data);
				var html = '';
				if(data['total'] > 0) {
					html += sheer.lib.html.optionsFromJson(data['results'], 'id', 'nome', 'Cidade');
				}
				else {
					html += '<option value="">Cidade</option>';
				}
				bairro.html(html);
			});
			
		});
		
	};
		
	shLocalidade.bairros = {
			
		gerenciar: {
			/*
			 * Método para buscar os dados do IS para usar no add de bairro
			 */
			adicionarContentFunction : function (node) {

				var data = $('.sim-box-is form').serialize();
				return data;
				
			},
			
			adicionarResponseHandler : function (promise, node) {
				
				promise.done(function (data) {
					
					if ( data.status ) {
						$('form[sh-is]').trigger('submit');
						$.colorbox.close();
					}  
					else {
						sheer.notify.create(data.message, 'fail');
					}
				});
				
			},
			
			deleteResponseHandler : function (promise, node) {
				promise.done(function (data) {
					
					data = JSON.parse(data);
					
					if ( data.status ) {
						$('form[sh-is]').trigger('submit');
					}  
					else {
						sheer.notify.create(data.message, 'fail');
					}
				});
				
			}
			
		}
		
	};
	
	return shLocalidade;
});
require(['sheer/modules/localidade']);
