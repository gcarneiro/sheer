<?php
	$remetente = reset($content['malaDiretaRemetente/malaDiretaRemetente_detalhes']['results']);
	$remetenteDs = \Sh\ModuleFactory::getModuleDataSource('malaDiretaRemetente', 'malaDiretaRemetente');
?>
<section class="sh-box sh-box-laranja sh-margin-x-auto ">
	
	<header>
		<div><span data-icon="a"></span></div>
		<h1>Editar Remetente</h1>
	</header>
	
	<div class="sh-box-content">
	
		<form class="sh-form" action="action.php?ah=malaDiretaRemetente/malaDiretaRemetente_update" method="post" novalidate sh-form >
		
			<fieldset>
			
				<h3>Informe os dados do remetente</h3>
				
				<div class="sh-form-fs">
				
					<?php
						$html = '';
						$html .= '<input type="hidden" name="id" id="id" value="'.$remetente['id'].'" />';
						
						//NOME
						$html .= \Sh\RendererLibrary::renderFieldBox($remetenteDs->getField('nomeEnvio', false), $remetente['nomeEnvio'], array(
								'placeholder'=>'Nome',
								'required'=>'true'
						), array('div'=>array('class'=>'sh-w-1')));
						
						//EMAIL ENVIO
						$html .= \Sh\RendererLibrary::renderFieldBox($remetenteDs->getField('emailEnvio', false), $remetente['emailEnvio'], array(
								'placeholder'=>'Email',
								'required'=>'true',
								'validationType' => 'email'
						), array('div'=>array('class'=>'sh-w-1')));
						
						//LISTA EMAIL
						$html .= \Sh\RendererLibrary::renderFieldBox($remetenteDs->getField('listaEmail', false), $remetente['listaEmail'], null,
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
	