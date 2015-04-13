<?php 
	$blogPostDS = \Sh\ModuleFactory::getModuleDataSourceByAlias('blog/post');
	$post = reset($content['blog/post_detalhes']['results']);

?>

<section class="sh-box sh-box-laranja" >

	<header>
		<div><span data-icon="k"></span></div>
		<h1>Visualizar Post</h1>
		<div>
			<a href="?p=blog/post/editar&id=<?php echo $post['id']; ?>" data-icon="s" title="Editar" ></a>
		</div>
	</header>

	<div class="sh-box-content">
	
		<?php 
			$html = '';

			$html .= '<h2>'.$post['titulo'].'</h2>';
			$html .= '<h5>Por: '.$post['autor'].'</h5>';
			$html .= '<h5>Em: '.$post['data']['date'].'</h5>';

			$html .= '<p style="margin-bottom: 2em;">'.$post['chamada'].'</p>';
			$html .= '<div style="overflow: auto">';
				$html .= $post['conteudo'];
			$html .= '</div>';
			
			$html .= '<p><strong>Palavras-Chave:</strong> '.$post['keywords'].'</p>';
			
			echo $html;
			
			
		?>
	
	</div>

</section>

