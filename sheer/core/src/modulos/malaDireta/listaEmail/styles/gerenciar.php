<?php
	$listaEmails = $content['malaDiretaListaEmail/malaDiretaListaEmail_lista'];
?>

<section class="sh-box">

	<header>
		<div><span data-icon="a"></span></div>
		
		<h1>E-mails</h1>
		
		<a href="#" sh-component="navigation" sh-component-target="navegacaoHeaderEmails" data-id="<?php echo $requestParameters['idLista'] ?>" data-icon="k" ></a>
		<a href="#" sh-component="navigation" sh-component-target="navegacaoHabilitarMultiplo" data-id="<?php echo $requestParameters['idLista'] ?>" data-icon="d" ></a>
		
		<div><?php echo $listaEmails['total'].'/'.number_format($listaEmails['available'], 0, ',', '.'); ?></div>
		
	</header>
	
	<div class="sh-box-content">
	
		<table id="emails" class="sh-table sh-table-admin">
		
			<thead>
				<tr>
					<th><input type="checkbox" /></th>
					<th>E-mail</th>
					<th>Nome</th>
					<th class="data-center sh-w-100">Habilitado</th>
					<th class="sh-w-100"></th>
				</tr>
			</thead>
			
			<tbody>
				<?php
					$html = '';
					if( $listaEmails['total'] > 0 ) {
							
						foreach ( $listaEmails['results'] as $id=>$email ) {
							
							$nomeStr = '--';
							if($email['contato']['nome']){
								$nomeStr = $email['contato']['nome'];
							}
								
							$html .= '<tr data-id="'.$email['id'].'" data-content>';
								$html .= '<td><input type="checkbox" name="itemId[]" value="'.$email['id'].'" sh-check /></td>';
								$html .= '<td>'.$email['contato']['email'].'</td>';
								$html .= '<td>'.$nomeStr.'</td>';
								$html .= '<td class="data-center">'.$email['enviar_lookup'].'</td>';
								$html .= '<td class="data-right">';
								if($email['enviar']==1){
									//sh-comp-rh="[currentPage][malaDiretaEmails.habilitarDesabilitarResponse]
									$html .= '<a href="action.php?ah=malaDiretaListaEmail/habilitarDesabilitarEmail&enviar=2&id={id}" sh-component="action" sh-comp-rh="[sheer/modules/maladireta][rh.desabilitarEmail]" data-icon="x"></a>';
								}
								else{
									//sh-comp-rh="[currentPage][malaDiretaEmails.habilitarDesabilitarResponse]"
									$html .= '<a href="action.php?ah=malaDiretaListaEmail/habilitarDesabilitarEmail&enviar=1&id={id}" sh-component="action" sh-comp-rh="[sheer/modules/maladireta][rh.habilitarEmail]" data-icon="A"></a>';
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


<!-- NAVIGATION DA HEADER DA LISTA -->
<template id="navegacaoHeaderEmails">
	<nav class="sheer-nav-inline" data-id="{id}">
		<header></header>
		<div>
			<ul>			
				<li><a href="renderer.php?rd=malaDiretaListaEmail/adicionarEmail&idLista=<?php echo $requestParameters['idLista'] ?>" sh-component="overlayLink">Adicionar um</a></li>
				<li><a href="renderer.php?rd=malaDiretaListaEmail/adicionarVariosEmails&idLista=<?php echo $requestParameters['idLista'] ?>" sh-component="overlayLink">Adicionar vários</a></li>
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
						sh-comp-rh="[sheer/modules/maladireta][rh.habilitarEmail]">Habilitar Selecionados</a>
				</li>
				<li>
					<a href="action.php?ah=malaDiretaListaEmail/habilitarDesabilitarEmail&enviar=2&idLista={id}" sh-component="action" sh-comp-content="n" sh-comp-contentNode="emails"
						sh-comp-rh="[sheer/modules/maladireta][rh.habilitarEmail]">Desabilitar Selecionados</a>
				</li>
			</ul>
		</div>
		<footer></footer>
	</nav>
</template>
