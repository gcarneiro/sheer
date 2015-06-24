<?php
	$remetenteDs = \Sh\ModuleFactory::getModuleDataSource('malaDiretaRemetente', 'malaDiretaRemetente');
?>
<section class="sh-box sh-box-laranja sh-margin-x-auto sh-w-600">
	
	<header>
		<div><span data-icon="a"></span></div>
		<h1>Cadastro de Remetente</h1>
	</header>
	
	<div class="sh-box-content">
	
		<form class="sh-form" action="action.php?ah=malaDiretaRemetente/malaDiretaRemetente_add" method="post" novalidate sh-form >
		
			<fieldset>
			
				<h3>Informe os dados do remetente</h3>
				
				<div class="sh-form-fs">
					<?php
						$html = '';
						
						//NOME
						$html .= \Sh\RendererLibrary::renderFieldBox($remetenteDs->getField('nomeEnvio', false), null, array(
								'placeholder'=>'Nome',
								'required'=>'true'
						), array('div'=>array('class'=>'sh-w-1')));
						
						//EMAIL ENVIO
						$html .= \Sh\RendererLibrary::renderFieldBox($remetenteDs->getField('emailEnvio', false), null, array(
								'placeholder'=>'Email',
								'required'=>'true',
								'validationType' => 'email'
						), array('div'=>array('class'=>'sh-w-1')));
						
						//LISTA EMAIL
						$html .= \Sh\RendererLibrary::renderFieldBox($remetenteDs->getField('listaEmail', false), null, null,
						array('div'=>array('class'=>'sh-w-1')));
	
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
