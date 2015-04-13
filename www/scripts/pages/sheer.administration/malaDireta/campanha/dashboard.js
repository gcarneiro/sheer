define('currentPage', ['jquery', 'sheer'], function (jq, sheer) {
	
	return {
		
		campanhaDashboard : {
			
			removerCampanhaResponse : function(promise){
				promise.done(function (data) {
					data = JSON.parse(data);
					if( data.status ){
						sheer.pages.redirect('?p=malaDireta/campanhas');
					}
					else{
						notify.create(data.message, 'fail');
					}
				});
			},
		},
	};
	
});