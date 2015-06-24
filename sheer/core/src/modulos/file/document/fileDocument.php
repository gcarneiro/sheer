<?php

namespace Sh\Modules\fileDocument;

/**
 * Método que irá forcar o browser a fazer download do arquivo
 */
function download() {
	
	try {
		//VERIFICANDO ENVIO DAS HEADERS
		if( headers_sent() ) {
			throw new \Sh\SheerException(array(
				'code' => null,
				'message' => 'Impossível efetuar download do arquivo pois as headers HTTP já foram enviadas.'
			));
		}
		
		//CAPTURANDO IDENTIFICADOR DO DOCUMENTO
		$idFileDocument = null;
		if( isset($_GET['i']) && $_GET['i'] ) {
			$idFileDocument = $_GET['i'];
		}
		
		//VERIFICANDO INTEGRIDADE DO DOCUMENTO
		if( !$idFileDocument ) {
			throw new \Sh\SheerException(array(
				'code' => null,
				'message' => 'Arquivo inválido para download.'
			));
		}
		
		//CAPTURANDO ARQUIVO
		$file = \Sh\ContentProviderManager::loadItem('fileDocument/fileDocument', $idFileDocument);
		if( !$file ) {
			throw new \Sh\SheerException(array(
				'code' => null,
				'message' => 'Arquivo inválido para download.'
			));
		}
		
		//MARCANDO DOWNLOAD DO ARQUIVO
		$fileUpdate = array(
			'id' => $file['id'],
			'downloads' => ++$file['downloads']
		);
		$response = \Sh\ContentActionManager::doAction('fileDocument/fileDocument_update', $fileUpdate);
		
		//DETERMINANDO INFORMAÇÕES PARA DOWNLOAD DO ARQUIVO
		$downloadName = $file['nameFull'];
		
		//ENVIANDO ARQUIVO
		// Configuramos os headers que serão enviados para o browser
		header('Content-Description: File Transfer');
		header('Content-Disposition: attachment; filename="'.$downloadName.'"');
		header('Content-Type: application/octet-stream');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: ' . $file['size']);
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Expires: 0');
		readfile($file['path']);
	}
	catch (\Sh\SheerException $e) {
		header('Content-Type: text/html; charset=UTF-8');
		echo '<h1>'.$e->getErrorMessage().'</h1>';
	}
	
	exit;
	
}

/**
 * @author Guilherme
 * 
 * DataProvider de detalhes customizado.
 *
 * @return null se não encontrar arquivo
 * 			array caso o encontre
 *
 */
class fileDocumentParser extends \Sh\GenericDataParser {
	
	public function parseData($data) {
		
		if( $data ) {
			foreach ( $data as $idContent=>&$content ) {
				$content['downloadLink'] = 'dfd.php?i='.$content['id'];
			}
		}
		
		return $data;
		
	}
	
}

/**
 * @author Guilherme
 * 
 * Customizo este ActionHandler pois ele irá receber exatamente as informações do $_FILES do php e deverá adicionar isso no Sheer e devolver um objeto de controle para este arquivo
 *
 */
class fileDocument_add extends \Sh\GenericAction {
	
	
	public function doAction($data) {
		
		//TRATANDO ERROS DE SUBMISSAO
		$errorMessage = null;
		switch( $data['error'] ) {
			case UPLOAD_ERR_INI_SIZE:
				$errorMessage = 'O arquivo excede o tamanho máximo de arquivos permitido.';
				break;
			case UPLOAD_ERR_FORM_SIZE:
				$errorMessage = 'O arquivo excede o tamanho máximo de arquivos permitido.';
				break;
			case UPLOAD_ERR_PARTIAL:
				$errorMessage = 'O arquivo foi apenas parcialmente enviado.';
				break;
			case UPLOAD_ERR_NO_FILE:
				$errorMessage = 'Nenhum arquivo foi enviado.';
				break;
			case UPLOAD_ERR_NO_TMP_DIR:
				$errorMessage = 'Não foram encontrados pastas temporárias para gravação do arquivo.';
				break;
			case UPLOAD_ERR_CANT_WRITE:
				$errorMessage = 'Erro ao tentar gravar no disco.';
				break;
			case UPLOAD_ERR_EXTENSION:
				$errorMessage = 'Problemas internos de extensões.';
				break;
		}
		if( $errorMessage ) {
			throw new \Sh\ActionException(array(
				'code' => null,
				'message' => $errorMessage
			));
		}
		
		//CRIANDO ESTRUTURA E DADOS DO FILEDOCUMENT
		$fileDocument = array(
			'id'				=> \Sh\Library::getUniqueId(true),
			'size'				=> $data['size'],
			'name'				=> null,
			'nameFull'			=> $data['name'],
			'nameExt'			=> null,
			'path'				=> null,
			'adicionadoEm'		=> date('d/m/Y H:i:s'),
			'adicionadoPor'		=> \Sh\AuthenticationControl::getAuthenticatedUserInfo('id'),
			'downloads'			=> 0,
			'mimeType'			=> $data['type'],
			'remove'			=> 2
		);
		
		//DEFININDO INFORMAÇÕES DO NOME E PATH FINAL
		//determinando extensao
		$positionDot = strrpos($fileDocument['nameFull'], '.');
		$positionBarra = strrpos($fileDocument['nameFull'], '/');
		$positionBarra2 = strrpos($fileDocument['nameFull'], '\\');
		if( $positionDot && ( !$positionBarra || $positionDot > $positionBarra ) && ( !$positionBarra2 || $positionDot > $positionBarra2 ) ) {
			$fileDocument['nameExt'] = substr($fileDocument['nameFull'], $positionDot+1);
			$fileDocument['name'] = substr($fileDocument['nameFull'], 0, $positionDot);
		}
		//determinando filepath
		$fileDocument['path'] = SH_PUBLIC_DATA_FILES.$fileDocument['id'];
		if( $fileDocument['nameExt'] ) {
			$fileDocument['path'] .= '.'.$fileDocument['nameExt'];
		}
		
		//MOVENDO O ARQUIVO DE UPLOAD
		$moved = move_uploaded_file($data['tmp_name'], $fileDocument['path']);
		if( !$moved ) {
			throw new \Sh\ActionException(array(
				'code' => null,
				'message' => 'Erro ao tentar mover arquivo temporário.'
			));
		}
		
		//SALVANDO DADOS DO ARQUIVO
		$response = \Sh\ContentActionManager::doPrimitiveAction('fileDocument/fileDocument', 'add', $fileDocument, $this->connection);
		\Sh\Library::actionResponseCheck($response);
		
		return $response;
		
		
	}
	
	
}
/*
class executarRemocaoDocumentos extends \Sh\GenericJob {
	
	public function run() {
		
		//PRECISO BUSCAR OS DOCUMENTOS PARA REMOVER
		do {
			$documentos = \Sh\ContentProviderManager::loadContent('fileDocument/fileDocument_lista', array('remove'=>1));
			if( $documentos['total'] > 0 ) {
				foreach ( $documentos['results'] as $idDocument=>$file ) {
					var_dump($file);
				}
			}
			
		} while( $documentos['total'] > 0 && $documentos['available'] >= $documentos['total'] );
		
		if( $documentos['total'] > 0 ) {
			
		}
		
		
	}
	
	
}
*/


