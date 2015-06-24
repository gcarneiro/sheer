<?php 

	$variavel = $content['variavel/listaFiltro'];
	
?>
<section class="sh-box">

	<header>
		<div data-icon="d"></div>
		
		<h1>Variáveis</h1>
		
		<a href="renderer.php?rd=variavel/adicionarVariavel" sh-component="overlayLink" data-icon="k" class="adicionarItem"></a>
		
		<div><?php echo $variavel['total'].'/'.$variavel['available']; ?></div>
		
	</header>
	
	<div class="sh-box-content">
	
		<table class="sh-table sh-table-admin">
		
			<thead>
				<tr>
					<th><input type="checkbox" /></th>
					<th>Nome</th>
					<th>Nome de Acesso</th>
					<th>Valor</th>
					<th>Tipo da variável</th>
					<th class="sh-w-50"></th>
				</tr>
			</thead>
			
			<tbody>
			
				<?php 
					$html = '';
					if( $variavel['total'] > 0 ) {
						
						foreach ( $variavel['results'] as $idVariavel=>$var ) {
							$html .= '<tr data-id="'.$var['id'].'" data-content>';
								$html .= '<td><input type="checkbox" /></td>';
								$html .= '<td>'.$var['nome'].'</td>';
								$html .= '<td>'.$var['nomeAcesso'].'</td>';
								$html .= '<td>'.$var['valor'].'</td>';
								$html .= '<td>'.$var['tipoVariavel_lookup'].'</td>';
								$html .= '<td><a href="#" sh-component="navigation" sh-component-target="unidadeElementoAcao" data-icon="d" data-id="'.$var['id'].'"></a></td>';
							$html .= '</tr>';

						}
						
						echo $html;

					}
				?>
			</tbody>
		
		</table>
			
		<?php 
			$html = '';
			if( $variavel['total'] == 0 ) {
				$html = '<div class="data-center">';
					$html .= '<p data-icon="g" style="font-size: 1.3em;"></p>';
					$html .= '<p style="text-transform: uppercase">Nenhum resultado encontrado</p>';
				$html .= '</div>';
				echo $html;
			}
		?>

	</div>
	
</section>

<template id="unidadeElementoAcao">
	<nav class="sheer-nav-inline" data-id="{id}">
		<header></header>
		<div>
			<ul>
				<li><a href="renderer.php?rd=variavel/editarVariavel&id={id}" sh-component="overlayLink">Editar</a></li>
				<li><a href="#" sh-component="action" sh-component-target="action.php?ah=variavel/variavel_delete&id={id}">Remover</a></li>
			</ul>
		</div>
		<footer></footer>
	</nav>
</template>
