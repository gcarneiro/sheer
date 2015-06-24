<?php
	$variavel = $content['variavel/variavel_lista'];
	
	$localidadeUf = $content['localidade/uf_lista'];
?>

<section class="sh-box sh-box-verde">

	<header>
		<div><span data-icon="d"></span></div>
		<h1>Gerenciar variáveis em lista</h1>
	</header>
	
	
	<div class="sh-box-content">
	
		<form class="sh-form sh-form-verde" action="action.php?ah=variavel/salvarListaGerenciavel" method="post" novalidate sh-form sh-form-rh="[sheer/modules/variavel][salvarListaGerenciavel]" >
			
			<fieldset>
				<h3>Localidade Padrão</h3>
				
				<div class="sh-form-fs">
				
					<!-- ESTADO -->	
					<div class="sh-w-1">
						
						<div class="sh-w-4-5">
							<p><strong><?php echo $variavel['results']['67beecce2c49db7706c61b0c0bbf2afc']['nome'] ?></strong></p>
							<small>Define o Estado padrão a ser usado no sistema</small>
						</div>
						<div class="sh-form-field sh-w-1-5 data-right">
							<select name="variavel[67beecce2c49db7706c61b0c0bbf2afc]" sh-localidade-role="estado" sh-localidade-rel="variavel" >
								<?php
									echo \Sh\RendererLibrary::doHtmlSelectOptions($localidadeUf['results'], $variavel['results']['67beecce2c49db7706c61b0c0bbf2afc']['valor'], 'id', 'nome', 'Selecione');
									
								?>
							</select>
						</div>
					</div>
					
					<hr />
					
					<!-- CIDADE -->	
					<div class="sh-w-1">
						
						<div class="sh-w-4-5">
							<p><strong><?php echo $variavel['results']['760b30d702f343395a9a6108232aeae7']['nome'] ?></strong></p>
							<small>Define a Cidade padrão a ser usado no sistema</small>
						</div>
						<div class="sh-form-field sh-w-1-5 data-right">
							<select name="variavel[760b30d702f343395a9a6108232aeae7]" sh-localidade-role="cidade" sh-localidade-rel="variavel" >
								<?php
									$localidadeCidade = \Sh\ContentProviderManager::loadContent('localidade/listaCidadesPorEstado',array('idUf'=>$variavel['results']['67beecce2c49db7706c61b0c0bbf2afc']['valor']));
									echo  \Sh\RendererLibrary::doHtmlSelectOptions($localidadeCidade['results'], $variavel['results']['760b30d702f343395a9a6108232aeae7']['valor'],'id','nome','Selecione');
								?>
							</select>
						</div>
					</div>
					
					<hr />
				
					<!-- CEP -->
					<div class="sh-w-1">
						<div class="sh-w-4-5">
							<p><strong><?php echo $variavel['results']['760b30d702f343395a9a6108232aftgj']['nome'] ?></strong></p>
							<small>Define o CEP padrão a ser usado no sistema</small>
						</div>
						
						<div class="sh-form-field sh-w-1-5 data-right">
							<input type="text" mask="cep" value="<?php echo $variavel['results']['760b30d702f343395a9a6108232aftgj']['valor'] ?>" name="variavel[760b30d702f343395a9a6108232aftgj]" />
						</div>
					</div>
									
				</div>

			</fieldset>
			
			<fieldset>
				<h3>Configurações de sistema</h3>
				
				<div class="sh-form-fs">
				
					<!-- Email default do sistema -->	
					<div class="sh-w-1">
						<div class="sh-w-4-5">
							<p><strong><?php echo $variavel['results']['AAD51EC7-819C-4A5D-B531-2F29D9D2FAF0']['nome'] ?></strong></p>
							<small>Determina qual a verificação de permissionamento padrão a ser assumida pelos ActionHandlers</small>
						</div>
						<div class="sh-form-field sh-w-1-5 data-right">
							<select name="variavel[AAD51EC7-819C-4A5D-B531-2F29D9D2FAF0]" >
								<option value="denyGuest">Negar guests</option>
								<option value="denyAll">Negar a todos</option>
								<option value="acceptAll">Aceitar todos</option>
							</select>
						</div>
					</div>
					
					<hr />
				
					<!-- ActionHandler Permissão Padrão -->	
					<div class="sh-w-1">
						<div class="sh-w-4-5">
							<p><strong><?php echo $variavel['results']['AAD51EC7-819C-4A5D-B531-2F29D9D2FAF0']['nome'] ?></strong></p>
							<small>Determina qual a verificação de permissionamento padrão a ser assumida pelos ActionHandlers</small>
						</div>
						<div class="sh-form-field sh-w-1-5 data-right">
							<select name="variavel[AAD51EC7-819C-4A5D-B531-2F29D9D2FAF0]" >
								<option value="denyGuest">Negar guests</option>
								<option value="denyAll">Negar a todos</option>
								<option value="acceptAll">Aceitar todos</option>
							</select>
						</div>
					</div>
					
					<hr />
					
					<!-- Tempo máximo de vida de uma sessão -->	
					<div class="sh-w-1">
						<div class="sh-w-4-5">
							<p><strong><?php echo $variavel['results']['5D0F0250-BCB1-4130-980C-2A6E1370106D']['nome'] ?></strong></p>
							<small>Tempo máximo que uma sessão deve permanecer ativa sem atividade</small>
						</div>
						<div class="sh-form-field sh-w-1-5 data-right">
							<input type="text" name="variavel[5D0F0250-BCB1-4130-980C-2A6E1370106D]" value="<?php echo $variavel['results']['5D0F0250-BCB1-4130-980C-2A6E1370106D']['valor']; ?>" mask="inteiro" validationType="number" data-number-min="15" />
						</div>
					</div>
					
				</div>

			</fieldset>
			
			<div class="sh-btn-holder">
				<button type="submit" class="sh-btn-verde">Salvar todas</button>
			</div>
			
		</form>
						
	</div>
		
</section>