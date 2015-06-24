<?php
	$contatoDS = \Sh\ModuleFactory::getModuleDataSource('contato', 'contato');
?>
<section class="sh-box sh-box-laranja sh-margin-x-auto sh-w-600">
	
	<header>
		<div><span data-icon="a"></span></div>
		<h1>Envio de contato pelo website</h1>
	</header>
	
	<div class="sh-box-content">
	
		<form class="sh-form" action="action.php?ah=contato/contato_add" method="post" novalidate sh-form >
		
			<fieldset>
			
				<h3>Informe os dados do destino</h3>
				
				<div class="sh-form-fs">
					<?php
						$html = '';
						
						//Destino
						$html .= \Sh\RendererLibrary::renderFieldBox($contatoDS->getField('idDestino', false), null, array(
							'blankOption'=>'Destino',
							'required'=>'true'
						), array('div'=>array('class'=>'sh-w-1')));

						//Nome
						$html .= \Sh\RendererLibrary::renderFieldBox($contatoDS->getField('nome', false), null, array(
							'placeholder'=>'Nome',
							'required'=>'true'
						), array('div'=>array('class'=>'sh-w-1')));

						//Telefone
						$html .= \Sh\RendererLibrary::renderFieldBox($contatoDS->getField('telefone', false), null, array(
							'placeholder'=>'Telefone',
							'mask' => 'telefone',
							'validationType' => 'telefone'
						), array('div'=>array('class'=>'sh-w-1')));
						
						//EMAIL
						$html .= \Sh\RendererLibrary::renderFieldBox($contatoDS->getField('email', false), null, array(
								'placeholder'=>'Email',
								'required'=>'true',
								'validationType' => 'email'
						), array('div'=>array('class'=>'sh-w-1')));

						//Estado
						$html .= \Sh\RendererLibrary::renderFieldBox($contatoDS->getField('idEstado', false), null, array(
							'blankOption'=>'Estado',
							'sh-localidade-role' => 'estado'
						), array('div'=>array('class'=>'sh-w-1-2')));

						//Cidade
						$html .= \Sh\RendererLibrary::renderFieldBox($contatoDS->getField('idCidade', false), null, array(
							'blankOption'=>'Cidade',
							'sh-localidade-role' => 'cidade'
						), array('div'=>array('class'=>'sh-w-1-2')));

						//Assunto
						$html .= \Sh\RendererLibrary::renderFieldBox($contatoDS->getField('assunto', false), null, array(
							'placeholder'=>'Assunto'
						), array('div'=>array('class'=>'sh-w-1')));

						//Mensagem
						$html .= \Sh\RendererLibrary::renderFieldBox($contatoDS->getField('mensagem', false), null, array(
							'placeholder'=>'Mensagem',
							'required' => true
						), array('div'=>array('class'=>'sh-w-1')));
						
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