<?php

	

	ob_start();
	
	include_once("../prova1/include/db2.php");
	

	
	if ($_POST['action']) {
		
		
		include_once("class/BATTLEGRID.class.php");
		
		
		
		
		$grid = new BATTLEGRID(array('db' => $db2));
		
		
		$data = json_decode($_POST['data'], true);
		
		
		$html_id = trim($data['html_id']);
		$pos_x = isset($data['pos_x']) ? trim($data['pos_x']) : '';
		$pos_y = isset($data['pos_y']) ? trim($data['pos_y']) : '';
		
		// print_r($pos_x . " " . $pos_y);die();
		
		if ((preg_match('/[0-9]+/', $pos_x)) && 
			preg_match('/[0-9]+/', $pos_y)) {
				
			// action:
			switch ($_POST['action']) {
				
				case 'get': // refresh pagina
					$return = $grid->get($html_id);
				break;
				
				case 'put': // pedina mossa per la prima volta
					$return = $grid->put($html_id, $pos_x, $pos_y);
				break;
				
				case 'update': // pedina mossa già presente in tabella
					$return = $grid->update($html_id, $pos_x, $pos_y);
				break;
				
				case 'delete': // pedina eliminata
					$return = $grid->remove($html_id);
				break;
				
				default:
					$return = array("error" => "nessuna azione");
				break;
			}
				
		} else {
			
			$return = array("error" => "Posizione errata");
			
			
			
		}
		
		
		
		
	

		
		echo json_encode($return);
		
		
	}
	
	
?>