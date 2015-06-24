<?php 
	$blogCategoriaDS = \Sh\ModuleFactory::getModuleDataSourceByAlias('blog/categoria');
	$categoria = reset($content['blog/categoria_detalhes']['results']);

?>

<section class="sh-box sh-box-laranja sh-margin-h sh-w-600" >

	<div class="sh-box-content">
	
		<form action="action.php?ah=blog/categoria_update" class="sh-form" sh-form novalidate>
		
			<fieldset>
				<h3>Insira as informações da categoria</h3>
				<div class="sh-form-fs">
					<?php 
					
						$html = '';
						$html .= '<input type="hidden" name="id" required="required" value="'.$categoria['id'].'" />';
						$html .= \Sh\RendererLibrary::renderFieldBox($blogCategoriaDS->getField('titulo', false), $categoria['titulo'], [
							'placeholder' => 'Título'
						], ['div'=>['class'=>'sh-w-1']]);
						$html .= \Sh\RendererLibrary::renderFieldBox($blogCategoriaDS->getField('posicao', false), $categoria['posicao'], [
								'placeholder' => 'Posição',
								'mask' => 'inteiro',
								'validationType' => 'number',
								'data-number-min' => 0
							], [
							'div'=>[
								'class' => 'sh-w-100'
							]
						]);
						
						echo $html;
					
					?>
					
				</div>
				
				<div class="sh-btn-holder">
					<button type="submit" class="sh-btn-laranja-i">Salvar</button>
				</div>
			
			</fieldset>
			
			
		
		</form>
	
	</div>

</section>

