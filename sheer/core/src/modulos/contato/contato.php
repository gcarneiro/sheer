<?php

namespace Sh\Modules\contato;

class contato_adicionar extends \Sh\GenericAction {
	
	public function doAction($data) {
		
		//Gerando localizador para o contato
		$data['localizador'] = \Sh\Library::getUniqueIntegerCode();
		
		//DETERMINANDO O DESTINO DA MENSAGEM
		//Capturo inicialmente o padrao
		$destinoEmail = \Sh\ContentProviderManager::loadContent('contato/destino_loadByAlias', array('alias'=>'default'));
		$destinoEmail = reset($destinoEmail['results']);
		//Se tivermos um destino setado enviamos para ele
		if( isset($data['idDestino']) && $data['idDestino'] ) {
			$tmp = \Sh\ContentProviderManager::loadContentById('contato/destino', $data['idDestino']);
			if( $tmp ) {
				$destinoEmail = $tmp;
			}
		}
		//Setando o idDestino na mensagem
		$data['idDestino'] = $destinoEmail['id'];
		
		//Capturando o email principal do projeto
		$emailAdmin = \Sh\Modules\variavel\variavel::getVariavelByAlias('emailDefault');
		
		//Gerando html
		$html = '';
		$html .= '<strong>Nome:</strong> '.$data['nome']."<br />";
		$html .= '<strong>E-mail:</strong> '.$data['email']."<br />";
		$html .= '<strong>Telefone:</strong> '.$data['telefone']."<br />";
		$html .= '<strong>Assunto:</strong> '.$data['assunto']."<br />";
		$html .= '<strong>Mensagem:</strong> '.$data['mensagem']."<br />";
		
		//Envinado email
		$mailInfo = array(
			'from' => array('name'=>'E-mail automático de:'.$data['email'], 'email'=>$emailAdmin['valor']),
			'replyTo' => array('name'=>$data['nome'], 'email'=>$data['email']),
			'address' => array('name'=>$destinoEmail['nome'], 'email'=>$destinoEmail['email']),
			'subject' => 'Contato sobre: '.$data['assunto'],
			'body' => $html
		);
		$response = \Sh\MailerProvider::sendMail($mailInfo);
		
		return \Sh\ContentActionManager::doPrimitiveAction('contato/contato', 'add', $data, $this->connection);
		
	}
	
}


/**
 * @author Guilherme
 * 
 * ActionHandler Delete customizado para impedir a remoção de destino reservados ao sistema
 *
 */
class destino_delete extends \Sh\GenericAction {
	
	public function doAction($data) {
		
		
		$destino = \Sh\ContentProviderManager::loadContentById('contato/destino', $data['id']);
		if( !$destino ) {
			throw new \Sh\SheerException(array(
				'code' => null,
				'message' => 'Destino inválido para remoção'
			));
		}
		
		if( $destino['removivel'] == 2 ) {
			throw new \Sh\SheerException(array(
				'code' => null,
				'message' => 'Este Destino é reservado ao sistema e não pode ser removido'
			));
		}
		
		return \Sh\ContentActionManager::doPrimitiveAction('contato/destino', 'delete', $data, $this->connection);
		
	}
	
}

/**
 * @author Guilherme
 * 
 * ActionHandler para arquivar contato anteriores
 *
 */
class contato_arquivar extends \Sh\GenericAction {
	
	public function doAction($data) {
		
		$data['arquivado'] = 1;
		return \Sh\ContentActionManager::doAction('contato/contato_update', $data, $this->connection);
		
	}
	
}


/**
 * @author Guilherme
 * 
 * ActionHandler Add padrão de destino customizado para geração do alias para o destino
 *
 */
class destino_adicionar extends \Sh\GenericAction {
	
	public function doAction($data) {
		
		$data['alias'] = \Sh\Library::generatePermalink($data['nome']);
		return \Sh\ContentActionManager::doPrimitiveAction('contato/destino', 'add', $data, $this->connection);
		
	}
	
}