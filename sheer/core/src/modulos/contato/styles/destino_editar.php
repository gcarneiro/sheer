<?php
	$destino = reset($content['contato/destino_detalhes']['results']);
	$destinoDS = \Sh\ModuleFactory::getModuleDataSource('contato', 'destino');
?>
<section class="sh-box sh-box-laranja sh-margin-x-auto sh-w-600">
	
	<header>
		<div><span data-icon="a"></span></div>
		<h1>Atualizar Destino</h1>
	</header>
	
	<div class="sh-box-content">
	
		<form class="sh-form" action="action.php?ah=contato/destino_update" method="post" novalidate sh-form >
		
			<fieldset>
			
				<h3>Informe os dados do destino</h3>
				
				<div class="sh-form-fs">
					<?php
						$html = '';

						$html .= '<input type="hidden" name="id" value="'.$destino['id'].'" required />';
						
						//NOME
						$html .= \Sh\RendererLibrary::renderFieldBox($destinoDS->getField('nome', false), $destino['nome'], array(
								'placeholder'=>'Nome',
								'required'=>'true'
						), array('div'=>array('class'=>'sh-w-1')));
						
						//EMAIL ENVIO
						$html .= \Sh\RendererLibrary::renderFieldBox($destinoDS->getField('email', false), $destino['email'], array(
								'placeholder'=>'Email',
								'required'=>'true',
								'validationType' => 'email'
						), array('div'=>array('class'=>'sh-w-1')));
						
						echo $html;
					?>
				</div>
				
			</fieldset>
				
			
			<div class="sh-btn-holder">
				<button type="submit" class="sh-btn-laranja">Salvar</button>
			</div>
		
		</form>
	
	</div>

</section>
