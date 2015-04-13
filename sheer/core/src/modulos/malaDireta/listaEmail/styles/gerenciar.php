<?php
	$listaEmails = $content['malaDiretaListaEmail/emailsPorLista'];
	$lista = reset($content['malaDiretaLista/malaDiretaLista_detalhes']['results']);
?>

<section class="sh-box">

	<header>
		<div><span data-icon="a"></span></div>
		
		<h1>E-mails</h1>
		
		<a href="#" sh-component="navigation" sh-component-target="navegacaoHeaderEmails" data-id="<?php echo $lista['id'] ?>" data-icon="k" ></a>
		<a href="#" sh-component="navigation" sh-component-target="navegacaoHabilitarMultiplo" data-id="<?php echo $lista['id'] ?>" data-icon="d" ></a>
		
		<div><?php echo $listaEmails['total'].'/'.number_format($listaEmails['available'], 0, ',', '.'); ?></div>
		
	</header>
	
	<div class="sh-box-content">
	
		<table id="emails" class="sh-table sh-table-admin">
		
			<thead>
				<tr>
					<th><input type="checkbox" /></th>
					<th>Nome</th>
					<th class="data-center">E-mail</th>
					<th class="data-center sh-w-100">Habilitado</th>
					<th class="sh-w-100"></th>
				</tr>
			</thead>
			
			<tbody>
				<?php
					$html = '';
					if( $listaEmails['total'] > 0 ) {
							
						foreach ( $listaEmails['results'] as $id=>$email ) {
								
							$html .= '<tr data-id="'.$email['id'].'" data-content>';
								$html .= '<td><input type="checkbox" name="itemId[]" value="'.$email['id'].'" sh-check /></td>';
								if($email['nome']){
									$html .= '<td>'.$email['nome'].'</td>';
								}
								else{
									$html .= '<td>--</td>';
								}
								$html .= '<td class="data-center">'.$email['email'].'</td>';
								$html .= '<td class="data-center">'.$email['enviar_lookup'].'</td>';
								$html .= '<td class="data-right">';
								if($email['enviar']==1){
									$html .= '<a href="navegacaoEmailHabilitado" sh-component="navigation" data-id="'.$email['id'].'" data-icon="d"></a>';
								}
								else{
									$html .= '<a href="navegacaoEmailDesabilitado" sh-component="navigation" data-id="'.$email['id'].'" data-icon="d"></a>';
								}
								$html .= '</td>';
							$html .= '</tr>';
						}
							
						echo $html;
					}
				?>
			</tbody>
		
		</table>
		
		<?php 
			if( $listaEmails['total'] == 0 ) {
				echo \Sh\RendererLibrary::getEmptyHolderHtml();
			}
		?>
			
	</div>
	
</section>

<!-- NAVIGATION DA LISTA DE EMAILS PARA REGISTROS HABILITADOS -->
<template id="navegacaoEmailHabilitado">
	<nav class="sheer-nav-inline" data-id="{id}">
		<header></header>
		<div>
			<ul>			
				<li><a href="action.php?ah=malaDiretaListaEmail/habilitarDesabilitarEmail&enviar=2&id={id}" sh-component="action"
				sh-comp-rh="[currentPage][malaDiretaEmails.habilitarDesabilitarResponse]">Desabilitar</a></li>
				<li><a href="renderer.php?rd=malaDiretaListaEmail/editarEmail&id={id}" sh-component="overlayLink">Editar</a></li>
			</ul>
		</div>
		<footer></footer>
	</nav>
</template>

<!-- NAVIGATION DA LISTA DE EMAILS PARA REGISTROS DESABILITADOS -->
<template id="navegacaoEmailDesabilitado">
	<nav class="sheer-nav-inline" data-id="{id}">
		<header></header>
		<div>
			<ul>			
				<li><a href="action.php?ah=malaDiretaListaEmail/habilitarDesabilitarEmail&enviar=1&id={id}" sh-component="action"
					sh-comp-rh="[currentPage][malaDiretaEmails.habilitarDesabilitarResponse]">Habilitar</a></li>
				<li><a href="renderer.php?rd=malaDiretaListaEmail/editarEmail&id={id}" sh-component="overlayLink">Editar</a></li>
			</ul>
		</div>
		<footer></footer>
	</nav>
</template>

<!-- NAVIGATION DA HEADER DA LISTA -->
<template id="navegacaoHeaderEmails">
	<nav class="sheer-nav-inline" data-id="{id}">
		<header></header>
		<div>
			<ul>			
				<li><a href="renderer.php?rd=malaDiretaListaEmail/adicionarEmail&idLista=<?php echo $lista['id'] ?>" sh-component="overlayLink">Adicionar um</a></li>
				<li><a href="renderer.php?rd=malaDiretaListaEmail/adicionarVariosEmails&idLista=<?php echo $lista['id'] ?>" sh-component="overlayLink">Adicionar vários</a></li>
			</ul>
		</div>
		<footer></footer>
	</nav>
</template>

<!-- NAVIGATION HEADER para habilitar/desabilitar os selecionados -->
<template id="navegacaoHabilitarMultiplo">
	<nav class="sheer-nav-inline" data-id="{id}">
		<header></header>
		<div>
			<ul>
				<li>
					<a href="action.php?ah=malaDiretaListaEmail/habilitarDesabilitarEmail&enviar=1&idLista={id}" sh-component="action" sh-comp-content="n" sh-comp-contentNode="emails"
						sh-comp-rh="[currentPage][malaDiretaEmails.habilitarDesabilitarResponse]">Habilitar Selecionados</a>
				</li>
				<li>
					<a href="action.php?ah=malaDiretaListaEmail/habilitarDesabilitarEmail&enviar=2&idLista={id}" sh-component="action" sh-comp-content="n" sh-comp-contentNode="emails"
					sh-comp-rh="[currentPage][malaDiretaEmails.habilitarDesabilitarResponse]">Desabilitar Selecionados</a>
				</li>
			</ul>
		</div>
		<footer></footer>
	</nav>
</template>
