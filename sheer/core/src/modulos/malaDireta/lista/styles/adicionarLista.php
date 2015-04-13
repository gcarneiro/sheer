<?php
	$listaDS = \Sh\ModuleFactory::getModuleDataSource('malaDiretaLista', 'malaDiretaLista');
?>
<section class="sh-box sh-box-laranja sh-w-800 sh-margin-x-auto ">
	
	<header>
		<div><span data-icon="a"></span></div>
		<h1>Adicionar lista</h1>
	</header>
	
	<div class="sh-box-content">
	
		<form class="sh-form" action="action.php?ah=malaDiretaLista/malaDiretaLista_add" method="post" novalidate sh-form >
		
			<fieldset class="sh-grid-box">
			
				<h3>Informe os dados da lista</h3>
				
				<div class="sh-form-fs">
					<?php
						$html = '';
						
						//NOME
						$html .= \Sh\RendererLibrary::renderFieldBox($listaDS->getField('nome', false), null, array(
								'required'=>'true',
								'placeholder' => 'Nome da lista'
						), array('div'=>array('class'=>'sh-w-1-1')));

						//TIPO
						$html .= \Sh\RendererLibrary::renderFieldBox($listaDS->getField('tipo', false), 1, array(
							'id' => 'tipoLista',
							'required'=>'true'
						), array('div'=>array('class'=>'sh-w-1-1')));

						$html .= '<div id="listaComplexaInformacoes" style="display: none;">';
							$html .= '<h4>Informações de acesso ao servidor</h4>';
							
							//HOST
							$html .= \Sh\RendererLibrary::renderFieldBox($listaDS->getField('host', false), null, array(
// 								'required'=>'true',
								'placeholder' => 'Endereço do servidor'
							), array('div'=>array('class'=>'sh-w-1-2 sh-form-field')));
							
							//databaseName
							$html .= \Sh\RendererLibrary::renderFieldBox($listaDS->getField('databaseName', false), null, array(
// 									'required'=>'true',
									'placeholder' => 'Nome do banco de dados'
							), array('div'=>array('class'=>'sh-w-1-2 sh-form-field')));
							
							//databaseTable
							$html .= \Sh\RendererLibrary::renderFieldBox($listaDS->getField('databaseTable', false), null, array(
// 									'required'=>'true',
									'placeholder' => 'Tabela do banco de dados'
							), array('div'=>array('class'=>'sh-w-1-3 sh-form-field')));
							
							//username
							$html .= \Sh\RendererLibrary::renderFieldBox($listaDS->getField('username', false), null, array(
// 								'required'=>'true',
								'placeholder' => 'Usuário de Acesso do banco'
							), array('div'=>array('class'=>'sh-w-1-3 sh-form-field')));
							
							//password
							$html .= \Sh\RendererLibrary::renderFieldBox($listaDS->getField('password', false), null, array(
								'placeholder' => 'Senha de acesso do banco'
							), array('div'=>array('class'=>'sh-w-1-3 sh-form-field')));
							

							$html .= '<h4>Customizando os campos</h4>';
							
							//fieldNome
							$html .= \Sh\RendererLibrary::renderFieldBox($listaDS->getField('fieldNome', false), null, array(
									'placeholder' => 'Nome do campo para "nome"'
							), array('div'=>array('class'=>'sh-w-1-2 sh-form-field')));
								
							//fieldEmail
							$html .= \Sh\RendererLibrary::renderFieldBox($listaDS->getField('fieldEmail', false), null, array(
									'placeholder' => 'Nome do campo para "email"'
							), array('div'=>array('class'=>'sh-w-1-2 sh-form-field')));
								
							//fieldEnviar
							$html .= \Sh\RendererLibrary::renderFieldBox($listaDS->getField('fieldEnviar', false), null, array(
									'placeholder' => 'Nome do campo para "enviar"'
							), array('div'=>array('class'=>'sh-w-1-2 sh-form-field')));

							//fieldEnviarValor
							$html .= \Sh\RendererLibrary::renderFieldBox($listaDS->getField('fieldEnviarValor', false), null, array(
									'placeholder' => 'Valor do campo a se considerar positivo o envio'
							), array('div'=>array('class'=>'sh-w-1-2 sh-form-field')));

						$html .= '</div>';
	
						echo $html;
					?>
					
					
				</div>
				
			</fieldset>
				
			<div class="sh-btn-holder">
				<button type="submit" class="sh-btn-laranja">Enviar</button>
			</div>
		
		</form>
	
	</div>
</section>

<script>
	$('#tipoLista').on('change', function () {
		var listaInfo = $('#listaComplexaInformacoes');
		if( this.value == 2 ) {
			listaInfo.show();
		}
		else {
			listaInfo.hide();
			listaInfo.find('input[type="text"]').val('');
		}
		$.colorbox.resize();
	});
</script>