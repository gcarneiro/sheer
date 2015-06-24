<?php
	$modulos = $content['modulo/listarModulosRegistrados'];
?>

<section class="sh-box sh-box-azul">

	<div class="sh-box-content">
	
	<?php 
	
	$html = $htmlNavegacao = '';;
	
	//Gerando htmls
	$htmlNavegacao .= '<div class="sh-w-1-5 sh-w-max-200" style="padding-right: 2em;">';
	$htmlNavegacao .= '<ul class="sh-list sh-list-vertical">';
	
	$html .= '<div class="sh-w-4-5">';
	
		$html .= '<form class="sh-form">';
				$html .= '<div class="sh-form-field sh-w-1">';
					$html .= '<input type="text" id="buscarModulo" placeholder="Procure pelo módulo desejado" />';
				$html .= '</div>';
				$html .= '';
		$html .= '</form>';
		
		$html .= '<div class="sh-w-1" style="padding-left: 1em;">';
	
		foreach ( $modulos as $idModulo=> &$info ) {
			
	// 		$info = new \Sh\Module($id, $name, $description);
	
			$htmlNavegacao .= '<li><a href="#">'.$info->name.'</a></li>';
			
			$html .= '<h2>'.$info->name.'</h2>';
			$html .= '<div id="'.$info->id.'" style="padding-left: 2em;">';
				$html .= '<p><strong>Id: </strong>'.$info->id.'</p>';
				$strContexto = '';
				if( $info->context['sheer'] ) {
					$strContexto .= 'Sheer';
				}
				if( $info->context['project'] ) {
					if( strlen($strContexto) ) {$strContexto .=', ';}
					$strContexto .= 'Project';
				}
// 				var_dump($info);
				$html .= '<p><strong>Contexto: </strong> '.$strContexto.'</p>';
				$html .= '<p><strong>Descrição:</strong></p>';
				$html .= '<p>'.$info->description.'</p>';
				
				//DATA SOURCES
				$html .= '<div class="sh-tab">';
					$html .= '<h3>DataSources</h3>';
					$html .= '<div class="sh-w-1" style="padding-left: 1.5em;">';
						$dataSources = $info->getDataSources(false);
						if($dataSources) {
							foreach ( $dataSources as $idDataSource => &$ds ) {
								$fields = $ds->getFields(false);
								
								$html .= '<h4>'.$ds->getId().'</h4>';
								$html .= '<div class="sh-w-1" style="padding-left: 1.5em;">';
									$html .= '<p><strong>ID: </strong>'.$ds->getModuleId().'/'.$ds->getId().'</p>';
									$html .= '<p><strong>Fields: </strong>';
										$f=true;
										foreach( $fields as $idField=>&$field ) {
											if( !$f ) {
												$html .= ', ';
											}
											$f = false;
											$html .= '<span class="sheer-dsField">'.$field->getId().'</span>';
										}
									$html .= '</p>';
								$html .= '</div>';
							}
						}
						else {
							$html .= '<p class="data-center">Não existem dataSources registrados</p>';
						}
					$html .= '</div>';
				$html .= '</div>';
				
				//DATAPROVIDERS
				$html .= '<div class="sh-tab">';
					$html .= '<h3>DataProviders</h3>';
					$html .= '<div class="sh-w-1" style="padding-left: 1.5em;">';
					$dataProviders = $info->dataProviders;
					if($dataProviders) {
						foreach ( $dataProviders as $idDataProvider => &$dp ) {
							
		// 					$dp = new \Sh\DataProvider($id, $dataSource);
							
							$html .= '<h4>'.$dp->getId().'</h4>';
							$html .= '<div class="sh-w-1" style="padding-left: 1.5em;">';
								$html .= '<p><strong>ID: </strong>'.$dp->getModuleId().'/'.$dp->getId().'</p>';
								$html .= '<p><strong>Data Source: </strong>'.($dp->getDataSourceId()).'</p>';
							$html .= '</div>';
							
							continue;
							$fields = $ds->getFields(false);
					
							
							
							$html .= '<p><strong>Fields: </strong>';
							$f=true;
							foreach( $fields as $idField=>&$field ) {
								if( !$f ) {
									$html .= ', ';
								}
								$f = false;
								$html .= '<span class="sheer-dsField">'.$field->getId().'</span>';
							}
							$html .= '</p>';
							
						}
					}
					else {
						$html .= '<p class="data-center">Não existem dataSources registrados</p>';
					}
					$html .= '</div>';
				$html .= '</div>';
			$html .= '</div>';
	
		}
	$htmlNavegacao .= '</ul>';
	$htmlNavegacao .= '</div>';
		$html .= '</div>';
	$html .= '</div>';
	
	echo $htmlNavegacao;
	echo $html;
	
	?>
	
	</div>
		
</section>