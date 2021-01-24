<?php

	
	include_once("../prova1/include/db2.php");
	include_once("class/INTERAGIRE.class.php");

	
	
	if ($_POST) {

		
		
		
		$param = array(
			'db' => $db2,
			// 'giocatore' => isset($_SESSION['giocatore']['id']) && !empty($_SESSION['giocatore']['id']) ? $_SESSION['giocatore']['id'] : $_POST['data']
			'giocatore' => isset($_POST['data']) ? $_POST['data'] : ''
		);

		$action = new INTERAGIRE($param);
		
		
		
		switch ($_POST['action']) {
			
			
			case 'login':
			
				session_unset();
				session_start();
				
				
				
				
				
				
				
				// print_r($action->login());
				$action->login();
				// print_r($_SESSION);
				break;
				
			case 'start':
				// T>EST
				session_unset();
				session_start();
				$action->login();
				// TEST FINE
				print_r($action->start());
				// print_r($_SESSION);
				
				break;
				
			case 'scegliNemico':
			
				
				// $nemicoStats = $action->caricaPersonaggio($nemico);
				$nemicoStats = $action->caricaPersonaggio();
				// $_SESSION['nemico']['id'] = $nemicoStats['id'];
				print_r($nemicoStats);
				// print_r($_SESSION);
				
				break;
				
			case 'scegliOggetto':
				
				
				$oggettoStats = $action->caricaOggetti();
				$_SESSION['giocatore']['oggetti']['equipaggiato'] = $oggettoStats['id'];
				print_r($oggettoStats);
				// print_r($_SESSION);
				
				break;
				
			// attacco
			
			
			// case TEST
			default: 
				
				print_r($_SESSION);
		}
		
		
	}
	
	
	
	

?>