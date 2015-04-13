<?php
	$campanha = reset($content['malaDiretaCampanha/malaDiretaCampanha_detalhes']['results']);
	$enviosTeste = $content['malaDiretaEnvioTeste/malaDiretaEnvioTeste_lista']['results'];
	$agendamentos = $content['malaDiretaAgendamento/malaDiretaAgendamento_lista']['results'];
	$disparos = $content['malaDiretaDisparo/malaDiretaDisparo_lista'];
	
	//variaveis para controlar a quantidade de visualizacoes, remoções e total de emails por campanha
	$totalVisualizacoes = 0;
	$totalRemocoes = 0;
	$totalEmails = 0;
	$produtividade = 0;
	$ineficiencia = 0;
	
	//TRATANDO INFORMAÇÕES DO DISPARO
	if( $disparos['total'] > 0 ){
		
		foreach ($disparos['results'] as &$disparo){
			
			//aqui defino as variaveis para gravar a quantidade de visualizações unicas, remoções e total de emails por disparo
			$totalVisualizacoes +=$disparo['visualizacoesUnicas'];
			$totalRemocoes += $disparo['remocoes'];
			$totalEmails += $disparo['total'];
			
		}
		
		//calculo a porcentagem de produtividade e ineficiencia
		$produtividade = floor(($totalVisualizacoes * 100) / $totalEmails);
		$ineficiencia  = floor(($totalRemocoes * 100) /$totalEmails);
	}
	
?>

<div class="sh-w-1">

	<!-- Campanha -->
	<section class="sh-box sh-box-azul"> 
<!-- 		<header> -->
<!-- 			<h1>Campanha</h1> -->
<!-- 		</header> -->
		
		<div class="sh-box-content">
			
			<h2 class="data-center">
				<?php echo $campanha['assunto']; ?>
				<div class="data-right" style="float: right; font-size: small;">
					<a title="Ver Html" target="_blank" href="renderer.php?rd=malaDiretaCampanha/ml&htmlResponse=1&idCampanha=<?php echo $campanha['id'] ?>" data-icon="l"></a>
					<a title="Menu" href="#" sh-component="navigation" sh-component-target="navegacaoCampanha" data-id="<?php echo $campanha['id'] ?>" data-icon="d" ></a>
				</div>
			</h2>
			
			<div class="sh-w-1">
				<p><strong>Remetente:</strong> <?php echo $campanha['remetente']['nomeEnvio'].' &lt; '.$campanha['remetente']['emailEnvio'].' &gt;' ?></p>
			</div>
			
			<div class="sh-w-1-3 data-center">
				<p><strong>Emails Enviados</strong></p>
				<p><?php echo $totalEmails ?></p>
			</div>
			
			<div class="sh-w-1-3 data-center">
				<p><strong>Visualizações / Remoções</strong></p>
				<p><?php echo $totalVisualizacoes.' / '.$totalRemocoes ?></p>
			</div>
			
			<div class="sh-w-1-3 data-center">
				<p><strong>Produtividade / Ineficiência</strong></p>
				<p><?php echo $produtividade.'% / '.$ineficiencia.'%'; ?></p>
			</div>
			
			
		</div>
	
	</section>

	<!-- Lado esquerdo -->
	<div class="sh-w-1-2">
	
		<!-- AGENDAMENTOS -->
		<section class="sh-box sh-box-azul">
			<header>
				<h1>Agendamentos</h1>
			</header>
			
			<div class="sh-box-content">
			<?php
				$html = '';
				if( $agendamentos ){

					foreach ( $agendamentos as $id=>$v ){

						//ignoro as campanhas canceladas
						if( $v['status']== 3 ){
							continue;
						}
						$html.='<div>';
							$html .= '<p class="sh-w-1-3"><strong>Agendado para:</strong><br /> '.$v['data']['date'].' '.$v['hora'].'</p>';
							$html .= '<p class="sh-w-1-3"><strong>Lista: </strong><br /> '.$v['lista']['nome'].'</p>';
							$html .= '<p class="sh-w-1-4"><strong>Status: </strong><br /> '.$v['status_lookup'].'</p>';
							$html .= '<div class="sh-w-1-12 data-right">';
								$html .= '<a title="Detalhes do Agendamento" href="renderer.php?rd=malaDiretaAgendamento/detalhesAgendamento&id='.$v['id'].'" sh-component="overlayLink" data-icon="l"></a>';
								$html .= '<a title="Cancelar Agendamento" href="action.php?ah=malaDiretaAgendamento/cancelarAgendamento&id={id}" sh-component="action" sh-comp-confirm sh-comp-confirmMessage="Deseja realmente cancelar esse agendamento?" data-icon="x" data-id="'.$v['id'].'"></a>';
							$html .= '</div>';
						$html .= '</div>';
						
					}
				}
				//verifico aqui se a variavel de html está vazia porque eu posso ter agendamentos cancelados que foram ignorados
				if($html == ''){
					$html.='<div class="data-center">';
						$html.='<p data-icon="g" style="font-size: 1.3em;"></p>';
						$html.='<p>Não possuímos nenhum agendamento pendente!</p>';
					$html.='</div>';
				}
				
				echo $html;
			?>
			</div>
		</section>
		
		<!-- TESTE DE ENVIO -->
		<section class="sh-box sh-box-azul">
			<header>
				<h1>Testes de Envio</h1>
			</header>
			
			<div class="sh-box-content">
				<?php
					$html = '';
					if( $enviosTeste ){
						
						foreach ($enviosTeste as $v){
							$html .= '<div>';
								$html.='<p class="sh-w-2-5"><strong>Enviado em:</strong><br /> '.$v['envioEm']['datetime'].'</p>';
								$html.='<p class="sh-w-2-5"><strong>Enviado Por:</strong><br /> '.$v['user']['nome'].' </p>';
								$html.='<div class="sh-w-1-5" style="text-align:right">';
								$html .= '<a title="Detalhes do envio" href="renderer.php?rd=malaDiretaEnvioTeste/detalhesTesteEnvio&id='.$v['id'].'" sh-component="overlayLink" data-icon="l"></a>';
								$html.='</div>';
							$html .= '</div>';
						}
					}
					else {
						$html.='<div class="data-center">';
							$html.='<p data-icon="g" style="font-size: 1.3em;"></p>';
							$html.='<p>Ainda não temos nenhum teste realizado!</p>';
						$html.='</div>';
					}
					echo $html;
				?>
			</div>
		</section>
	
	</div>
	
	<!-- Lado esquerdo -->
	<div class="sh-w-1-2">
	<?php
		$html = '';
		if( $disparos['total'] > 0 ){
			
			foreach ( $disparos['results'] as $id=>$v ) {

				$produtividade = 0;
				$ineficiencia = 0;
				$totalVisualizacoes		 = $v['visualizacoesUnicas'];
				$totalRemocoes			 = $v['remocoes'];
				
				$totalEmails			 = $v['total'];
				//calculo a porcentagem de produtividade e ineficiencia para cada disparo
				$produtividade 		= floor(($totalVisualizacoes * 100) / $totalEmails);
				$ineficiencia 		= floor(($totalRemocoes * 100) /$totalEmails);
				
				$html .= '<section class="sh-box"> ';
					$html .= '<header class="data-center">';
						$html .= '<h1>Envio realizado - '.$v['disparadoEm']['day'].'/'.$v['disparadoEm']['monthNameAbbr'].'/'.$v['disparadoEm']['year'].' - '.$v['disparadoEm']['time'].'</h1>';
						$html .= '<a title="Visualizar html" target="_blank" href="renderer.php?rd=malaDiretaCampanha/ml&htmlResponse=1&idDisparo='.$v['id'].'" data-icon="l"></a>';
					$html .= '</header>';
					
					$html .= '<div class="sh-box-content">';
					
						$html .= '<div>';
							$html .= '<p class="sh-w-1-2"><strong>Assuto:</strong><br /> '.$v['assunto'].' </p>';
							$html .= '<p class="sh-w-1-2 data-right"><strong>Lista de E-mail:</strong><br /> '.$v['lista']['nome'].' </p>';
							$html .= '<p class="sh-w-1"><strong>Remetente:</strong><br /> '.$v['remetente']['nomeEnvio'].' &lt; '.$v['remetente']['emailEnvio'].' &gt; </p>';
							
							$html .= '<div class="sh-w-1-3 data-center">';
								$html .= '<p><strong>Emails Enviados</strong><br/> '.$v['total'].' </p>';
							$html .= '</div>';
							
							$html .= '<div class="sh-w-1-3 data-center">';
								$html .= '<p><strong>Visualizações</strong><br/> '.$v['visualizacoesUnicas'].' </p>';
							$html .= '</div>';
							
							$html .= '<div class="sh-w-1-3 data-center">';
								$html .= '<p><strong>Produtividade</strong><br/> '.$produtividade.'%</p>';
							$html .= '</div>';
							
							$html .= '<div class="sh-w-1-3 data-center">';
								$html .= '<p><strong>--</strong><br/></p>';
							$html .= '</div>';
							
							$html .= '<div class="sh-w-1-3 data-center">';
								$html .= '<p><strong>Remoções</strong><br/>'.$v['remocoes'].'</p>';
							$html .= '</div>';
							
							$html .= '<div class="sh-w-1-3 data-center">';
								$html .= '<p><strong>Ineficiência</strong><br/>'.$ineficiencia.'%</p>';
							$html .= '</div>';
							
						$html .= '</div>';
					$html .= '</div>';
				$html .= '</section>';
			}
		}
		echo $html;
	 ?>
	</div>

</div>


<!-- MENU DA CAMPANHA -->
<template id="navegacaoCampanha"> 
	<nav class="sheer-nav-inline" data-id="{id}">
		<header></header>
		<div>
			<ul>
				<li><a href="renderer.php?rd=malaDiretaCampanha/adicionarAgendamento&id={id}" sh-component="overlayLink">Agendar Envio</a></li>
				<li><a href="renderer.php?rd=malaDiretaEnvioTeste/enviarTeste&id={id}" sh-component="overlayLink">Enviar Teste</a></li>
				<li><a href="renderer.php?rd=malaDiretaCampanha/dispararCampanha&id={id}" sh-component="overlayLink">Disparar E-mails</a></li>
				<li><a href="renderer.php?rd=malaDiretaCampanha/editarCampanha&id={id}" sh-component="overlayLink">Editar</a></li>
				<li><a href="action.php?ah=malaDiretaCampanha/removerCampanha&id={id}" sh-component="action" 
				sh-comp-rh="[currentPage][campanhaDashboard.removerCampanhaResponse]">Remover</a></li>
			</ul>
		</div>
		<footer></footer>
	</nav>
</template>


<?php 
	return ;
?>
<div class="sh-margin-x-auto sh-w-min-800"> 
		
	<div class="sh-w-1-2" style="vertical-align: top;" >
		
		
	<div class="sh-w-1-2" style="vertical-align: top;" >
	
		
	</div>
	
</div>
