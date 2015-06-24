<?php
	$picture = $content['imageRepository/picture_detalhes'];
	if( $picture['total'] != 1 ) {
		return;
	}
	$picture = reset($picture['results']);
?>
<section class="sh-box sh-box-verde sh-w-700 sh-margin-x-auto">

	<header>
		<div><span data-icon="D"></span></div>
		<h1>Insira a legenda para a foto</h1>
	</header>

	<div class="sh-box-content">
		
		<form class="sh-form" action="action.php?ah=imageRepository/picture_update" method="post" novalidate autocomplete="off" sh-form sh-form-rh="[sheer/adm][imageRepository.rh.alterarLegenda]">
			<fieldset>
			
				<div class="data-center sh-w-150 sh-w-ib">
					<?php 
						$tbm = &$picture['idPicture']['pictures']['sheer']['sh_tbm'];
						echo '<img src="'.$tbm['downloadLink'].'" style=" width: '.$tbm['width'].'px; height: '.$tbm['height'].'px;" />';
					?>
					<div class="sh-btn-holder">
						<button type="submit" class="sh-btn-verde">Salvar</button>
					</div>
				</div>
				
				<div class="sh-w-500 sh-w-ib">
					<input type="hidden" name="id" required="required" value="<?php echo $picture['id']; ?>" />
					<div class="sh-form-field sh-w-1">
						<textarea name="legenda" style="height: 150px;" placeholder="Escreva a leganda aqui"><?php echo $picture['legenda']; ?></textarea>
					</div>
				</div>
				
				
			
			</fieldset>
		
		</form>
		
	</div>


</section>