<?php

	class INTERAGIRE {
		

		
		private $db;
		public $giocatore;
		public $nome;
		
		
		
		public function __construct($param) {
			
			
			$this->db = isset($param['db']) && !empty($param['db']) ? $param['db'] : [];
			$this->giocatore = isset($param['giocatore']) && !empty($param['giocatore']) ? $param['giocatore'] : '';
			// $stat = $this->caricaPersonaggio($param['giocatore']);
			
			
		}
		
		
		public function logout() {
			
			if (session_status() !== "0") {

				session_unset();	
				
				ob_end_flush();
				

			} else {
				print_r(session_status());
			}
			
			
		}
		

		/**
		* login: carica tutte le statistiche del personaggio
		*/
		public function login($giocatore = '') {
			
			session_unset();
			session_start();
			
			$stats = !empty($giocatore) ? $this->caricaPersonaggio($giocatore) : $this->caricaPersonaggio($this->giocatore);

			// [type] [ID] [data]=>array()
			$typeArray = $this->typeArray('login', $stats);
	
			$tmp = json_decode($typeArray, JSON_OBJECT_AS_ARRAY);

			$oggetti = $this->caricaOggettiDaProprietario($tmp['data']['id']);
			$abilita = $this->caricaAbilitaDaGiocatore($tmp['data']['id']);
			$tmp['data']['armi'] = $oggetti;
			$tmp['data']['abilita'] = $abilita;
			
			
			
			$_SESSION['ID'] = $tmp['ID'];
			$_SESSION['data']['giocatore'] = $tmp['data'];

			return $typeArray;

			
		}
		
		
		/**
		* start: inizio combattimento - carica statistiche di tutti i personaggi
		* in sessione, calcola iniziativa, assegna giro combattimento, prepara i personaggi nella
		* tabella tmp, crea gli oggetti da inserire nel piano di attacco.
		* 
		* 
		* @return 		array			prova					elenco degli oggetti in HTML
		* 		
		*/
		public function start() {
	
			$tabella = $this->leggiPianoDiAttacco();
			
			if ($tabella == "Combattimento non in esecuzione") {

				
				$test = array(); $iniziativa = array();
				
				$attaccoTmp = array(); $param = array(); $prova = array();
				
				// calcolo INIZIATIVA
				$return = array(); $nomi = array(); $id = array(); $iniziativaR = array(); $caratteristiche = array(); $giro = array();
				
				// personaggi senza GIRO
				$personaggi = $return['personaggi'] = $this->caricaPersonaggi();
				$return['oggetti'] = $this->caricaOggetti();
				


				// creare giro: PERSONAGGI
				foreach ($personaggi as $index => $value) {
					
					$init = array();
					
					// preleva tutte le caratteristiche di tutti i personaggi
					foreach ($value as $index2 => $value2) {

						if ($index2 == 'caratteristiche') $caratteristiche[] = (!empty($value2)) ? json_decode($value2, JSON_OBJECT_AS_ARRAY) : '';
						if ($index2 == 'iniziativa') $iniziativa[] = $value2;
						if ($index2 == 'nome') $nomi[] = $value2;
						if ($index2 == 'id') $id[] = $value2;

					}
					
				}	
				

				foreach ($caratteristiche as $index => $value) {

					if ($value != '') {
						
						foreach ($value as $index2 => $value2) {
							
							// DESTREZZA || INIZIATIVA
							if ($index2 == 'des') {
								
								$iniziativaR[] = $this->diceRoll(20) + $value2['bonus'];
								
							} else {
								
								if ($index2 == 'iniziativa') {
									
									$iniziativaR[] = $value2;
									
								}

							}
				
						}
						
						// in caso di personaggio senza caratteristiche
						
					} else $iniziativaR[] = $iniziativa[$index];
				
				}
				
				

				foreach ($iniziativaR as $index => $value) {
					
					$giro[$value] = $nomi[$index];

				}
				
				// GIRO COMBATTIMENTO
				krsort($giro);
				
				$return['giro'] = $giro;

				$_SESSION['data']['giro'] = $giro;



				// svuota tabella tmp
				$this->resetAttaccoBuffer();
				
				// carica personaggi ingaggiati nello scontro nella tabella tmp
				foreach ($personaggi as $index => $value) {
					foreach ($value as $index2 => $value2) {
						
						
						
						if ($index2 == 'nome') {
							$nome = $value2;
							$param['nome'] = $nome;
						};
						
						if ($index2 == 'id') $param['id'] = $value2;
						if ($index2 == 'giocatore') $param['giocatore'] = $value2;
						if ($index2 == 'caratteristiche') $param['caratteristiche'] = $value2;
						if ($index2 == 'iniziativa') $param['iniziativa'] = $value2;
						if ($index2 == 'ca') $param['ca'] = $value2;
						if ($index2 == 'pf') $param['pf'] = $value2;
						if ($index2 == 'armi') $param['armi'] = $value2;
						if ($index2 == 'incantesimi') $param['incantesimi'] = $value2;
						if ($index2 == 'abilita') $param['abilita'] = $value2;
						if ($index2 == 'equipaggiamento') $param['equipaggiamento'] = $value2;
						
						
						
						
						if (!empty($nome)) {
							foreach ($giro as $l => $r) {
								if ($r == $nome) {
									$iniziativaGiro = $l;
									$param['iniziativa_giro'] = $iniziativaGiro;
									
								}
							}
	
						};
				
					}
					
					$attaccoTmp[] = $param;
					$return['giro'] = $giro;
					

				}
				
				
				
				// sostituisci indici con tiro iniziativa
				foreach ($attaccoTmp as $index => $value) {
					
					foreach ($value as $index2 => $value2) {

						if ($index2 == 'nome') $key = array_search($value2, $return['giro']);

					}
					
					$test[$key] = $value;

				}

				
				krsort($test);
				
			
				
				// butta dentro tabella tmp in ordine desc di iniziativa
				foreach ($test as $index => $value) {
					$prova = $this->pianoDiAttaccoBuffer($value);

				}
				
				
				$return['personaggi'] = $this->leggiPianoDiAttacco();
				

				// crea oggetti
				$prova = $this->creaOggettiHTML($return);
				

				
			} else {
				
				$return['personaggi'] = $this->leggiPianoDiAttacco();
				
				$return['oggetti'] = $this->caricaOggetti();
				$iniziativa = array(); $nome = array();
				foreach ($return['personaggi'] as $index => $value) {
					
					foreach ($value as $index2 => $value2) {
						
						if ($index2 == 'iniziativa_giro') $iniziativa[] = $value2;
						if ($index2 == 'nome') $nome[] = $value2;
						
					}
					
					
					
				}
				

				foreach ($nome as $index => $value) {
	
					$return['giro'][$iniziativa[$index]] = $value;
					
				}

				// crea oggetti
				$prova = $this->creaOggettiHTML($return);
				
			}

			return $prova;

		}

		
		/**
		* attacca: attaccante VS attaccato 
		*/
		public function attacca($nemicoID) {
			
			$result = '';
			
			
			// identifica NEMICO - se !empty(nemico) è selezione del nemico
			if ($nemicoID == '') {
				
				$result = "Nemico selezionato";
				
			} else {
				
				
				if (!isset($_SESSION['data']['giocatore']['manoDestra'])) {
					
					
					$result = 'Non hai impugnato niente in mano!';
					
				} else {
					
					// preleva dati nemico da attacco_tmp
					$Query = "SELECT * from attacco_tmp where id = :id_nemico";
					
					$stmt = $this->db->prepare($Query);
					
					$stmt->bindParam(":id_nemico", $nemicoID, PDO::PARAM_STR);
					
					$stmt->execute();			

					
					
					if ($stmt->rowCount() > 0) {
						
						$resultPGs = $stmt->fetchAll(PDO::FETCH_ASSOC);	
						
						
						if ($resultPGs[0]['giocatore'] !== 'npc') {
							
							
							$result = 'Stai attaccando un tuo compagno!';
							
							
						} else {
							
							// id arma del nemico (sempre singola)
							$nemicoArmaID = preg_replace("/[^0-9]+/", "", $resultPGs[0]['armi']);
							$nemicoNome = $resultPGs[0]['nome'];
							
							// preleva armi/incantesimi/oggetti
							$nemicoGittata = $nemicoBonus = $gittata = $bonus = ''; $danno = $nemicoDanno = array();
							
							$QueryOggetti = 'SELECT * from oggetti';
							$stmtOggetti = $this->db->prepare($QueryOggetti);
							$stmtOggetti->execute();

							$resultOggetti = $stmtOggetti->fetchAll(PDO::FETCH_ASSOC);
							
							
							// dati giocatore da SESSIONE per armi/incantesimi/oggetti impugnati
							$giocatoreID = $_SESSION['data']['giocatore']['id'];
							$nome = $_SESSION['data']['giocatore']['nome'];
							
							// player di riga 1 fa TIRO PER COLPIRE 1d20 (nemici comandati da DM)
							$giocatoreArmaDX = $_SESSION['data']['giocatore']['manoDestra'];

							
							foreach ($resultOggetti as $index => $value) {
								
								// preleva dati arma nemico
								if ($value['id'] == $nemicoArmaID) {
									
									$nemicoGittata = $value['gittata'];
									$nemicoBonus = $value['bonus_tiro'];
									$nemicoDanno = json_decode($value['danno']);
									
								}
								
								// preleva dati arma giocatore
								if ($value['id'] == $giocatoreArmaDX) {
									
									$gittata = $value['gittata'];
									$bonus = $value['bonus_tiro'];
									$danno = json_decode($value['danno']);
									
								}
								
							}
							

							// TIRO PER COLPIRE NON SERVE CON INCANTESIMI

							// calcola TIRO X COLPIRE (TxC)
							$carattObject = json_decode($_SESSION['data']['giocatore']['caratteristiche']);
							
							
							// arma da mischia (mod FOR) o a distanza (mod DES)
							$carattMod = ($gittata == '') ? $carattObject->for->bonus : $desMod = $carattObject->des->bonus;

							$tiroPerColpire = $this->diceroll(20) + $_SESSION['data']['giocatore']['attacco_base'] + $carattMod;
			
							$nemicoCA = (int)$resultPGs[0]['ca'];

							$compar = ($tiroPerColpire >= (int)$nemicoCA) ? "maggiore" : "minore";
							
							
							
							// TxC andato a segno
							if ($tiroPerColpire >= (int)$nemicoCA) {
								

								// COLPITO!
								$danni = $this->diceroll($danno->dado, $danno->quantita) + (int)$carattMod;
								
								$nuoviPF = $resultPGs[0]['pf'] - $danni;
								
								
								// se pf nemico <= 0 elimina riga altrimenti diminuisci pf
								if ($nuoviPF <= 0) {
									
									return "$nome uccide $nemicoNome";die();
									
									
									$Query = "DELETE from attacco_tmp where id = :id_nemico";
						
									$stmt = $this->db->prepare($Query);
									
									$stmt->bindParam(":id_nemico", $nemicoID, PDO::PARAM_STR);
									
									$stmt->execute();
									
									
								} else {
									
									
									return "$nemicoNome subisce $danni punti di danno da $nome";die();
									
									
									// copia dati riga 1 in ultima riga e cancella riga 1 da attacco_tmp
									$Query = "SELECT id from attacco_tmp ORDER BY id DESC LIMIT 1;";
									
									$stmt = $this->db->prepare($Query);
									
									$stmt->execute();
									
									if ($stmt->rowCount() > 0) {
							
										$lastID = $stmt->fetch(PDO::FETCH_ASSOC);
									
									}
									
									// aggiorna statistiche attaccato in attacco_tmp
									$Query = "UPDATE attacco_tmp SET pf = :pf where id = :id_nemico;";

									$stmt = $this->db->prepare($Query);
									
									$stmt->bindParam(":pf", $nuoviPF, PDO::PARAM_STR);
									$stmt->bindParam(":id_nemico", $nemicoID, PDO::PARAM_STR);

									$stmt->execute();
									
									
									
									
									
								}	
								
								
								// aggiorna giro di iniziativa su schermo in tempo reale
								
								
							} else {
								
								$result = "<b>Mancato!</b><br>$tiroPerColpire $compar di $nemicoCA";
								
							}

						}

			
					} else $result = 'Combattimento non in esecuzione';
					
					
				}


			}



			return $result;
			
		}
		
		
		
		public function apri() {
			
			
			
		}
		
		
		public function parla() {
			
			
			
		}
		
		/* 
		NO DM
		*/
		public function raccogli() {
			
			
			
		}
		
		/* 
		NO DM
		*/
		public function interagisci() {
			
			
			
			
		}
		
		/**
		* 
		* funzione annullaAttacco: al click l'utente svuota la tabella buffer di combattimento
		* 
		*/
		public function annullaAttacco() {
			
			$this->resetAttaccoBuffer();
	
		}
		
		
		
		
		
		
		/**
		* funzione inserisciDM: consente inserimento di un nuovo oggetto|personaggio
		* 
		* @param 
		* 			tipo				string				'oggetto'|'personaggio'
		* 			param				array				statistiche
		* 
		* @return	risultato			string				Last inserted ID | error
		* 
		*/
		public function inserisciDM($tipo, $param) {
			
			$return = array(); $name = array(); $danno = ''; 
			$armi = ''; $incantesimi = ''; $abilita = '';$equipaggiamento = ''; $razza = array(); $classe = array();
			$caratteristiche = array();

			foreach ($param as $index2 => $value2) {
	
				foreach ($value2 as $index3 => $value3) {
					
					if ($index3 == 'value') $value[] = $value3;
						
					if ($index3 == 'name') $column = $name[] = str_replace("inserisci-" . $tipo . "-", "", $value3);

				}
				

				// TUTTI
				$return[$name[$index2]] = $value[$index2];
				
				
				
				
				
				
				switch ($tipo) {
					
					
					case 'oggetto':
					
						$danno = (!empty($return['numero-quantita'])) && (!empty($return['numero-dadi'])) ? '{"quantita":"' . $return['numero-quantita'] . '","dado":"' . $return['numero-dadi'] . '"}' : '';

						$return['danno'] = $danno;
						
					break;
					
					case 'personaggio':
					
						
					
					
						foreach ($return as $index3 => $value3) {
							

							if (substr($index3, 0, 15) == 'caratteristiche') {
								
								$count = true;

								$carattTemp = substr($index3, 16);
								
								// casi bonus
								if (strpos($carattTemp, "-") > 0) {
									
									$caratt = str_replace("bonus-", "", $carattTemp);
									$count = true;
								
								} else {
									$caratt = $carattTemp;
									$count = false;
								}
								
								$carattTemp = substr($caratt, 0, 3);
								
								
								if ($count) {
									if (!empty($value3)) $caratteristiche[$carattTemp]['bonus'] = $value3;
								} else {
									if (!empty($value3)) $caratteristiche[$carattTemp]['base'] = $value3;
								}

							}
							
							
							$tmp = (strpos($value3, " ") > 0) ? str_replace(" ", "", $value3) : '';
							
							switch ($index3) {
								
								case 'armi':
									$armi = !empty($tmp) ? explode(",", $tmp) : array();
								break;
								case 'incantesimi':
									$incantesimi = !empty($tmp) ? explode(",", $tmp) : array();
								break;
								case 'abilita':
									$abilita = !empty($tmp) ? explode(",", $tmp) : array();
								break;
								case 'equipaggiamento':
									$equipaggiamento = !empty($tmp) ? explode(",", $tmp) : array();
								break;
								case 'razza':
									$razza = !empty($value3) ? $value3 : array();
								break;
								case 'classe':
									$classe = !empty($value3) ? $value3 : array();
								break;
								
							}
						}
					break;	
				}
			}			
			
			
			
			
			$return['caratteristiche'] = (($tipo == 'personaggio') && (!empty($caratteristiche))) ? json_encode($caratteristiche) : '';
			
			if (!empty($armi)) $return['armi'] = json_encode($armi);
			if (!empty($incantesimi)) $return['incantesimi'] = json_encode($incantesimi);
			if (!empty($abilita)) $return['abilita'] = json_encode($abilita);
			if (!empty($equipaggiamento)) $return['equipaggiamento'] = json_encode($equipaggiamento);
			if (!empty($razza)) $return['razza'] = trim($razza);
			if (!empty($classe)) $return['classe'] = trim($classe);
			
			
			if (isset($return['caratteristiche-forza'])) unset($return['caratteristiche-forza']);
			if (isset($return['caratteristiche-bonus-forza'])) unset($return['caratteristiche-bonus-forza']);
			if (isset($return['caratteristiche-destrezza'])) unset($return['caratteristiche-destrezza']);
			if (isset($return['caratteristiche-bonus-destrezza'])) unset($return['caratteristiche-bonus-destrezza']);
			if (isset($return['caratteristiche-costituzione'])) unset($return['caratteristiche-costituzione']);
			if (isset($return['caratteristiche-bonus-costituzione'])) unset($return['caratteristiche-bonus-costituzione']);
			if (isset($return['caratteristiche-carisma'])) unset($return['caratteristiche-carisma']);
			if (isset($return['caratteristiche-bonus-carisma'])) unset($return['caratteristiche-bonus-carisma']);
			if (isset($return['caratteristiche-intelligenza'])) unset($return['caratteristiche-intelligenza']);
			if (isset($return['caratteristiche-bonus-intelligenza'])) unset($return['caratteristiche-bonus-intelligenza']);
			if (isset($return['caratteristiche-saggezza'])) unset($return['caratteristiche-saggezza']);
			if (isset($return['caratteristiche-bonus-saggezza'])) unset($return['caratteristiche-bonus-saggezza']);
			
			if (isset($return['numero-quantita'])) unset($return['numero-quantita']);
			if (isset($return['numero-dadi'])) unset($return['numero-dadi']);
			
			
			
			$risultato = $this->aggiungiDM($tipo, $return);

			return $risultato;

			
		}
		
		
		
		
		
		/**
		* funzione modificaDM: consente modifica di un oggetto|personaggio esistente
		*/
		public function modificaDM() {
			
			
			
		}
		
		
		
		
		
		
		public function getAbilityScore($json, $name, $type) {
			
			
			return $this->abilityScore($json, $name, $type);
			
			
		}
		
		
		
		
		/**
		* lancia Dadi
		* 
		* @param		int			$dice
		* 				int			$quantity			(se FALSE si intende 1)
		* 
		*/
		protected function diceroll($dice, $quantity = false) {
			
			
			if (!$quantity) {

				return mt_rand(1, $dice);
				
			} else {
				
				$result = 0;
				
				for ($i = 1; $i <= $quantity; $i++) {
					$result = $result + mt_rand(1, $dice);
				}
				
				return $result;
			}

		}
		

		
		/**
		* typeArray: costruzione ritorno per ogni chiamata
		*/
		protected function typeArray($type, $data) {
			
			
			$typeArray = json_encode(array(
			
				'type' => $type,
				'ID' => hash('whirlpool', time() . "latradizionedidomani"),
				'data' => $data
			
			));
			
			
			
			return $typeArray;
			
		}
		
		
		/**
		* funzione creaOggettiHTML
		*/
		protected function creaOggettiHTML($param, $armaScelta = false, $tabella = false) {
			
			
			$id = array(); $color = array(); $htmlPlayer = '';  $idTest = ''; $html = ''; $prova = array(); 
			$result = array(); $key = '';

			$stringaArmi = ''; $stringaIncantesimi = ''; $bonusTiro = ''; $danno = ''; $stringaAbilita = '';
			
			if ($tabella) {
				$stringa = '<table class="table"><thead><tr>
								<th scope="col">ID</th>
								<th scope="col">NOME</th>';
								
				$stringa .= array_key_exists("personaggi", $param) 
							 ? 
							'<th scope="col">GIOCATORE</th>
							<th scope="col">PRESENTE</th>
							<th scope="col">CLASSE</th>
							<th scope="col">RAZZA</th>
							<th scope="col">LIVELLO</th>
							<th scope="col">CARATTERISTICHE</th>
							<th scope="col">INIZIATIVA</th>
							<th scope="col">CA</th>
							<th scope="col">PF</th>
							<th scope="col">PE</th>
							<th scope="col">DENARO</th>
							<th scope="col">ARMI</th>
							<th scope="col">INCANTESIMI</th>
							<th scope="col">ABILITA</th>
							<th scope="col">EQUIPAGGIAMENTO</th>' 
							 : 
							'';
				$stringa .= array_key_exists("oggetti", $param)
							 ?
							'<th scope="col">PROPRIETARIO</th>
							<th scope="col">PRESENTE</th>
							<th scope="col">TIPO</th>
							<th scope="col">BONUS TIRO</th>
							<th scope="col">DANNO</th>
							<th scope="col">GITTATA</th>'
							: '';
				
				$stringa .= array_key_exists("abilita", $param)
							?
							'<th scope="col">DESCRIZIONE</th>
							<th scope="col">MODIFICATORE DI CARATTERISTICA</th>
							<th scope="col">PROVA CONTRAPPOSTA</th>
							<th scope="col">CONDIZIONI AGGIUNTIVE</th>'
							:
							'';
				
				$stringa .= '<th scope="col">AZIONE</th></tr></thead><tbody>';
			}
			

			if (!is_string($param)) {
				
				foreach ($param as $index => $value) {

					

					$result = $value;
					
					
					switch ($index) {
						
						case 'incantesimi':
						
							$nome = '';

							foreach ($value as $index2 => $value2) {


								foreach ($value2 as $index3 => $value3) {

									if ($index3 == 'nome') $nome = $value3;
									if ($index3 == 'bonus_tiro') $bonusTiro = $value3;
									if ($index3 == 'danno') $danno = json_decode($value3, JSON_OBJECT_AS_ARRAY);
									if ($index3 == 'id') $id = $value3;
			
								}
								

								if (($nome) && ($bonusTiro) && ($danno) && ($id)) {
									
									$stringaIncantesimi .= '<span id="list-incantesimi-' . $id . '">
												<button type="button" id="carica-incantesimi-' . $id . '" onclick="scegliIncantesimo(' . $id . ');closeModal(\'incantesimi\');">' . $nome . '</button>
												<p>ID: ' . $id . ' ' . $danno['quantita'] . 'd' . $danno['dado'] . ', bonus: ' . $bonusTiro . '</p>
											</span>';
						
								}

							}
							
							$result = $stringaIncantesimi;
						
						break;
						
						case 'abilita':

							$nome = $id = $descrizione = $modCaratt = $contrapp = $altreCond = $cdTmp = $altreCondTmp = array();
							
							foreach ($value as $index2 => $value2) {
								
								foreach ($value2 as $index3 => $value3) {

									if ($index3 == 'id') $id[] = $value3;
									if ($index3 == 'nome') $nome[] = $value3;
									if ($index3 == 'descrizione') $descrizione[] = $value3;
									if ($index3 == 'modificatore_caratteristica') $modCaratt[] = ucfirst($value3);
									if ($index3 == 'prova_contrapposta') {
										// elimimina caratteri di punteggiatura
										$contrapp[] = ($value3 !== null) ? preg_replace("/[[:punct:]]/", "", $value3) : "Nessuna";
									}
									
									if ($index3 == 'cd') {
										$cdTmp = ($value3 !== null) ? $value3 : 'Nessuna';
									} 
									
									if ($index3 == 'condizioni_aggiuntive') {
										$altreCondTmp = ($value3 !== null) ? $value3 : "Nessuna";
									}
									
								}
								
								$altreCond = $cd = '';
								
								// classe difficoltà
								if ($cdTmp !== "Nessuna") {
									$tmp = json_decode($cdTmp, true);
									
									
									foreach ($tmp as $index3 => $index3) {
										$cd .= 'CD: ' . $tmp[$index3]['cd'] . ', azione: ' . $tmp[$index3]['azione'] . '<br>';
									}
									
								} else $cd = $cdTmp;
								
								
								// condizioni aggiuntive
								if ($altreCondTmp !== "Nessuna") {
									$tmp = json_decode($altreCondTmp, true);
									foreach ($tmp as $index3 => $index3) {
										$altreCond .= 'CD: ' . $tmp[$index3]['cd'] . ', azione: ' . $tmp[$index3]['azione'] . '<br>';
									}
									
								} else $altreCond = $altreCondTmp;
								
								
								
								$stringaAbilita .= '<span id="list-abilita-' . $id[$index2] . '">
														<b>NOME: </b><p> ' . $nome[$index2] . '</p><br>
														<b>DESCRIZIONE: </b><p>' . $descrizione[$index2] . '</p><br>
														<b>CARATTERISTICA: </b><p>' . $modCaratt[$index2] . '</p><br>
														<b>PROVA CONTRAPPOSTA: </b><p>' . $contrapp[$index2] . '</p><br>
														<b>CLASSE DIFFICOLTA\': </b><p>' . $cd . '</p><br>
														<b>CONDIZIONI AGGIUNTIVE: </b><p>' . $altreCond . '</p><br>
														<hr><br>
								
								';
								
								
							}
						
							$result = $stringaAbilita;
						
						break;
						
						case 'armi':
						
							$nome = ''; 
							
							foreach ($value as $index2 => $value2) {
								
								foreach ($value2 as $index3 => $value3) {
									
									
									if ($index3 == 'nome') $nome = $value3;
									if ($index3 == 'bonus_tiro') $bonusTiro = $value3;
									if ($index3 == 'danno') $danno = json_decode($value3, JSON_OBJECT_AS_ARRAY);
									if ($index3 == 'id') $id = $value3;
									
									// se utente sceglie arma da impugnare
									if (($armaScelta) && ($value3 == $armaScelta)) {

										if ($index3 == 'id') $_SESSION['data']['giocatore']['manoDestra'] = $value3;
										
										if ($index3 == 'azione') $_SESSION['data']['giocatore']['tipoManoDestra'] = $value3;

									}
									
									
									
								}

								$stringaArmi .= '<span id="list-armi-' . $id . '">
													<button type="button" id="carica-armi-' . $id . '" onclick="scegliArma(' . $id . ');closeModal(\'armi\');">' . $nome . '</button>
													<p>ID: ' . $id . ' ' . $danno['quantita'] . 'd' . $danno['dado'] . ', bonus: ' . $bonusTiro . '</p>
												</span>';

							}
						
							$result = $stringaArmi;
						
						break;
						
						case 'personaggi':
						
							$nome = array(); $giocatore = array(); $presente = array(); $classe = array(); $razza = array(); $livello = array(); $caratteristiche = array(); 
							$iniziativa = array(); $ca = array(); $pf = array(); $pe = array(); $denaro = array(); $armi = array(); $incantesimi = array(); $abilita = array(); $equipaggiamento = array(); 
							
							
							foreach ($value as $index2 => $value2) {

								foreach ($value2 as $index3 => $value3) {
									
									
									
									if ($tabella) {

										if ($index3 == 'id') $id[] = $value3;
										if ($index3 == 'nome') $nome[] = $value3;
										if ($index3 == 'giocatore') $giocatore[] = $value3;
										if ($index3 == 'presente') $presente[] = $value3;
										if ($index3 == 'classe') $classe[] = $value3;
										if ($index3 == 'razza') $razza[] = $value3;
										if ($index3 == 'livello') $livello[] = $value3;
										if ($index3 == 'caratteristiche') {
											// es. 'for' . 
											$tmp = json_decode($value3, JSON_OBJECT_AS_ARRAY); $label = array(); $base = array(); $bonus = array(); $tmpText = '';
											
											foreach ($tmp as $x => $y) {
												
												foreach ($y as $x2 => $y2) {
													if ($x2 == 'base') $base[] = $y2;
													if ($x2 == 'bonus') $bonus[] = $y2;
												}
												
												$label[] = $x;
												
											}
											
											foreach ($label as $x => $y) {
												$tmpText .= "<b>" .  $y . "</b>: " . $base[$x] . " +" . $bonus[$x] . "<br>";
											
											}
											
											$caratteristiche[] = $tmpText;
											
											 
										}
										if ($index3 == 'iniziativa') $iniziativa[] = $value3;
										if ($index3 == 'ca') $ca[] = $value3;
										if ($index3 == 'pf') $pf[] = $value3;
										if ($index3 == 'pe') $pe[] = $value3;
										if ($index3 == 'denaro') $denaro[] = $value3;
										// da semplificare
										if ($index3 == 'armi') {
											
											$tmp = json_decode($value3, JSON_OBJECT_AS_ARRAY); $tmpText = '';
											
								
											foreach ($tmp as $x => $y) {
												
												$tmpText .= $y . ", ";
												
											}

											$armi[] = $tmpText;
											
										}
										
										if ($index3 == 'incantesimi') {
											
											$tmp = json_decode($value3, JSON_OBJECT_AS_ARRAY); $tmpText = '';
											
								
											foreach ($tmp as $x => $y) {
												
												$tmpText .= $y . ", ";
												
											}

											$incantesimi[] = $tmpText;
											
										}
										if ($index3 == 'abilita') {
											
											$tmp = json_decode($value3, JSON_OBJECT_AS_ARRAY); $tmpText = '';
											
								
											foreach ($tmp as $x => $y) {
												
												$tmpText .= $y . ", ";
												
											}

											$abilita[] = $tmpText;
											
										}
										if ($index3 == 'equipaggiamento') {
											
											$tmp = json_decode($value3, JSON_OBJECT_AS_ARRAY); $tmpText = '';
											
								
											foreach ($tmp as $x => $y) {
												
												$tmpText .= $y . ", ";
												
											}

											$equipaggiamento[] = $tmpText;
											
										}
				
				
										
									} else {
										
										if ($index3 == 'giocatore') {
											
											if ($value3 !== 'npc') {
												
												$color[] = 'player bg-danger';
												$idTest = 'link-attacca-player';
												
											} else {
												
												$color[] = 'npc enemy bg-black';
												$idTest = 'link-attacca-npc';
												
											}
										
										}
									
										
										if ($index3 == 'id') {
											$idTest .= $value3;
											$id[] = $value3;
										}
										
										if ($index3 == 'nome') {
											$nome[] = $value3;
										}
										
									}


								}	
								
								
								
								
								
								if ($tabella) $stringa .= '<tr>
														<td>' . $id[$index2] . '</td>
														<td>' . $nome[$index2] . '</td>
														<td>' . $giocatore[$index2] . '</td>
														<td>' . $presente[$index2] . '</td>
														<td>' . $classe[$index2] . '</td>
														<td>' . $razza[$index2] . '</td>
														<td>' . $livello[$index2] . '</td>
														<td>' . $caratteristiche[$index2] . '</td>
														<td>' . $iniziativa[$index2] . '</td>
														<td>' . $ca[$index2] . '</td>
														<td>' . $pf[$index2] . '</td>
														<td>' . $pe[$index2] . '</td>
														<td>' . $denaro[$index2] . '</td>
														<td>' . $armi[$index2] . '</td>
														<td>' . $incantesimi[$index2] . '</td>
														<td>' . $abilita[$index2] . '</td>
														<td>' . $equipaggiamento[$index2] . '</td>
										
														<td><button type="button" id="carica-personaggi-' . $id[$index2] . '" onclick="scegliPersonaggio(' . $id[$index2] . ');closeModal(\'personaggi\');">MODIFICA</button></td>
													
													</tr>';
										
										
								
								
								
							}
						
						break;
						
						case 'oggetti':
						
							// tabella con TUTTO e ultima colonna btn x select
							$nomeOggetto = array(); $id = array(); $bonusTiro = array(); $danno = array(); $proprietario = array(); $presente = array(); $tipo = array(); $gittata = array();

							
							foreach ($value as $index2 => $value2) {


								foreach ($value2 as $index3 => $value3) {
									
									if ($index3 == 'id') $id[] = $value3;
									if ($index3 == 'nome') $nomeOggetto[] = $value3;
									if ($index3 == 'proprietario') $proprietario[] = $value3;
									if ($index3 == 'presente') $presente[] = ($value3 == '1') ? 'Sì' : 'No';
									if ($index3 == 'azione') $tipo[] = $value3;
									if ($index3 == 'bonus_tiro') $bonusTiro[] = $value3;
									
									if ($index3 == 'danno') {
										
										$tmp = json_decode($value3, JSON_OBJECT_AS_ARRAY); $dado = array(); $quantita = array(); 
										
										foreach ($tmp as $x => $y) {
											
											if ($x == 'dado') $dado[] = $y;
											if ($x == 'quantita') $quantita[] = $y;

										}
										
										$danno[] = '<p>' . $quantita[0] . 'd' . $dado[0] . '</p>';
										
									} 
									
									if ($index3 == 'gittata') $gittata[] = (!empty($value3)) ? $value3 . " mt" : '';


								}


								$stringa = '<tr>
											<td>' . $id[$index2] . '</td>
											<td>' . $nomeOggetto[$index2] . '</td>
											<td>' . $proprietario[$index2] . '</td>
											<td>' . $presente[$index2] . '</td>
											<td>' . $tipo[$index2] . '</td>
											<td>' . $bonusTiro[$index2] . '</td>
											<td>' . $danno[$index2] . '</td>
											<td>' . $gittata[$index2] . '</td>
						
									<td><button type="button" id="carica-oggetti-' . $id[$index2] . '" onclick="scegliOggetto(' . $id[$index2] . ');closeModal(\'oggetti\');">MODIFICA</button></td>
									
								</tr>';



							}
							

						break;
						
					}
					

				}

			} else $result = $param;
			



			if ($tabella) {
				$stringa .= '</tbody></table>';
				$result = $stringa;
			}




			
			if (isset($param['giro'])) {
				
				$count = -1;
				
				// giro
				foreach ($param['giro'] as $index4 => $value4) {
						
					$count++;
	
					// 'attacca' al posto di 'nemico'
					$htmlPlayer .= '<a href="javascript:void(0);" class="' . $color[$count] . '" id="pg-' . $index4 . '" onclick="selezionaPG(' . $id[$count] . ', \'attacca\');closeModal(\'nemico\');">
							<label for="' . $index4 . '">' . $nome[$count] . '</label>
						</a>';
                    
				}
				
			}

			if ($htmlPlayer) {
				return $htmlPlayer;
			} else {
				return $result;
			}

		}
		
		
		
		/**
		* funzione updateSessionData(): aggiorna un dato relativo ad uno user 
		* all'interno della sessione
		*/
		public function updateSessionData($user, $data, $value) {
			
			$return = '';
			
			if ((int)$user < 1) {
				
				$return = "ERRORE 35: utente non trovato.";
				
			} else {
				
				
				$_SESSION['data']['giocatore'][$data] = $value;

				
				$return = 'OK';
				
			}
			
			
			return $return;
			
		}
		
		
		/**
		* funzione resetAttaccoBuffer: azzera tabella temporanea x giro attacco
		*/
		protected function resetAttaccoBuffer() {
			

			$Query = 'TRUNCATE TABLE attacco_tmp';
			
			$stmt = $this->db->prepare($Query);
			$stmt->execute();
			
			
		}
		
		
		/**
		* funzione leggiPianoDiAttacco: preleva i dati caricati su DB relativi al combattimento
		*/
		protected function leggiPianoDiAttacco() {
			
			$Query = 'SELECT * FROM attacco_tmp';
			$stmt = $this->db->prepare($Query);
			$query = $stmt->execute();
			
			if ($stmt->rowCount() > 0) {

				$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
				
			} else $result = 'Combattimento non in esecuzione';
			
			return $result;
		}
		

		/**
		* funzione pianoDiAttaccoBuffer: inserisce giocatore all'interno della tabella temporanea di attacco
		*/
		protected function pianoDiAttaccoBuffer($param) {
			
			if ((isset($_SESSION['data']['giro'])) && ($param)) {
				
				
				$return = 'false';
				
				
				$Query = 'INSERT INTO attacco_tmp (id_giocatore, nome, giocatore, caratteristiche, iniziativa, iniziativa_giro, ca, pf, armi, incantesimi, abilita, equipaggiamento) 
							VALUES (:id_giocatore, :nome, :giocatore, :caratteristiche, :iniziativa, :iniziativa_giro, :ca, :pf, :armi, :incantesimi, :abilita, :equipaggiamento)';
							

				$stmt = $this->db->prepare($Query);

					
				$stmt->bindParam(':id_giocatore', $param['id'], PDO::PARAM_INT);
				$stmt->bindParam(':nome', $param['nome'], PDO::PARAM_STR);
				$stmt->bindParam(':giocatore', $param['giocatore'], PDO::PARAM_STR);
				$stmt->bindParam(':caratteristiche', $param['caratteristiche'], PDO::PARAM_STR);
				$stmt->bindParam(':iniziativa', $param['iniziativa'], PDO::PARAM_STR);
				$stmt->bindParam(':iniziativa_giro', $param['iniziativa_giro'], PDO::PARAM_STR);
				$stmt->bindParam(':ca', $param['ca'], PDO::PARAM_STR);
				$stmt->bindParam(':pf', $param['pf'], PDO::PARAM_STR);
				$stmt->bindParam(':armi', $param['armi'], PDO::PARAM_STR);
				$stmt->bindParam(':incantesimi', $param['incantesimi'], PDO::PARAM_STR);
				$stmt->bindParam(':abilita', $param['abilita'], PDO::PARAM_STR);
				$stmt->bindParam(':equipaggiamento', $param['equipaggiamento'], PDO::PARAM_STR);
				

				
				$return = ($stmt->execute()) ? "true" : $this->db->errorCode();
			

				return $return;
				
				
			}
			
		}
		
		/**
		* funzione aggiungiDM: aggiunge un oggetto|personaggio nel db
		*/
		protected function aggiungiDM($tipo, $param) {
			
			$risultato = '';
			$table = ($tipo == 'personaggio') ? 'personaggi' : 'oggetti';
			$columns = ($tipo == 'personaggio') ? 
						"(nome, giocatore, presente, classe, razza, livello, caratteristiche, iniziativa, ca, pf, pe, denaro, armi, incantesimi, abilita, equipaggiamento)" :
						"(nome, proprietario, presente, azione, bonus_tiro, danno, gittata)";
			$values = ($tipo == 'personaggio') ? 
						"(:nome, :giocatore, :presente, :classe, :razza, :livello, :caratteristiche, :iniziativa, :ca, :pf, :pe, :denaro, :armi, :incantesimi, :abilita, :equipaggiamento)" :
						"(:nome, :proprietario, :presente, :azione, :bonus_tiro, :danno, :gittata)";
			
			$Query = 'INSERT INTO ' . $table . ' ' . $columns . ' VALUES ' . $values;
			
			
			try {
				
				$stmt = $this->db->prepare($Query);
				$stmt->bindParam(':nome', $param['nome'], PDO::PARAM_STR);
				$stmt->bindParam(':presente', $param['presente'], PDO::PARAM_INT);
				
				if ($tipo == 'personaggio') {
					$stmt->bindParam(':giocatore', $param['giocatore'], PDO::PARAM_STR);
					$stmt->bindParam(':classe', $param['classe'], PDO::PARAM_STR);
					$stmt->bindParam(':razza', $param['razza'], PDO::PARAM_STR);
					$stmt->bindParam(':livello', $param['livello'], PDO::PARAM_STR);
					$stmt->bindParam(':caratteristiche', $param['caratteristiche'], PDO::PARAM_STR);
					$stmt->bindParam(':iniziativa', $param['iniziativa'], PDO::PARAM_STR);
					$stmt->bindParam(':ca', $param['ca'], PDO::PARAM_STR);
					$stmt->bindParam(':pf', $param['pf'], PDO::PARAM_STR);
					$stmt->bindParam(':pe', $param['pe'], PDO::PARAM_STR);
					$stmt->bindParam(':denaro', $param['denaro'], PDO::PARAM_STR);
					$stmt->bindParam(':armi', $param['armi'], PDO::PARAM_STR);
					$stmt->bindParam(':incantesimi', $param['incantesimi'], PDO::PARAM_STR);
					$stmt->bindParam(':abilita', $param['abilita'], PDO::PARAM_STR);
					$stmt->bindParam(':equipaggiamento', $param['equipaggiamento'], PDO::PARAM_STR);
				} else {
					$stmt->bindParam(':proprietario', $param['proprietario'], PDO::PARAM_STR);
					$stmt->bindParam(':azione', $param['azione'], PDO::PARAM_STR);
					$stmt->bindParam(':bonus_tiro', $param['bonus_tiro'], PDO::PARAM_STR);
					$stmt->bindParam(':danno', $param['danno'], PDO::PARAM_STR);
					$stmt->bindParam(':gittata', $param['gittata'], PDO::PARAM_STR);
				}
				
				$stmt->execute();
				
				$query = $this->db->query($Query);
				
				$risultato = $this->db->lastInsertId();
				
			} catch (Exception $e) {
				
				$risultato = "ERRORE: " . $e->getMessage();
				
			}
			
			return $risultato;
			
		}
		
		
		
		/**
		* caricaPersonaggio: carica stats del personaggio
		*/
		public function caricaPersonaggio($giocatore) {

			$Query = 'SELECT * FROM personaggi WHERE giocatore = :giocatore';
			$stmt = $this->db->prepare($Query);
			$stmt->bindParam(':giocatore', $giocatore, PDO::PARAM_STR);
			$stmt->execute();
			
			$query = $this->db->query($Query);
		
		
		
			if ($stmt->rowCount() == 0) {
				
				$result = "Personaggio non trovato";
				
			} else {

				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				
			}
			
			
			return $result;
		
			
		}
		
		
		/**
		* caricaPersonaggi: carica tutti i personaggi presenti nella mappa
		*/
		public function caricaPersonaggi($all = false) {
			
			$testo = '';
			
			if ($all) $testo = ' WHERE presente = "1" and pf <> "0"';
			
			$Query = 'SELECT * FROM personaggi' . $testo;
			$stmt = $this->db->prepare($Query);
			$stmt->execute();
			
			$query = $this->db->query($Query);
		
		
		
			if ($stmt->rowCount() == 0) {
				
				
				$result = "Personaggio non trovato";
				
			} else {

				$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

			}
			
			
			if ($all) {
				
				$param = array(
					'personaggi' => $result
				);
				
				$risultato = $this->creaOggettiHTML($param, false, true);
				
			} else $risultato = $result;
			

			return $risultato;
			
		}
		
		
		/**
		* caricaOggetto: carica un solo oggetto/incantesimo scelto dal giocatore
		*/
		public function caricaOggetto($id) {
			

			$Query = 'SELECT * FROM oggetti WHERE id = :id and presente = "1"';
			$stmt = $this->db->prepare($Query);
			$stmt->bindParam(':id', $id, PDO::PARAM_STR);
			$stmt->execute();
			
			$query = $this->db->query($Query);

			if ($stmt->rowCount() == 0) {

				$result = "Oggetto non trovato";
				
			} else {

				$result = $stmt->fetch(PDO::FETCH_ASSOC);

			}

			$return = json_encode($result);
			
			return $return;
			
		}
		
		
		/**
		* caricaOggettiDaProprietario: carica oggetti/incantesimi di un giocatore
		* 
		* @param			id					string				id proprietario
		* 					stringa				bool				se true imposta output per lettura a video
		* 					tipoOggetto			string				azione dell'oggetto da tabella
		* 					armaScelta			string				id dell'arma scelta dal giocatore
		* 
		*/
		public function caricaOggettiDaProprietario($id, $stringa = false, $tipoOggetto = false, $armaScelta = false) {
			
			$oggettoQuery = ($tipoOggetto) && ($stringa) ? ' and azione = "' . $tipoOggetto . '"' : '';
			$Query = 'SELECT * FROM oggetti WHERE proprietario = :id and presente = "1"' . $oggettoQuery;
			$risultato = '';
			try {
				
				$stmt = $this->db->prepare($Query);
				$stmt->bindParam(':id', $id, PDO::PARAM_STR);
				$stmt->execute();
			
				$query = $this->db->query($Query);
				
	
				$result = (($stringa) && ($tipoOggetto)) ? array($tipoOggetto => $stmt->fetchAll(PDO::FETCH_ASSOC)) : $stmt->fetchAll(PDO::FETCH_ASSOC);

				if ($stringa) {
				
					// $risultato = ($armaScelta) ? $this->creaOggettiHTML($result, $armaScelta) : $this->creaOggettiHTML($result);
				
					$risultato = $this->creaOggettiHTML($result);
				
				
				} else $risultato = $result;
				
			} catch (Exception $e) {
				
				$risultato .= "ERRORE: " . $e->getMessage();
				
				
			}

			return $risultato;
			

			
		}
		
		
		/**
		* caricaOggetti: carica tutti gli oggetti presenti nella mappa
		*/
		public function caricaOggetti($all = false) {
			
			$testo = '';
			
			if ($all) {
				$testo = ' WHERE presente = "1"';
			}
			
			$Query = 'SELECT * FROM oggetti' . $testo;

			try {
				
				$stmt = $this->db->prepare($Query);
				$stmt->execute();
				
				$query = $this->db->query($Query);
				
				$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
				
				
				if ($all) {
					
					$param = array(
						'oggetti' => $result
					);
					
					$risultato = $this->creaOggettiHTML($param, false, true);
					
				} else $risultato = $result;
				
				
				
			} catch (Exception $e) {

				// da mettere a posto: conviene farlo in stringa???
				$result = array();
			}

		

			return $risultato;
			
			
		}
		
		
		
		
		
		/**
		* caricaOggetto: carica una sola abilità scelta dal giocatore
		*/
		public function caricaAbilita($id) {
			

			$Query = 'SELECT * FROM abilita WHERE id = :id';
			$stmt = $this->db->prepare($Query);
			$stmt->bindParam(':id', $id, PDO::PARAM_STR);
			$stmt->execute();
			
			$query = $this->db->query($Query);

			if ($stmt->rowCount() == 0) {

				$result = "Abilità non trovata";
				
			} else {

				$result = $stmt->fetch(PDO::FETCH_ASSOC);

			}
			

			$return = json_encode($result);
			
			return $return;
			
		}
		
		
		/**
		* caricaAbilitaDaGiocatore: carica tutte le abilità di un giocatore
		* 
		* @param			id					string				id proprietario
		* 					stringa				bool				se true imposta output per lettura a video
		* 					tipoOggetto			string				azione dell'oggetto da tabella
		* 					armaScelta			string				id dell'arma scelta dal giocatore
		* 
		*/
		public function caricaAbilitaDaGiocatore($id, $stringa = false, $tipoOggetto = false, $armaScelta = false) {
			
			$oggettoQuery = ($tipoOggetto) && ($stringa) ? ' and azione = "' . $tipoOggetto . '"' : '';
			$Query = 'SELECT * FROM abilita WHERE proprietario = :id' . $oggettoQuery;
			$risultato = '';
			
			
			try {
				
				$stmt = $this->db->prepare($Query);
				$stmt->bindParam(':id', $id, PDO::PARAM_STR);
				$stmt->execute();
			
				$query = $this->db->query($Query);
				
	
				$result = (($stringa) && ($tipoOggetto)) ? array($tipoOggetto => $stmt->fetchAll(PDO::FETCH_ASSOC)) : $stmt->fetchAll(PDO::FETCH_ASSOC);
// return $result;die();
				if ($stringa) {
				
					// $risultato = ($armaScelta) ? $this->creaOggettiHTML($result, $armaScelta) : $this->creaOggettiHTML($result);
				
					$risultato = $this->creaOggettiHTML($result);
				
				
				} else $risultato = $result;
				
			} catch (Exception $e) {
				
				$risultato .= "ERRORE: " . $e->getMessage();
				
				
			}
			
			
			return $risultato;
			

			
		}
		
		
		
		
		
		
		
		
		
		
		/**
		* aggiornaStats: UPDATE personaggi
		* 
		* @param 			string			$personaggio
		* 					array			$stats
		* 
		*/
		private function aggiornaStats($personaggio, $stats) {
			
			
			
		}
		
		
		
		/**
		* 
		*/ 
		public function test() {
           
            return "PROVA";
            
		}
		
		
	}





?>
