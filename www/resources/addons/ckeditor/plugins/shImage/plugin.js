CKEDITOR.plugins.add( 'shImage', {
    icons: 'shImage',
    init: function( editor ) {
    	
    	//inserindo botao
    	editor.ui.addButton( 'shImage', {
    	    label: 'Imagem',
    	    command: 'inserirImagem'
    	});
    	
    	/*
    	 * Método para cadastrar as imagens quando selecionadas
    	 */
    	function cadastrarImagem () {
    		
			//capturando imagem e criadno formData
			var image = this.files[0];
			var formData = new FormData();
			formData.append('image', image);
			
			//enviando requisicao
			require(['sheer'], function (sheer) {
				var promise = sheer.action('filePicture/adicionarImagemDireta', {}, formData);
				
				//DETERMINANDO PATH A SER UTILIZADO PARA A IMAGEM
				var urlFinal 		= '';
				var baseUrl 		= document.getElementsByTagName('base');
				//Não consegui encontrar o base url
				if( baseUrl.length > 0 ) {
					baseUrl = baseUrl.item(0).getAttribute('href');
				}
				else {
					urlFinal = window.location.protocol+'//'+window.location.host+window.location.pathname;
					urlFinal = urlFinal.substring(0, urlFinal.lastIndexOf('/')+1);
				}
				
				//Efetuando troca de variavel
				var absolutePath = urlFinal;
				
				
				//criando notificação
				notify.create({
					timeout: promise,
					message: 'Enviando imagem, aguarde...'
				});
				
				promise.done(function (data) {
					data = JSON.parse(data);
					
					if( data.status ) {
						//crio o path da imagem
						var imagePath = absolutePath+data['data']['picsMap']['sheer']['sh_medium']['path'];
						//vou substituir as ocorrencias de /./ por apenas / pare remover o ./ do inicio do nome
						imagePath = imagePath.replace('/./', '/');
						
						var html = '<img src="'+imagePath+'" style="margin: 0.4em;" />';
						editor.insertHtml( html, 'unfiltered_html' );
					}
					else {
						notify.create(data.message, 'fail');
					}
					
				});
				
			});
		}
    	
    	//criando comando
		editor.addCommand( 'inserirImagem', {
			exec: function( editor ) {
				
				//INJETOR INPUT TYPE FILE NA BODY
				var inputFile = document.body.querySelector('#ckeditorSheerInputImage');
				if( !inputFile ) {
					inputFile = document.createElement('input');
					inputFile.setAttribute('id', 'ckeditorSheerInputImage');
					inputFile.setAttribute('accept', 'image/*');
					inputFile.setAttribute('type', 'file');
					inputFile.style.display = 'none';
					
					//CRIANDO EVENTO DE ESCUTA
					inputFile.addEventListener('change', cadastrarImagem);
				}
				document.body.appendChild(inputFile);
				
				//ABRINDO DIALOG DE ARQUIVOS
				inputFile.click();
				
				
//				var now = new Date();
//				
			}
		});
    }
});