<?php 
namespace Sh\Modules\malaDiretaDisparoLink;


class verificarLink extends \Sh\GenericAction {
	
	public function doAction($data){
		
		$disparoLink = \Sh\ContentProviderManager::loadContentById('malaDiretaDisparoLink/malaDiretaDisparoLink', $data['id'], $this->connection);
		
		if($disparoLink){
			$linkAux = array(
				'id' => $disparoLink['id'],
				'total' => $disparoLink['total'] + 1,
			);
			
			$response = \Sh\ContentActionManager::doAction('malaDiretaDisparoLink/malaDiretaDisparoLink_update', $linkAux, $this->connection);
			
			$this->connection->commit();
			
			\Sh\Library::Redirect($disparoLink['link']);
		}
		
		
		return array(
			'status' => false,
			'code' => null,
			'data' => null,
		);
		
	}
	
}