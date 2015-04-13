<?php 
	$blogPostDS = \Sh\ModuleFactory::getModuleDataSourceByAlias('blog/post');

?>

<section class="sh-box sh-box-laranja" >

	<header>
		<div><span data-icon="k"></span></div>
		<h1>Novo Post</h1>
	</header>

	<div class="sh-box-content">
	
		<form action="action.php?ah=blog/post_add" class="sh-form" sh-form novalidate sh-form-rh="[sheer/modules/blog][rh.postActionResponse]">
		
			<fieldset>
<!-- 				<h3>Insira as informações da categoria</h3> -->
				<div class="sh-form-fs">
					<?php 
					
						$html = '';
						
						$html .= \Sh\RendererLibrary::renderFieldBox($blogPostDS->getField('idCategoria', false), null, ['placeholder' => 'Categoria', 'blankOption'=>'Selecione a categoria'], ['div'=>['class' => 'sh-w-1']]);
						
						$html .= \Sh\RendererLibrary::renderFieldBox($blogPostDS->getField('titulo', false), null, [
								'placeholder' => 'Título'
							], 
							[
								'div'=>['class'=>'sh-w-1']
							]
						);
						$html .= \Sh\RendererLibrary::renderFieldBox($blogPostDS->getField('chamada', false), null, ['placeholder' => 'Chamada'], ['div'=>['class' => 'sh-w-1']]);

						$html .= \Sh\RendererLibrary::renderFieldBox($blogPostDS->getField('conteudo', false), null, ['required'=> 'required'], ['div'=> ['class' => 'sh-w-1']]);

						$html .= \Sh\RendererLibrary::renderFieldBox($blogPostDS->getField('keywords', false), null, ['placeholder'=> 'Palavras-Chave, separe por vírgula'], ['div'=> ['class' => 'sh-w-1']]);

						$html .= \Sh\RendererLibrary::renderFieldBox($blogPostDS->getField('autor', false), \Sh\AuthenticationControl::getAuthenticatedUserInfo('nome'), ['placeholder'=>'Autor'], ['div'=> ['class' => 'sh-w-300']]);
						
						$html .= \Sh\RendererLibrary::renderFieldBox($blogPostDS->getField('data', false), \Sh\FieldDate::formatInputDataToSheer(date('d/m/Y')), ['datePicker'=>true, 'mask'=>'date', 'validationType'=>'date', 'placeholder'=> 'Data' ], ['div'=> ['class' => 'sh-w-150']]);

						
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

