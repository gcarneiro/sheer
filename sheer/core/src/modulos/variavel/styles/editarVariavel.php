<?php


if( $content['variavel/variavel_detalhes']['total'] < 1 ) {
	var_dump('erro');
	exit;
}

//carregando informações da variavel
$variavelDados = reset($content['variavel/variavel_detalhes']['results']);
//carregando dataSource
$variavelDS = \Sh\ModuleFactory::getModuleDataSource('variavel', 'variavel');

?>


<section class="sh-margin-x-auto sim-box sim-box-action sh-w-1000">
	
	<header>
		<div><span data-icon="c"></span></div>
		<h1>Editar Variável</h1>
	</header>
	
	<div class="sim-box-content">
	<form id="variavel" class="sim-form" action="action.php?ah=variavel/variavel_update" method="post" novalidate autocomplete="off" sh-form>
				
		<div class="sim-form-fieldset">
		<fieldset>
			<h3>Dados da Variável</h3>
			<input type="hidden" name="id" value="<?php echo $variavelDados['id']; ?>" />
				<?php
				$html = '';
				//nome
				$html .= \Sh\RendererLibrary::renderFieldBox($variavelDS->getField('nome', false), $variavelDados['nome'], array(
						'id' => 'nome',
						'name' => 'nome',
						'placeholder' => 'Nome',
						'required' => true,
				), array(
						'div' => array(
								'class' => 'sh-w-1'
						)
				));


				//valor
				$html .= \Sh\RendererLibrary::renderFieldBox($variavelDS->getField('valor',false),$variavelDados['valor'], array (
						'id' => 'mae',
						'name' => 'mae',
						'placeholder' => 'Valor',
						'required' => true
				), array(
						'div' => array(
								'class' => 'sh-w-1'
						)
				));
				//tipoVariavel
				$html .= \Sh\RendererLibrary::renderFieldBox($variavelDS->getField('tipoVariavel',false), $variavelDados['tipoVariavel'], array (
						'id' => 'tipoVariavel',
						'name' => 'tipoVariavel',
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