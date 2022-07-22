<?php

	

	class BATTLEGRID {
		
		
		private $db;
		
		public function __construct($param) {
			
			$this->db = isset($param['db']) && !empty($param['db']) ? $param['db'] : [];
			
			
		}
		
		
	
	
		
		public function get($id) {
			

			$result = (!empty($id)) ? $this->getPosFromId($id) : array();
			
			return $result;
		}
		
		
		public function put($html_id, $pos_x, $pos_y) {
			
			$result = (!empty($html_id) && !empty($pos_x) && !empty($pos_y)) ? $this->putPos($html_id, $pos_x, $pos_y) : array();
			
			return $result;
		}
		
		public function update($html_id, $pos_x, $pos_y) {
			
			$result = (!empty($html_id) && !empty($pos_x) && !empty($pos_y)) ? $this->updatePos($html_id, $pos_x, $pos_y) : array();
			
			return $result;
		}
		
		
		public function remove($id) {
			
			$result = (!empty($id)) ? $this->deletePosFromId($id) : array();
			
			return $result;
		}
		
		
		
		
		private function checkIfElemPresent($html_id) {
			
			$Query = 'SELECT * FROM grid_pos WHERE html_id = :html_id';
			
			$stmt = $this->db->prepare($Query);
			$stmt->bindParam(":html_id", $html_id, PDO::PARAM_STR);
			$stmt->execute();
			
			$result = $stmt->rowCount();
			
			
			return $result;
			
		}
		
		
		
		
		private function getPosFromId($html_id) {
			
			$present = $this->checkIfElemPresent($html_id);


			if ($present == 1) {
				$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			} else {
				
				$result = array('pos_x' => '', 'pos_y' => '');
			}
			
			
			return $result;
			
		}
		
		
		private function putPos($html_id, $posX, $posY) {
			
			$Query = 'INSERT INTO grid_pos (html_id, pos_x, pos_y) VALUES (:html_id, :pos_x, :pos_y)';

			$stmt = $this->db->prepare($Query);
			$stmt->bindParam(":html_id", $html_id, PDO::PARAM_STR);
			$stmt->bindParam(":pos_x", $posX, PDO::PARAM_STR);
			$stmt->bindParam(":pos_y", $posY, PDO::PARAM_STR);
			$stmt->execute();
			
			if ($this->db->lastInsertId() > 0) {
				$result = array("status" => 'ok');
			} else {
				$result = array("error" => 'impossibile salvare le posizioni');
			}
			
			
			return $result;
		}
		
		
		private function updatePos($html_id, $posX, $posY) {
			
			$present = $this->checkIfElemPresent($html_id);
			
			if ($present == 1) {
				
				$Query = 'UPDATE grid_pos SET pos_x = :pos_x, pos_y = :pos_y WHERE html_id = :html_id';

				$stmt = $this->db->prepare($Query);
				$stmt->bindParam(":html_id", $html_id, PDO::PARAM_STR);
				$stmt->bindParam(":pos_x", $posX, PDO::PARAM_STR);
				$stmt->bindParam(":pos_y", $posY, PDO::PARAM_STR);
				$stmt->execute();
				
				
				$result = array("status" => 'ok');

			} else {
				
				$result = $this->putPos($html_id, $posX, $posY);
				
			}
			
			return $result;
		
		}
		
		
		private function deletePosFromId($html_id) {
			
			$Query = 'DELETE grid_pos WHERE html_id = :html_id';

			$stmt = $this->db->prepare($Query);
			$stmt->bindParam(":html_id", $html_id, PDO::PARAM_STR);
			$stmt->execute();
			
			return array("status" => 'ok');
		}
		
	}
	
	
?>