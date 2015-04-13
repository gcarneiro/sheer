<?php
	$cliente = reset($content['cliente/cliente_detalhes']['results']);
?>
<!-- INFORMAÇÃO CLIENTE -->
<section class="sim-box sim-box-infoRapida sh-w-1000 sh-margin-x-auto">

	<header>
		<div><span data-icon="a"></span></div>
		
		<h1><?php echo $cliente['nome'] ?></h1>
		
	</header>
	
	<div class="sim-box-content">
	
		<section class="sim-lista-pessoas">
	
			<div data-id="<?php echo $cliente['id']; ?>">
				<div>
					<div>
						<img src="<?php echo $cliente['avatar']['90']; ?>" title="<?php echo $cliente['nome']; ?>" alt="<?php echo $cliente['nome']; ?>" />
					</div>
				</div>
				
				<div>
					<?php
						$html = '';
						if($cliente['tipoPessoa']==2){
							$html .='<p><strong>CPF:</strong> '.$cliente['cpf'].'</p>';
						}
						else{
							$html .='<p><strong>Razão Social:</strong> '.$cliente['razaoSocial'].'</p>';
							$html .='<p><strong>CNPJ:</strong> '.$cliente['cnpj'].'</p>';
						}
						if($cliente['email']){
							$html .='<p><strong>E-mail:</strong> '.$cliente['email'].'</p>';
						}
						if($cliente['telefone']){
							$html .='<p><strong>Telefone:</strong> '.$cliente['telefone'].'</p>';
						}
						if($cliente['inscricaoMunicipal']){
							$html .='<p><strong>Inscrição Municipal:</strong> '.$cliente['inscricaoMunicipal'].'</p>';
						}
						
						echo $html;
					?>
				</div>
			</div>
			<div class="detalhes">
				<?php
				if($cliente['email2'] || $cliente['telefone2']){
					$html = '';
					$html.='<div class="sh-w-1-2">';
					if($cliente['email2']){
						$html .='<p><strong>E-mail Secundário:</strong> '.$cliente['email2'].'</p>';
					}
					if($cliente['telefone2']){
						$html .='<p><strong>Telefone Secundário:</strong> '.$cliente['telefone2'].'</p>';
					}
					
					$html.='</div>';
					echo $html;					
				}
				?>
				<div class="sh-w-1-2">
					<?php
					$html = '';
					$html .= '<p><strong>Endereço: </strong>'.$cliente['endereco'].', ';
					if($cliente['numero']){
						$html.='Nº: '.$cliente['numero'].', ';
					}
					$html.=''.ucfirst(mb_strtolower($cliente['bairro']['nome'], 'UTF-8')).', '.$cliente['cidade']['nome'].' - '.$cliente['estado']['sigla'].'</p>';
					if($cliente['cep']){
						$html .= '<p><strong>CEP: </strong>'.$cliente['cep'];
					}
					if($cliente['complemento']){
						$html .='<p><strong>Complemento: </strong>'.$cliente['complemento'].'</p>';
					}
					echo $html;
					?>
				</div>
			</div>
			<div class="sim-btn-1 sim-btn-margin-05">
				<a href="renderer.php?rd=cobranca/adicionarCobranca&idCliente=<?php echo $cliente['id'] ?>" sh-component="overlayLink">Adicionar Cobrança</a>
			</div>
			
		</section>
		
	</div>
	
</section>

<!-- COBRANÇA RECORRENTE -->

<section class="sim-box sh-w-1000 sh-margin-x-auto"">

	<header>
		<div><span data-icon="a"></span></div>
		
		<h1>Cobranças Recorrentes</h1>
		
		<a  href="renderer.php?rd=cobrancaRecorrente/adicionarRecorrente&id=<?php echo $_GET['id']?>" sh-component="overlayLink" data-icon="k" class="adicionarItem"></a>
		
	</header>
	
	<?php 
		$classeVazio = '';
		if( $cliente['cobrancaRecorrente'] == null ) {
			$classeVazio = 'sim-lista-empty';
		}
	?>
	<div class="sim-box-content">
	
		<section class="sim-lista-gerenciar <?php echo $classeVazio; ?>">
		
			<table class="sh-table sh-table-admin">
			
				<thead>
					<tr>
						<th><input type="checkbox" /></th>
						<th>Serviço</th>
						<th class="data-center">Valor</th>
						<th class="data-center" >Recorrência</th>
						<th class="data-center" >Data de Início</th>
						<th class="data-center" >Data de Término</th>
						<th class="data-center" >Descrição</th>
						<th class="sim-element-action"></th>
					</tr>
				</thead>
				
				<tbody>
				
					<?php 
						$html = '';
						if( $cliente['cobrancaRecorrente'] ) {
							//FIXME CONTIUNA DAQUI. FALTA FZER O ESTILO DE INSERIR COBRANÇA
							foreach ( $cliente['cobrancaRecorrente'] as $idCobrancaRecorrente=>$cobrancaRecorrente ) {
								
								if($cobrancaRecorrente['ativo']==2){									
									$html .= '<tr class="cobrancaRecorrente-cancelada" data-id="'.$cobrancaRecorrente['id'].'" data-content>';
								}
								else{
									$html .= '<tr data-id="'.$cobrancaRecorrente['id'].'" data-content>';
								}
								
								
								$html .= '<td><input type="checkbox" name="itemId[]" value="'.$cobrancaRecorrente['id'].'" /></td>';
								//SERVIÇO
								if($cobrancaRecorrente['servico']['descricao']){
									$html .= '<td class="observacoesLabel">'.$cobrancaRecorrente['servico']['nome'];
									$html .= '<input type="hidden" class="conteudo" value="'.$cobrancaRecorrente['servico']['descricao'].'" />';
									$html .= '<input type="hidden" class="titulo" value="Descrição do Serviço" /></td>';
								}
								else {
									$html .= '<td>'.$cobrancaRecorrente['servico']['nome'].'</td>';
								}
								//VALOR
								$html .= '<td class="data-center">R$ '.$cobrancaRecorrente['valor'].'</td>';
								//RECORRENCIA
								$html .= '<td class="data-center">'.$cobrancaRecorrente['recorrencia_lookup'].'</td>';
								//DATAINICIO
								$html .= '<td class="data-center">'.$cobrancaRecorrente['dataInicio']['date'].'</td>';
								//DATAFIM
								$html .= '<td class="data-center">'.$cobrancaRecorrente['dataFim']['date'].'</td>';
								
								//se o registro tiver observações imprimo sim
								if($cobrancaRecorrente['descricao']){
									$html .= '<td class="data-center observacoesLabel" >Sim';
									$html .= '<input type="hidden" class="conteudo" value="'.$cobrancaRecorrente['descricao'].'" />';
									$html .= '<input type="hidden" class="titulo" value="Descrição" /></td>';
								}
								else {
									$html .= '<td class="data-center " >Não</td>';
								}
								$html .= '<td><a href="#" sh-component="navigation" sh-component-target="navegacaoRecorrente" data-id="'.$cobrancaRecorrente['id'].'" data-icon="d" ></a></td>';
								$html .= '</tr>';

							}
							echo $html;
						}
					?>
					
				</tbody>
			
			</table>
			
			<div class="empty-info">
				<p data-icon="g"></p>
				<p>Nenhum resultado encontrado</p>
			</div>
			
		</section>
	

	</div>
	
</section>


<!-- LISTA DE SENHAS -->
<section class="sim-box sh-w-1000 sh-margin-x-auto"">

	<header>
		<div><span data-icon="a"></span></div>
		
		<h1>Lista de Senhas</h1>
		
		<a  href="renderer.php?rd=clienteSenha/adicionarSenha&id=<?php echo $_GET['id']?>" sh-component="overlayLink" data-icon="k" class="adicionarItem"></a>
		
	</header>
	
	<?php 
		$classeVazio = '';
		if( $cliente['senha'] == null ) {
			$classeVazio = 'sim-lista-empty';
		}
	?>
	<div class="sim-box-content">
	
		<section class="sim-lista-gerenciar <?php echo $classeVazio; ?>">
		
			<table class="sh-table sh-table-admin">
			
				<thead>
					<tr>
						<th><input type="checkbox" /></th>
						<th>Serviço</th>
						<th>Login</th>
						<th class="data-center" >Senha</th>
						<th class="data-center" >Observações</th>
						<th class="sim-element-action"></th>
					</tr>
				</thead>
				
				<tbody>
				
					<?php 
						$html = '';
						if( $cliente['senha'] ) {
							
							foreach ( $cliente['senha'] as $idSenha=>$senha ) {
								$html .= '<tr data-id="'.$senha['id'].'" data-content>';
									$html .= '<td><input type="checkbox" name="itemId[]" value="'.$senha['id'].'" /></td>';
									$html .= '<td>'.$senha['servico'].'</td>';
									$html .= '<td>'.$senha['login'].'</td>';
									
									$html .= '<td class="data-center colunaSenhas">';
										$html .= '<span class="verSenha">Ver Senha</span>';
										$html .= '<span class="senha" style="display:none">'.$senha['senha'].'</span>';
									$html .= '</td>';
									
									//se o registro tiver observações imprimo sim
									if($senha['observacao']){
										$html .= '<td class="data-center observacoesLabel" >Sim';
										$html .= '<input type="hidden" class="conteudo" value="'.$senha['observacao'].'" >';
										$html .= '<input type="hidden" class="titulo" value="Observações" ></td>';
									}
									else {
										$html .= '<td class="data-center " >Não</td>';
									}
									$html .= '<td><a href="#" sh-component="navigation" sh-component-target="navegacaoCliente" data-id="'.$senha['id'].'" data-icon="d" ></a></td>';
								$html .= '</tr>';

							}
							echo $html;
						}
					?>
					
				</tbody>
			
			</table>
			
			<div class="empty-info">
				<p data-icon="g"></p>
				<p>Nenhum resultado encontrado</p>
			</div>
			
		</section>
	

	</div>
	
</section>

<template id="navegacaoCliente">
	<nav class="sim-navegacaoItem" data-id="{id}">
		<header></header>
		<div>
			<ul>
				<li><a href="renderer.php?rd=clienteSenha/adicionarSenha&id={id}" sh-component="overlayLink">Editar</a></li>
				<li><a href="action.php?ah=clienteSenha/clienteSenha_delete&id={id}" sh-component="action">Apagar</a></li>
			</ul>
		</div>
		<footer></footer>
	</nav>
</template>

<template id="navegacaoRecorrente">
	<nav class="sim-navegacaoItem" data-id="{id}">
		<header></header>
		<div>
			<ul>
				<li><a href="renderer.php?rd=cobrancaRecorrente/adicionarRecorrente&id={id}" sh-component="overlayLink">Editar</a></li>
				<li><a href="renderer.php?rd=cobrancaRecorrente/cancelarRecorrente&id={id}" sh-component="overlayLink">Cancelar</a></li>
<!-- 				<li><a href="action.php?ah=cobrancaRecorrente/cobrancaRecorrente_delete&id={id}" sh-component="action">Excluir</a></li> -->
			</ul>
		</div>
		<footer></footer>
	</nav>
</template>

<script>
	require(['sim/cliente'], function (cliente) {
		cliente.listaSenhas.init();
	});
</script>