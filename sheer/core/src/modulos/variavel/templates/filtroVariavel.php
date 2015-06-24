<?php 

	$variavelDS = \Sh\ModuleFactory::getModuleDataSource('variavel','variavel');
	
?>

<section class="sim-box sim-box-is">
		
	<header>
		<div><span data-icon="e"></span></div>
		
		<h1>Faça sua busca</h1>
	</header>

	<div class="sim-box-content">
		<form class="sim-form" action="renderer.php?rd=variavel/listaVariavel" method="post" 
				sh-is sh-is-holder="#listaGrupo" autocomplete="off">
			<fieldset>
			
				<?php 
				$html = '';
				$html .= \Sh\RendererLibrary::renderFieldBox($variavelDS->getField('nome', false), null, array(
					'placeholder' => 'Nome',
					'required' => false
				));
				$html .= \Sh\RendererLibrary::renderFieldBox($variavelDS->getField('tipoVariavel', false), null, array(
					'required' => false
				));
				echo $html;
				?>
			
			</fieldset>
			
		</form>
	</div>
	
</section>