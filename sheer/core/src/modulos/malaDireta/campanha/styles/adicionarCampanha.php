<?php
	$campanhaDs = \Sh\ModuleFactory::getModuleDataSource('malaDiretaCampanha', 'malaDiretaCampanha');
?>
<section class="sh-box sh-box-laranja sh-w-1000 sh-margin-x-auto ">
	
	<header>
		<div><span data-icon="a"></span></div>
		<h1>Adicionar Campanha</h1>
	</header>
	
	<div class="sh-box-content">
	
		<form class="sh-form" action="action.php?ah=malaDiretaCampanha/malaDiretaCampanha_add" method="post" novalidate sh-form >
		
			<fieldset class="sh-grid-box">
			
				<h3>Informe os dados da campanha</h3>
				<div class="sh-form-fs">
					<?php
						$html = '';
						
						//ASSUNTO
						$html .= \Sh\RendererLibrary::renderFieldBox($campanhaDs->getField('assunto', false), null, array(
								'placeholder'=>'Assunto',
								'required'=>'true',
						), array('div'=>array('class'=>'sh-w-1')));
	
						//REMETENTE
						$html .= \Sh\RendererLibrary::renderFieldBox($campanhaDs->getField('idRemetente', false), null, array(
							'placeholder'=>'Nome de Envio',
							'required'=>'true'
						), array('div'=>array('class'=>'sh-w-1')));
	
						//HTML
						$html .= \Sh\RendererLibrary::renderFieldBox($campanhaDs->getField('html', false), null, array(
								'required'=>'true'
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
