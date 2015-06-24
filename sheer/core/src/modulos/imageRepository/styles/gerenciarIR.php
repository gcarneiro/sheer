<?php

//BUSCANDO O IMAGE REPOSITORY
$data = $content['imageRepository/gerenciarIR'];

//determinando se temos os conteudos todos
if( $data['available'] == 0 ) {
	echo '<h3>Dados do Repositório de imagens são inválidos</h3>';
	return;
}

$imageRepository = &$data['results']['imageRepository'];
$content = &$data['results']['content'];
?>

<section class="sh-box">
	
	<div class="sh-box-content">
		<div class="sh-w-ib" style="margin-right: 0.5em; vertical-align: middle;">
			<?php 
				$coverImage = array('path'=>null, 'width'=>0, 'height'=>0);
				$display = 'display: none;';
				if( $imageRepository['capa'] ) {
					$coverImage = $imageRepository['capa']['idPicture']['pictures']['sheer']['sh_tbm'];
					$display = '';
				}
				echo '<img id="imagemCapa" src="'.$coverImage['path'].'" style="width: '.$coverImage['width'].'px; height: '.$coverImage['height'].'px; '.$display.'" />';
			?>
		</div>
		<div class="sh-w-ib" style="vertical-align: middle;">
			<h3><?php echo $content['nome'] ?><br /><small style="font-size: 0.7em;">Repositório de imagens</small></h3>
		</div>
		
	</div>

</section>

<section class="sh-box sh-box-verde sheer-imrep" data-idRepository="<?php echo $imageRepository['id']; ?>">
	<header>
		<div><span data-icon="i"></span></div>
		<h1>Galeria <small></small></h1>
		<a href="#" class="sheer-imrep-adicionarImagens" data-icon="k" title="Enviar Imagens"></a>
		<div><?php echo $imageRepository['quantidade']; ?> imagens</div>
		<a href="action.php?ah=imageRepository/picture_deleteAll" data-id="<?php echo $imageRepository['id']; ?>" title="Remover todas as imagens do álbum" data-icon="C" sh-component="action" sh-comp-confirm sh-comp-confirmMessage="Você irá remover todas as imagens do álbum, tem certeza desta operação?"></a>
	</header>
	
	<div class="sh-box-content">
	
		<ul data-idRepository="<?php echo $imageRepository['id']; ?>">
			<?php 
				if( $imageRepository['imagens'] ) {
					$html = '';
					foreach ( $imageRepository['imagens'] as $idPicture=>$picture ) {
						$tmpImg = &$picture['pictures']['sheer'];
						$legenda = '';
						if( $picture['legenda'] ) {
							$legenda = 'data-legenda="'.stripslashes($picture['legenda']).'"';
						}
						
						//este mesmo estilo esta explicito no js: 'sheer/imageRepository'
						$html .= '<li data-id="'.$picture['id'].'" draggable="true" class="sh-imrep-cesto" '.$legenda.' >';
							$html .= '<div>';
								$html .= '<div class="sheer-imrep-overlay">';
									$html .= '<a href="renderer.php?rd=imageRepository/alterarLegenda&id='.$picture['id'].'" title="Alterar legenda" data-icon="D" sh-component="overlayLink"></a>';
									$html .= '<a href="action.php?ah=imageRepository/marcarCapa" title="Definir como capa do álbum" data-icon="I" sh-component="action" sh-comp-rh="[sheer/adm][imageRepository.rh.definirCapa]" sh-comp-confirm sh-comp-confirmMessage="Deseja realmente marcar esta imagem como capa?"></a>';
									$html .= '<a href="action.php?ah=imageRepository/picture_delete" title="Remover imagem" data-icon="C" sh-component="action" sh-comp-rh="[sheer/adm][imageRepository.rh.removerImagem]" sh-comp-confirm sh-comp-confirmMessage="Deseja realmente remover esta imagem?"></a>';
								$html .= '</div>';
								$html .= '<a href="'.$tmpImg['sh_medium']['downloadLink'].'">';
									$html .= '<img src="'.$tmpImg['sh_tbm']['downloadLink'].'" style="width: '.$tmpImg['sh_tbm']['width'].'px; height: '.$tmpImg['sh_tbm']['height'].'px;" />';
								$html .= '</a>';
							$html .= '</div>';
						$html .= '</li>';
					}
					echo $html;
				}
			?>
		</ul>
		
		<div class="droppableZone">
			<div class="sh-w-ib">
				<span data-icon="K"></span>
				<p>Solte suas imagens aqui</p>
			</div>
		</div>
		
	</div>

</section>

<script>
	//Carregando script necessário
	require(['sheer/adm'], function (sheerAdm) {
		sheerAdm.imageRepository.init();
	});		
</script>
