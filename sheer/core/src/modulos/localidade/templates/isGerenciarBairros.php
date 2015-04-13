<?php 
	$bairroDS = \Sh\ModuleFactory::getModuleDataSource('localidade', 'bairro');
?>

<section class="sh-box sh-box-agua">
		
	<header>
		<div><span data-icon="e"></span></div>
		
		<h1>Faça sua busca</h1>
	</header>

	<div class="sh-box-content">
		
		<form class="sh-form" action="renderer.php?rd=localidade/gerenciarBairros" method="post" sh-is sh-is-holder="#listaBairros" autocomplete="off">
			<fieldset>
			
				<div class="sh-form-field sh-w-250">
					<label for="idUf">Estado</label>
					<?php 
						$html = '';
						
						$html .= \Sh\RendererLibrary::renderField($bairroDS->getField('idUf', false), null, array(
							'blankOption' => 'Estado',
							'sh-localidade-role' => 'estado',
							'label' => 'Estado',
							'sh-is-ignore' => true
						));
	
						echo $html;
					?>
				</div>
				
				<div class="sh-form-field sh-w-400">
					<label for="idCidade">Cidade</label>
					<?php 
						$html = '';
						
						$html .= \Sh\RendererLibrary::renderField($bairroDS->getField('idCidade', false), null, array(
							'blankOption' => 'Cidade',
							'sh-localidade-role' => 'cidade',
							'label' => 'Cidade'
						));
	
						echo $html;
					?>
				</div>
			
			</fieldset>
			
		</form>
		
	</div>
	
</section>