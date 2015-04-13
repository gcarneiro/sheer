<?php
	$clienteSenha = $content['clienteSenha/clienteSenhaPorPessoa'];
	$cliente = \Sh\ContentProviderManager::loadContentById('cliente/cliente', $_GET['idCliente']);
?>

<section class="sim-box">

	<header>
	
		<div><span data-icon="a"></span></div>
		<h1>Cliente</h1>
		
	</header>
	
	<?php 
	$html = '';
	
		$html .='<p>Nome: '.$cliente['nome'].'</p>';
		$html .='<p>Tipo: '.$cliente['tipoPessoa_lookup'].'</p>';
		if($cliente['tipoPessoa']==2){
			$html .='<p>CPF: '.$cliente['cpf'].'</p>';
		}
		else{
			$html .='<p>Razão Social: '.$cliente['razaoSocial'].'</p>';
			$html .='<p>CNPJ: '.$cliente['cnpj'].'</p>';
			$html .='<p>Inscrição Municipal: '.$cliente['inscricaoMunicipal'].'</p>';
		}
		if($cliente['telefone']!= null){
			$html .='<p>Telefone:'.$cliente['telefone'].'</p>';
		}
		if($cliente['telefone2']!= null){
			$html .='<p>Telefone2 :'.$cliente['telefone2'].'</p>';
		}
		if($cliente['email']!= null){
			$html .='<p>E-mail: '.$cliente['email'].'</p>';
		}
		if($cliente['email2']!= null){
			$html .='<p>E-mail2: '.$cliente['email2'].'</p>';
		}
		$html .='<p>Endereço: '.$cliente['endereco'].'</p>';
		$html .='<p>Número: '.$cliente['numero'].'</p>';
		$html .='<p>Complemento: '.$cliente['complemento'].'</p>';
		$html .='<p>Estado: '.$cliente['estado']['nome'].'</p>';
		$html .='<p>Ciade: '.$cliente['cidade']['nome'].'</p>';
		$html .='<p>Bairro: '.$cliente['bairro']['nome'].'</p>';
			
		$html .='<p><a href="renderer.php?rd=clienteSenha/adicionarSenha&id='.$cliente['id'].'" sh-component="overlayLink">Cadastrar Senha</a></p>';
		
	echo $html;
	?>

</section>

<section class="sim-box">

	<header>
		<div><span data-icon="a"></span></div>
		
		<h1>Lista de Senhas</h1>
		
		<a  href="renderer.php?rd=clienteSenha/adicionarSenha&id=<?php echo $_GET['idCliente']?>" sh-component="overlayLink" data-icon="k" class="adicionarItem"></a>
		
		<div class="totalItens"><?php echo $clienteSenha['total'].'/'.$clienteSenha['available']; ?></div>
		
	</header>
	
	<?php 
		$classeVazio = '';
		if( $clienteSenha['total'] == 0 ) {
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
						<th>Senha</th>
						<th>Observação</th>
					</tr>
				</thead>
				
				<tbody>
				
					<?php 
						$html = '';
						if( $clienteSenha['total'] > 0 ) {
							
							foreach ( $clienteSenha['results'] as $idSenha=>$senha ) {
								$html .= '<tr data-id="'.$senha['id'].'" data-content>';
									$html .= '<td><input type="checkbox" name="itemId[]" value="'.$senha['id'].'" /></td>';
									$html .= '<td>'.$senha['servico'].'</td>';
									$html .= '<td>'.$senha['login'].'</td>';
									$html .= '<td>'.$senha['senha'].'</td>';
									$html .= '<td>'.$senha['observacao'].'</td>';
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