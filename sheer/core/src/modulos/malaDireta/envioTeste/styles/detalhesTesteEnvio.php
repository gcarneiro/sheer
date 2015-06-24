<?php
	$envioTeste = reset($content['malaDiretaEnvioTeste/malaDiretaEnvioTeste_detalhes']['results']);
	$emails = preg_split("/[\;\,]/", $envioTeste['destinos'] );
?>
<section class="sh-box sh-w-600 sh-margin-x-auto">
	<header>
		<h1>Detalhes do teste de envio</h1>
	</header>
	
	<div class="sh-box-content">
		<p class="sh-w-1">
			<strong>Remetente: </strong> <?php echo $envioTeste['remetente']['nomeEnvio'].' &lt; '.$envioTeste['remetente']['emailEnvio'].' &gt;' ?>
		</p>
	
		<p class="sh-w-1"><strong>Assunto: </strong> <?php echo $envioTeste['assunto'] ?></p>
		<p class="sh-w-1"><strong>Envio em:</strong> <?php echo $envioTeste['envioEm']['datetime'] ?></p>
		
		<p class="sh-w-1-3"><strong>Destinos: </strong><br />
			<?php foreach ($emails as $v){echo $v.'<br/>';} ?> 
		</p>
		
		<div class="sh-btn-holder">
			<a target="_blank" href="renderer.php?rd=malaDiretaCampanha/ml&htmlResponse=1&idTesteEnvio=<?php echo $envioTeste['id'] ?>">Ver Html</a>
		</div>
	</div>
</section>