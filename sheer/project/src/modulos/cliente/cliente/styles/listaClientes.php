<?php
	$clientes = $content['cliente/cliente_lista'];
?>

<section class="sh-box">

	<header>
		<div><span data-icon="a"></span></div>
		
		<h1>Lista de Clientes</h1>
		
		<a href="?p=cliente/adicionar" data-icon="k"></a>
		
		<div><?php echo $clientes['total'].'/'.$clientes['available']; ?></div>
		
	</header>
	
	<div class="sh-box-content">
	
		<table id="listaBairros" class="sh-table sh-table-admin" >
		
			<thead>
				<tr>
					<th><input type="checkbox" /></th>
					<th>Nome</th>
					<th>Email</th>
					<th>CPF/CNPJ</th>
					<th class="sh-w-100"></th>
				</tr>
			</thead>
			
			<tbody>
			
				<?php 
					$html = '';
					if( $clientes['results'] ) {
						foreach ( $clientes['results'] as $detalhes ) {
							$html .= '<tr data-id="'.$detalhes['id'].'" data-content>';
								$html .= '<td><input type="checkbox" sh-check="" name="itemId[]" value="'.$detalhes['id'].'" /></td>';
								$html .= '<td>'.$detalhes['nome'].'</td>';
								$html .= '<td>'.$detalhes['email'].'</td>';
								$html .= '<td>'.$detalhes['cpf'].'</td>';
								$html .= '<td class="data-right">';
									$html .= '<a href="renderer.php?rd=localidade/atualizarBairro&id='.$detalhes['id'].'" sh-component="overlayLink" data-id="'.$detalhes['id'].'" data-icon="s" ></a>';
									$html .= '<a href="action.php?ah=localidade/bairro_delete&id='.$detalhes['id'].'" sh-component="action" sh-comp-rh="[sheer/modules/localidade][bairros.gerenciar.deleteResponseHandler]" data-id="'.$detalhes['id'].'" data-icon="x" ></a>';
								$html .= '</td>';
							$html .= '</tr>';
						}
						echo $html;
					}
				?>
				
			</tbody>
		
		</table>
		
		<?php 
			$html = '';
			if( $clientes['total'] == 0 ) {
				$html = '<div class="data-center">';
					$html .= '<p data-icon="g" style="font-size: 1.3em;"></p>';
					$html .= '<p style="text-transform: uppercase">Nenhum resultado encontrado</p>';
				$html .= '</div>';
				echo $html;
			}
		?>
	
	
	
		<section class="sim-lista-pessoas">
		
			<?php 
				
				/*$html = '';
						
						foreach ($cliente['results'] as $idPessoa=>$pessoa) {
							
							//determinando primeiro nome
							$tmp = explode(' ', $pessoa['nome']);
							$nomePrimeiro = $tmp[0];
							
							$html .= '<div data-id="'.$pessoa['id'].'">';
							
								$html .= '<div>';
									$html .= '<div>';
										$html .= '<img src="'.$pessoa['avatar']['90'].'" title="'.$pessoa['nome'].'" alt="'.$pessoa['nome'].'" />';
										$html .= '<p><strong>'.$nomePrimeiro.'</strong></p>';
										$html .= '<p>'.$pessoa['codigo'].'</p>';
										$html .= '<p>'.$pessoa['tipoPessoa_lookup'].'</p>';
									$html .= '</div>';
								$html .= '</div>';
						
								$html .= '<div>';
								
										$html .= '<p class="highlight">'.$pessoa['nome'].'</p>';
										if($pessoa['tipoPessoa']==2){
											
											$html .= '<p><label>CPF: </label>'.$pessoa['cpf'].'</p>';
										}
										else{											
											$html .= '<p><label>Razão Social: </label>'.$pessoa['razaoSocial'].'</p>';
											$html .= '<p><label>CNPJ: </label>'.$pessoa['cnpj'].'</p>';
										}
									
// 										if($pessoa['telefone']!=null){
												
// 											$html .= '<p><label>Telefone: </label>'.$pessoa['telefone'];
// 										}
										
// 										if($pessoa['telefone2']!=null){
										
// 											$html .= ' /  '.$pessoa['telefone'];
// 										}
										
										$html .='</p>';
										
										if($pessoa['email']!=null){
												
											$html .= '<p><label>E-mail: </label>'.$pessoa['email'];
										}
										
										if($pessoa['email2']!=null){
										
											$html .= '  /  '.$pessoa['email2'];
										}
								
										$html .='</p>';
										
// 										if($pessoa['endereco']!=null){
												
// 											$html .= '<p><label>Endereço: </label>'.$pessoa['endereco'].', 
// 											'.ucfirst(mb_strtolower($pessoa['bairro']['nome'], 'UTF-8')).', 
// 											'.$pessoa['cidade']['nome'].', '.$pessoa['estado']['sigla'].'</p>';
// 										}
										
										
									$html .= '</div>';
									
									$html .= '<div class="sim-btn-acao">';
										$html .= '<a href="?p=cliente/dashboard&id='.$pessoa['id'].'">Dashboard</a>';
									$html .= '</div>';
							
							$html .= '</div>';
				}
				
				echo $html;*/
			
			?>
		
		</section>
		
		
		<div class="sh-btn-holder">
			<a href="?p=cliente/adicionar"  class="sh-btn-cinza-i">Cadastrar Cliente</a>
		</div>
		
		
	</div>
	
</section>
