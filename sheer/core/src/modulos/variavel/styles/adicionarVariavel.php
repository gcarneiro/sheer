<?php

$variavel = \Sh\ModuleFactory::getModuleDataSource('variavel','variavel');

?>


<section class="sh-margin-x-auto sim-box sim-box-action sh-w-1000">
	
	<header>
		<div><span data-icon="c"></span></div>
		<h1>Adicionar Variável</h1>
	</header>
	
	<div class="sim-box-content">
	<form class="sim-form" action="action.php?ah=variavel/adicionarVariavel" method="post" novalidate autocomplete="off" sh-form>
				
		<div class="sim-form-fieldset">
		<fieldset>
			<h3>Dados da variável</h3>
				<?php
				$html = '';
				//nome
				$html .= \Sh\RendererLibrary::renderFieldBox($variavel->getField('nome', false), null, array(
						'id' => 'nome',
						'name' => 'nome',
						'placeholder' => 'Nome',
						'required' => true,
				), array(
						'div' => array(
								'class' => 'sh-w-1'
						)
				));

				//nome acesso
				$html .= \Sh\RendererLibrary::renderFieldBox($variavel->getField('nomeAcesso',false), null, array (
					'id' => 'nomeAcesso',
					'name' => 'nomeAcesso',
					'placeholder' => 'Nome de Acesso',
					), array(
						'div' => array(
								'class' => 'sh-w-1'
						)
				));
				
				//valor
				$html .= \Sh\RendererLibrary::renderFieldBox($variavel->getField('valor',false), null, array (
						'id' => 'valor',
						'name' => 'valor',
						'placeholder' => 'Valor',
						'required' => true
				), array(
						'div' => array(
								'class' => 'sh-w-1'
						)
				));
				
				//tipo da variavel
				$html .= \Sh\RendererLibrary::renderFieldBox($variavel->getField('tipoVariavel',false), null, array (
						'id' => 'tipoVariavel',
						'name' => 'tipoVariavel',
						'placeholder' => 'Tipo',
						'required' => true
				), array(
						'div' => array(
								'class' => 'sh-w-1'
						)
				));
				echo $html;
				
				?>
				
		</fieldset>
		
		</div>
		<div class="sim-btn-1 sim-btn-margin-05">
			<button type="submit">Enviar</button>
		</div>
	</form>
	</div>
	
</section>