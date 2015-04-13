<?php 
	$blogPostDS = \Sh\ModuleFactory::getModuleDataSourceByAlias('blog/post');
	$post = reset($content['blog/post_detalhes']['results']);

?>

<section class="sh-box sh-box-laranja" >

	<header>
		<div><span data-icon="k"></span></div>
		<h1>Novo Post</h1>
	</header>

	<div class="sh-box-content">
	
		<form action="action.php?ah=blog/post_update" class="sh-form" sh-form novalidate sh-form-rh="[sheer/modules/blog][rh.postActionResponse]">
		
			<fieldset>
<!-- 				<h3>Insira as informações da categoria</h3> -->
				<div class="sh-form-fs">
					<?php 
					
						$html = '';
						$html .= '<input type="hidden" name="id" required="requried" value="'.$post['id'].'" />';
						
						$html .= \Sh\RendererLibrary::renderFieldBox($blogPostDS->getField('idCategoria', false), $post['idCategoria'], ['placeholder' => 'Categoria', 'blankOption'=>'Selecione a categoria'], ['div'=>['class' => 'sh-w-1']]);
						
						$html .= \Sh\RendererLibrary::renderFieldBox($blogPostDS->getField('titulo', false), $post['titulo'], [
								'placeholder' => 'Título'
							], 
							[
								'div'=>['class'=>'sh-w-1']
							]
						);
						$html .= \Sh\RendererLibrary::renderFieldBox($blogPostDS->getField('chamada', false), $post['chamada'], ['placeholder' => 'Chamada'], ['div'=>['class' => 'sh-w-1']]);

						$html .= \Sh\RendererLibrary::renderFieldBox($blogPostDS->getField('conteudo', false), $post['conteudo'], ['required'=> 'required'], ['div'=> ['class' => 'sh-w-1']]);

						$html .= \Sh\RendererLibrary::renderFieldBox($blogPostDS->getField('keywords', false), $post['keywords'], ['placeholder'=> 'Palavras-Chave, separe por vírgula'], ['div'=> ['class' => 'sh-w-1']]);

						$html .= \Sh\RendererLibrary::renderFieldBox($blogPostDS->getField('autor', false), $post['autor'], ['placeholder'=>'Autor'], ['div'=> ['class' => 'sh-w-300']]);
						
						$html .= \Sh\RendererLibrary::renderFieldBox($blogPostDS->getField('data', false), $post['data'], ['datePicker'=>true, 'mask'=>'date', 'validationType'=>'date', 'placeholder'=> 'Data' ], ['div'=> ['class' => 'sh-w-150']]);

						
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

