<?php
	$agendamento = reset($content['malaDiretaAgendamento/malaDiretaAgendamento_detalhes']['results']);
?>
<section class="sh-box sh-w-600 sh-margin-x-auto">
	<header>
		<h1>Detalhes do agendamento</h1>
	</header>
	
	<div class="sh-box-content">
		<p class="sh-w-1">
			<strong>Remetente: </strong> <?php echo $agendamento['remetente']['nomeEnvio'].' &lt; '.$agendamento['remetente']['emailEnvio'].' &gt;' ?>
		</p>
		<p class="sh-w-1">
			<strong>Assunto: </strong> <?php echo $agendamento['assunto'] ?>
		</p>
		<p class="sh-w-1">
			<strong>Lista:</strong> <?php echo $agendamento['lista']['nome'].' ('.$agendamento['lista']['totalHabilitados'].' emails)'; ?>
		</p>
		<?php 
			$html = '<p class="sh-w-1">';
			if( $agendamento['status'] == '1' ) {
				$html .= '<strong>Disparado em:</strong> '.$agendamento['data']['date'].' '.$agendamento['hora'];
			}
			else if ( $agendamento['status'] == '2' ) {
				$html .= '<strong>Agendado para:</strong> '.$agendamento['data']['date'].' '.$agendamento['hora'];
			}
			$html .= '</p>';
			echo $html;
		
		?>
		<div class="sh-btn-holder">
			<a target="_blank" href="renderer.php?rd=malaDiretaCampanha/ml&htmlResponse=1&idAgendamento=<?php echo $agendamento['id'] ?>">Ver Html</a>
		</div>
	</div>
</section>