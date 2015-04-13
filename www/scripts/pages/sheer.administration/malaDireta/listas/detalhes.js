define('currentPage', ['jquery', 'sheer'], function (jq, sheer) {
	
	return {
		malaDiretaEmails: {
			
			//responsehandler que irá trocar o status e o navigation de acordo com seu novo status
			habilitarDesabilitarResponse: function (promise, node) {
				promise.done(function ( data ) {
					data = JSON.parse(data);
					if(data.status) {
						
						linha = $('tr[data-id="'+data.data.id+'"]');
						status = 'Sim';
						pop = 'Habilitado com Sucesso';
						navigation = 'navegacaoEmailHabilitado';
						
						//itero por cada registro alterado e faço as modificações
						for(var i = 0 ; i < data.data.email.length ; i++){
							
							var id = data.data.email[i].id;
							linha = $('tr[data-id="'+id+'"]');
							
							//verifico o seu status e defino as mudanças
							if(data.data.email[i].enviar==2){
								status = 'Não';
								pop = 'Desabilitado com Sucesso';
								navigation = 'navegacaoEmailDesabilitado';
							}
							
							//troco o status
							linha.children().eq(3).html(status);
							
							//troco o navigation
							linha.find($('a')).attr('href', navigation);
						}
						$(document.body).trigger('click');
						notify.create(pop, 'success');
						
					}
					else {
						notify.create(data.message, 'fail');
					}
				});
			},
			
		}
	};
	
});