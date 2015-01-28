<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modelitem');
JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_together/tables');
 
class TogetherModelTogether extends JModelItem {
	
	protected $listadoAntecedentes = null;
	protected $listadoMedidas = null;

	/**
	 * Retorna la constante segun el caso.
	 * @param string $genero
	 * @param integer $x
	 * @param integer $y
	 * @return double
	 */
	public function getConstant($genero, $x, $y) {
		try {
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select('constante');
			$query->from('#__imc_constants');
			$query->where("genero='".$genero."'")->where("x=".round($x, 0))->where("y=".round($y, 0));
			$db->setQuery((string)$query);
			$val = $db->loadObject();
			return isset($val->constante) ? $val->constante : 0;
		} catch (Exception $ex) {
			error_log($ex->getMessage());
			return -1;
		}
	}
	
	public function getListadoAntecedentes() {
		try {
			if ($this->listadoAntecedentes) {
				return $this->listadoAntecedentes;
			}
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from('#__together_antecedente');
			$query->where("activo = 1");
			$query->order("cuales, antecedente");
			$db->setQuery((string)$query);
			$this->listadoAntecedentes = $db->loadObjectList();
			return $this->listadoAntecedentes;
		} catch (Exception $ex) {
			error_log($ex->getMessage());
			return -1;
		}
	}
	
	public function getListadoMedidas() {
		try {
			if ($this->listadoMedidas) {
				return $this->listadoMedidas;
			}
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from('#__together_medida');
			$query->where("activo = 1");
			$query->order("medida");
			$db->setQuery((string)$query);
			$this->listadoMedidas = $db->loadObjectList();
			return $this->listadoMedidas;
		} catch (Exception $ex) {
			error_log($ex->getMessage());
			return -1;
		}
	}
	
	public function getAlimentos($uid, $tipo, $limit) {
		try {
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select('st.subtipo')->select('al.nombre')->select('al.valor as valor')->select('al.unidad');
			$query->from('#__together_alimentos al')->from('#__together_subtipo st');
			$query->where("al.subtipo = st.id")->where("st.tipo=" . $tipo);
			$query->order("rand() limit " . $limit);
			$db->setQuery((string)$query);
			return $db->loadObjectList();
		} catch (Exception $ex) {
			error_log($ex->getMessage());
			return -1;
		}
	}
	
	public function registrarUsuarioJoomla($data) {
		jimport('joomla.application.component.helper');
		jimport('joomla.user.helper');
		$acl =& JFactory::getACL();
		$usersParams = &JComponentHelper::getParams( 'com_users' );
		$user = clone(JFactory::getUser(0));
		$data['groups'] = [10 => 10];
		$password = JUserHelper::genRandomPassword(8);
		$data['password'] = $password;
		$data['password2'] = $password;
		$data['sendEmail'] = 1;
		$data['guest'] = 0;
		$data['block'] = 0;
		$data['activation'] =JApplication::getHash(JUserHelper::genRandomPassword());
		if (!$user->bind($data) || !$user->save()) {
			throw new Exception(JText::_($user->getError()));
		}
		return $user;
	}
	
	public function buscarPersona($documento) {
		try {
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from('#__together_persona');
			$query->where("documento = '" . $documento . "'");
			$db->setQuery((string)$query);
			$per = $db->loadObject();
			return $per;
		} catch (Exception $ex) {
			error_log($ex->getMessage());
			return -1;
		}
	}
	
	public function registrarPersona($data, $update = false) {
		try {
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$fecha = date_create_from_format('d/m/Y', $data['nacimiento']);
			if (!$fecha) {
				throw new Exception(JText::_('Fecha no valida'));
			}
			$data['nacimiento'] = $fecha->getTimestamp();
			if (!$update) {
				$query->insert('#__together_persona')->columns('uid, documento, tipo_documento, fecha_nacimiento, genero, telefono, profesion, afisica')->values($data['uid'].",'".$data['documento']."','".$data['tipo_documento']."',".$data['nacimiento'].",'".$data['genero']."','".$data['telefono']."','".$data['profesion']."',".$data['afisica']);
			} else {
				$query->update('#__together_persona')->
					set("documento='" . $data['documento'] . "'")->
					set("tipo_documento='" . $data['tipo_documento'] . "'")->
					set("fecha_nacimiento=" . $data['nacimiento'])->
					set("genero='" . $data['genero'] . "'")->
					set("telefono='" . $data['telefono'] . "'")->
					set("profesion='" . $data['profesion'] . "'")->
					set("afisica=" . $data['afisica'])->
					where("uid='" . $data['uid'] . "'");
			}
			$db->setQuery((string)$query);
			return $db->execute();
		} catch (Exception $ex) {
			throw new Exception(JText::_($ex->getMessage()));
		}
	}
	
	public function registrarAntecedentes($data) {
		$db = JFactory::getDBO();
		$db->transactionStart();
		try {
			for ($i = 0; $i < count($data); $i ++) {
				if ($i == 0) {
					$query = $db->getQuery(true);
					$query->delete('#__together_persona_antecedente')->where('uid=' + $data[$i]['uid']);
					$db->setQuery((string)$query);
					if (!$db->execute()) {
						throw new Exception(JText::_('[ANT] No se logro eliminar los registros anteriores'));
					}
				}
				$query = $db->getQuery(true);
				$query->insert('#__together_persona_antecedente')->columns('uid, id_antecedente, cuales')->values($data[$i]['uid'] . "," . $data[$i]['antecedente'] . ",'" . $data[$i]['cuales'] . "'");
				$db->setQuery((string)$query);
				if (!$db->execute()) {
					throw new Exception(JText::_('[ANT] No se logro registrar el antecedente.'));
				}
			}
			$db->transactionCommit();
		} catch (Exception $ex) {
			$db->transactionRollback();
			throw new Exception(JText::_($ex->getMessage()));
		}
	}
	
	public function registrarMedidas($data) {
		$db = JFactory::getDBO();
		$db->transactionStart();
		try {
			for ($i = 0; $i < count($data); $i ++) {
				if ($i == 0) {
					$query = $db->getQuery(true);
					$query->delete('#__together_persona_medida')->where('uid=' + $data[$i]['uid']);
					$db->setQuery((string)$query);
					if (!$db->execute()) {
						throw new Exception(JText::_('[MED] No se logro eliminar los registros anteriores'));
					}
				}
				$query = $db->getQuery(true);
				$query->insert('#__together_persona_medida')->columns('uid, id_medida, valor, fecha')->values($data[$i]['uid'] . "," . $data[$i]['medida'] . "," . $data[$i]['valor'] . ", now()");
				$db->setQuery((string)$query);
				if (!$db->execute()) {
					throw new Exception(JText::_('[MED] No se logro registrar el antecedente.'));
				}
			}
			$db->transactionCommit();
		} catch (Exception $ex) {
			$db->transactionRollback();
			throw new Exception(JText::_($ex->getMessage()));
		}
	}
	
	public function buscarAntecedentes($uid) {
		try {
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select('id_antecedente, cuales');
			$query->from('#__together_persona_antecedente');
			$query->where("uid = " . $uid);
			$db->setQuery((string)$query);
			return $db->loadObjectList();
		} catch (Exception $ex) {
			error_log($ex->getMessage());
			return -1;
		}
	}
	
	public function buscarMedidas($uid) {
		try {
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select('id_medida, valor');
			$query->from('#__together_persona_medida');
			$query->where("uid = " . $uid);
			$db->setQuery((string)$query);
			return $db->loadObjectList();
		} catch (Exception $ex) {
			error_log($ex->getMessage());
			return -1;
		}
	}
	
	public function getMedida($uid, $medida) {
		try {
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select('valor');
			$query->from('#__together_persona_medida');
			$query->where("uid = " . $uid)->where("id_medida = " . $medida);
			$db->setQuery((string)$query);
			$val = $db->loadObject();
			return $val ? $val->valor : 0;
		} catch (Exception $ex) {
			error_log($ex->getMessage());
			return 0;
		}
	}
	
	public function getAFisica($id) {
		try {
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select('valor');
			$query->from('#__together_afisica');
			$query->where("id = " . $id);
			$db->setQuery((string)$query);
			$val = $db->loadObject();
			return $val ? $val->valor : 0;
		} catch (Exception $ex) {
			error_log($ex->getMessage());
			return 0;
		}
	}
	
}