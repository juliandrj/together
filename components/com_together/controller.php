<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

class TogetherController extends JControllerLegacy {
	
	const MASCULINO = 'M';
	const FEMENINO = 'F';
	
	const CADERA = 1;
	const CINTURA = 2;
	const TALLA = 3;
	
	const PROTREINAS = 1;
	const CARBOHIDRATOS = 2;
	const GRASAS = 3;
	
	const GR_PROTEINAS = 7;
	const GR_CARBOHIDRATOS = 9;
	const GR_GRASAS = 12;
	
	const NUMERO_COMIDAS = 5;
	const POR_COMIDAS_PPALES = 0.275;
	const POR_COMIDAS_INTERMEDIA = 0.0875;
	
	const MPESO = 10;
	const MALTURA = 9;
	const MCINTURA = 8;
	const MCADMUN = 7;
	const MAGUA = 2;
	const MGRASAVICERAL = 6;
	const MMASAOSEA = 5;
	
	private $nombreComidas = ["Desayuno", "Mediasnueves", "Almuerzo", "Onces", "Cena"];
	
	public function calcularIMC($documento = null) {
		JFactory::getDocument()->setMimeEncoding( 'application/json' );
		JResponse::setHeader('Content-Disposition','attachment;filename="progress-report-results.json"');
		try {
			if (!$documento) {
				$documento = JFactory::getApplication()->input->getString("documento");
			}
			$m = $this->getModel();
			$persona = $m->buscarPersona($documento);
			if (!$persona) {
				throw new Exception(JText::_('PERSON_NOTFOUND'));
			}
			$data = new stdClass();
			$data->genero = $persona->genero;
			$data->peso = $m->getMedida($persona->uid, self::MPESO);
			$data->altura = $m->getMedida($persona->uid, self::MALTURA);
			$data->cintura = $m->getMedida($persona->uid, self::MCINTURA);
			$data->cadmun = $m->getMedida($persona->uid, self::MCADMUN);
			$data->afisica = $m->getAFisica($persona->afisica);
			$imc = $this->imc($data);
			$imc->dieta = $this->generarDieta($persona->uid, $imc);
			$imc->rutina = $m->getRutina($imc->grasa);
			echo json_encode($imc);
		} catch (Exception $ex) {
			echo '{"exception":"' . $ex->getMessage() . '"}';
		}
		JFactory::getApplication()->close();
	}
	
	private function imc($input) {
		$valores = null;
		switch ($input->genero) {
			case self::FEMENINO:
				$valores = $this->imcMujeres($input);
				break;
			case self::MASCULINO:
				$valores = $this->imcHombres($input);
				break;
		}
		return $valores;
	}
	
	private function imcMujeres ($in) {
		$peso = floatval($in->peso);
		$altura = floatval($in->altura);
		$cadera = $this->getModel()->getConstant(self::FEMENINO, self::CADERA, floatval($in->cadmun));
		$cintura = $this->getModel()->getConstant(self::FEMENINO, self::CINTURA, floatval($in->cintura));
		$talla = $this->getModel()->getConstant(self::FEMENINO, self::TALLA, $altura);
		$constantes = new stdClass();
		$constantes->grasa = round(($cadera + $cintura) - $talla, 0);
		$constantes->imc = round($peso / pow($altura / 100, 2), 0);
		$constantes->magro = round($peso - ($peso * $constantes->grasa / 100), 0);
		$constantes->proteina = ceil($constantes->magro * floatval($in->afisica));
		return $constantes;
	}
	
	private function imcHombres ($in) {
		$peso = floatval($in->peso);
		$altura = floatval($in->altura);
		$constantes = new stdClass();
		$constantes->grasa = round($this->getModel()->getConstant(self::MASCULINO, $peso, floatval($in->cintura) - floatval($in->cadmun)), 4);
		$constantes->imc = round($peso / pow($altura / 100, 2), 0);
		$constantes->magro = round($peso - ($peso * $constantes->grasa / 100), 4);
		$constantes->proteina = ceil($constantes->magro * floatval($in->afisica));
		return $constantes;
	}
	
	private function generarDieta($uid, $valores) {
		try {
			$proteinas = $this->getModel()->getAlimentos($uid, self::PROTREINAS, self::NUMERO_COMIDAS);
			$carbohidratos = $this->getModel()->getAlimentos($uid, self::CARBOHIDRATOS, self::NUMERO_COMIDAS);
			$grasas = $this->getModel()->getAlimentos($uid, self::GRASAS, self::NUMERO_COMIDAS);
			$dieta = [];
			for ($i = 0; $i < self::NUMERO_COMIDAS; $i ++) {
				$comida = new stdClass();
				$comida->nombre = $this->nombreComidas[$i];
				$k = ($valores->proteina * ($i % 2 == 0 ? self::POR_COMIDAS_PPALES : self::POR_COMIDAS_INTERMEDIA)) / self::GR_PROTEINAS;
				$comida->k = round($k, 1);
				$comida->proteina = $proteinas[$i];
				$comida->proteina->valor = round($comida->proteina->valor * $k, 0);
				$comida->carbohidratos = $carbohidratos[$i];
				$comida->carbohidratos->valor = round($comida->carbohidratos->valor * $k, 0);
				$comida->grasas = $grasas[$i];
				$comida->grasas->valor = round($comida->grasas->valor * $k, 0);
				$dieta[] = $comida;
			}
			return $dieta;
		} catch (Exception $ex) {
			return '{"exception":"' . $ex->getMessage() . '"}';
		}
	}
	
	public function buscar() {
		JFactory::getDocument()->setMimeEncoding( 'application/json' );
		JResponse::setHeader('Content-Disposition','attachment;filename="progress-report-results.json"');
		try {
			$in = JFactory::getApplication()->input;
			$persona = $this->getModel()->buscarPersona($in->getString("documento"));
			if (!$persona) {
				throw new Exception(JText::_('PERSON_NOTFOUND'));
			}
			$persona->fecha_nacimiento = date('d/m/Y', $persona->fecha_nacimiento);
			$user = JFactory::getUser($persona->uid);
			$persona->nombres = $user->name;
			$persona->email = $user->email;
			$antecedentes = $this->getModel()->buscarAntecedentes($user->id);
			$medidas = $this->getModel()->buscarMedidas($user->id);
			$res = new stdClass();
			$res->persona = $persona;
			$res->antecedentes = $antecedentes;
			$res->medidas = $medidas;
			echo json_encode($res);
		} catch (Exception $e) {
			echo "{\"exception\":\"" . $e->getMessage() . "\"}";
		}
		JFactory::getApplication()->close();
	}
	
	public function registrar() {
		JFactory::getDocument()->setMimeEncoding( 'application/json' );
		JResponse::setHeader('Content-Disposition','attachment;filename="progress-report-results.json"');
		try {
			$in = JFactory::getApplication()->input;
			$data = array();
			$data['name'] = $in->getString('nombre');
			$data['username'] = $in->getString('email');
			$data['email'] = $in->getString('email');
			$data['tipo_documento'] = $in->getString('tipo_documento');
			$data['documento'] = $in->getString('documento');
			$data['genero'] = $in->getString('genero');
			$data['nacimiento'] = $in->getString('nacimiento');
			$data['telefono'] = $in->getString('telefono');
			$data['profesion'] = $in->getString('profesion');
			$data['afisica'] = $in->getString('afisica');
			$persona = $this->getModel()->buscarPersona($data['documento']);
			$user = $persona ? JFactory::getUser($persona->uid) : $this->getModel()->registrarUsuarioJoomla($data);
			$data['uid'] = $user->id;
			$this->getModel()->registrarPersona($data, $persona);
			$ants = array();
			$i = 0;
			while ($in->get('antecedente-'.$i, null) != null) {
				if ($in->getInt('antecedente-'.$i) == 0) {
					$i ++;
					continue;
				}
				$ant = array();
				$ant['uid'] = $user->id;
				$ant['antecedente'] = $in->getInt('antecedente-'.$i);
				$ant['cuales'] = $in->get('cuales-'.$i, null) != null ? $in->get('cuales-'.$i) : '';
				$ants[] = $ant;
				$i ++;
			}
			$this->getModel()->registrarAntecedentes($ants);
			$meds = array();
			$i = 0;
			while ($in->get('medida-'.$i, null) != null) {
				$med = array();
				$med['uid'] = $user->id;
				$med['medida'] = $in->getString('medida-'.$i);
				$med['valor'] = $in->getFloat('valor-'.$i);
				$meds[] = $med;
				$i ++;
			}
			$this->getModel()->registrarMedidas($meds);
			echo "{\"status\":\"OK\"}";
		} catch (Exception $e) {
			echo "{\"exception\":\"" . $e->getMessage() . "\"}";
		}
		JFactory::getApplication()->close();
	}
	
	public function enviar() {
		JFactory::getDocument()->setMimeEncoding( 'application/json' );
		JResponse::setHeader('Content-Disposition','attachment;filename="progress-report-results.json"');
		try {
			$documento = JFactory::getApplication()->input->getString("documento");
			$m = $this->getModel();
			$persona = $m->buscarPersona($documento);
			$user = JFactory::getUser($persona->uid);
			$data = new stdClass();
			$data->genero = $persona->genero;
			$data->peso = $m->getMedida($persona->uid, self::MPESO);
			$data->altura = $m->getMedida($persona->uid, self::MALTURA);
			$data->cintura = $m->getMedida($persona->uid, self::MCINTURA);
			$data->cadmun = $m->getMedida($persona->uid, self::MCADMUN);
			$data->afisica = $m->getAFisica($persona->afisica);
			$data->agua = $m->getMedida($persona->uid, self::MAGUA);
			$data->cAgua = $this->evaluarAgua($persona->genero, $data->agua);
			$data->grasaViceral = $m->getMedida($persona->uid, self::MGRASAVICERAL);
			$data->cGrasaViceral = $this->evaluarGrasaViceral($data->grasaViceral);
			$data->masaOsea = $m->getMedida($persona->uid, self::MMASAOSEA);
			$data->cMasaOsea = $this->evaluarMasaOsea($persona->genero, $data->peso, $data->masaOsea);
			$edad = time() - $persona->fecha_nacimiento;
			$edad = floor(((($edad/60)/60)/24)/365);
			$data->cPeso = $this->evaluarPeso($persona->genero, $edad, $data->altura, $data->peso);
			$imc = $this->imc($data);
			$mail = '';
			$mail .= "<p>Se&ntilde;or(a) ".strtoupper($user->name)." ($edad)</p>";
			$mail .= "<p>Con el fin de contribuir a mejorar su estilo de vida, le estamos enviando los resultados de la valoraci&oacute;n realizada en la Escuela de Guerra por TOFITNET.</p>";
			$mail .= "<p>Es importante que tenga en cuenta que estas sugerencias le pueden ayudar a mejorar su condici&oacute;n actual, pero ninguna de ellas est&aacute; por encima de las recomendaciones y tratamiento m&eacute;dicos que tenga.  EN CASO DE DUDA POR FAVOR CONSULTE CON SU MEDICO Y ES MUY IMPORTANTE QUE NUNCA SE AUTOMEDIQUE.</p>";
			$mail .= "<ul>";
			$mail .= "<li>";
			$mail .= "<p>AGUA: El consumo de agua es b&aacute;sico para tener una buena salud, evitar enfermedades y mantener el peso ideal.</p>";
			$mail .= "<p>Su porcentaje de agua es: $data->agua ($data->cAgua)</p>";
			$mail .= "</li>";
			$mail .= "<li>";
			$mail .= "<p>GRASA VISCERAL: Es un tejido graso que envuelve &oacute;rganos vitales internos. Es un enemigo silencioso pues normalmente la persona  no sabe de su existencia hasta que se enferma.</p>";
			$mail .= "<p>Su grasa visceral esta en: $data->grasaViceral ($data->cGrasaViceral)</p>";
			$mail .= "</li>";
			$mail .= "<li>";
			$mail .= "<p>MASA OSEA: Tener huesos fuertes es importante para la salud. Una masa &oacute;sea baja, significa que se ha entrado en el camino de la Osteopenia.</p>";
			$mail .= "<p>Su masa &oacute;sea  se encuentra en: $data->masaOsea ($data->cMasaOsea)</p>";
			$mail .= "</li>";
			$mail .= "<li>";
			$mail .= "<p>PESO IDEAL: Es aquel que permite que el individuo pueda desarrollar normalmente todas sus funciones biol&oacute;gicas y voluntarias.</p>";
			$mail .= "<p>Su peso es de: $data->peso ($data->cPeso)</p>";
			$mail .= "</li>";
			$mail .= "</ul>";
			$mail .= "<p>A continuaci&oacute;n encontrara unas rutinas de ejercicio las cuales  est&aacute;n recomendadas para personas que tengan una condici&oacute;n f&iacute;sica  saludable, si usted ha tenido alguna herida en combate, lesi&oacute;n f&iacute;sica, cirug&iacute;a, u otra situaci&oacute;n que haya disminuido o desmejorado su condici&oacute;n f&iacute;sica, le sugerimos consultar con la Fisioterapeuta antes de comenzar cualquier rutina.</p>";
			$mail .= $m->getRutina($imc->grasa);
			$mail .= "<p><strong>TIPS ALIMENTICIOS.</strong></p>";
			$mail .= "<p>Estos son algunas recomendaciones y sugerencias que puede tener en cuenta para mejorar la alimentaci&oacute;n diaria suya y de su familia.</p>";
			$mail .= "<ol>";
			$mail .= "<li>Aumente las comidas diarias bajando las porciones en las comidas principales.</li>";
			$mail .= "<li>Nunca se salte ninguna comida, principalmente el desayuno.</li>";
			$mail .= "<li>Aumente el consumo de frutas y verduras.</li>";
			$mail .= "<li>Trate de comer una harina por comida y en la noche preferiblemente ingiera prote&iacute;na y no harinas.</li>";
			$mail .= "<li>Tome agua.</li>";
			$mail .= "<li>Evite en lo posible las salsas en las comidas r&aacute;pidas.</li>";
			$mail .= "<li>Disminuya el consumo de fritos y alimentos con contenido de grasa.</li>";
			$mail .= "<li>Trate de comer a horas.</li>";
			$mail .= "</ol>";
			$mailer = JFactory::getMailer();
			$config = JFactory::getConfig();
			$sender = array(
				$config->get('config.mailfrom'),
				$config->get('config.fromname')
			);
			//T@pTa?T7r[~P
			$mailer->setSender($sender);
			$mailer->addRecipient($user->email);
			$mailer->addCC('juliandrj@gmail.com');
			$mailer->setSubject('Estas gord@?, ya no más, con tofitnet!!!');
			$mailer->isHTML(true);
			$mailer->setBody($mail);
			$send = $mailer->Send();
			if ( $send !== true ) {
				throw new Exception('Mensaje no enviado'); 
			} else {
				echo "{\"status\":\"OK\"}";
			}
		} catch (Exception $e) {
			echo "{\"exception\":\"" . $e->getMessage() . "\"}";
		}
		JFactory::getApplication()->close();
	}
	
	private function evaluarAgua($genero, $agua) {
		$agua = round($agua, 0);
		if ($genero == 'M') {
			if ($agua < 50) {
				return 'BAJO';
			} else if ($agua >= 50 && $agua <= 65) {
				return 'NORMAL';
			} else {
				return 'ALTO';
			}
		} else {
			if ($agua < 45) {
				return 'BAJO';
			} else if ($agua >= 45 && $agua <= 60) {
				return 'NORMAL';
			} else {
				return 'ALTO';
			}
		}
	}
	
	private function evaluarGrasaViceral($gv) {
		$gv = round($gv, 0);
		if ($gv < 3) {
			return 'IDEAL';
		} else if ($gv >= 4 && $gv <= 6) {
			return 'INTERMEDIO';
		} else if ($gv >= 7 && $gv <= 8) {
			return 'RIESGO';
		} else {
			return 'ALTO RIESGO';
		}
	}
	
	private function evaluarMasaOsea($genero, $peso, $osea) {
		$peso = round($peso);
		if ($genero == 'M') {
			if ($peso < 65) {
				if ($osea < 2.66) {
					return 'BAJO';
				} else if ($osea == 2.66) {
					return 'NORMAL';
				} else {
					return 'BUENO';
				}
			} else if ($peso >= 65 && $peso <= 95) {
				if ($osea < 3.29) {
					return 'BAJO';
				} else if ($osea == 3.29) {
					return 'NORMAL';
				} else {
					return 'BUENO';
				}
			} else {
				if ($osea < 3.69) {
					return 'BAJO';
				} else if ($osea == 3.69) {
					return 'NORMAL';
				} else {
					return 'BUENO';
				}
			}
		} else {
			if ($peso < 45) {
				if ($osea < 1.95) {
					return 'BAJO';
				} else if ($osea == 1.95) {
					return 'NORMAL';
				} else {
					return 'BUENO';
				}
			} else if ($peso >= 45 && $peso <= 60) {
				if ($osea < 2.4) {
					return 'BAJO';
				} else if ($osea == 2.4) {
					return 'NORMAL';
				} else {
					return 'BUENO';
				}
			} else {
				if ($osea < 2.95) {
					return 'BAJO';
				} else if ($osea == 2.95) {
					return 'NORMAL';
				} else {
					return 'BUENO';
				}
			}
		}
	}

	private function evaluarPeso($genero, $edad, $altura, $peso) {
		$edad = round($edad, 0);
		if ($genero == 'M') {
			if ($edad < 30) {
				return $this->evaluarPesoAltura($altura, $peso, 2);
			} else if ($edad >= 30 && $edad <= 60) {
				return $this->evaluarPesoAltura($altura, $peso, 0);
			} else {
				return $this->evaluarPesoAltura($altura, $peso, -2);
			}
		} else {
			if ($edad < 45) {
				return $this->evaluarPesoAltura($altura, $peso, 2);
			} else if ($edad >= 45 && $edad <= 60) {
				return $this->evaluarPesoAltura($altura, $peso, 0);
			} else {
				return $this->evaluarPesoAltura($altura, $peso, -2);
			}
		}
	}
	
	private function evaluarPesoAltura($altura, $peso, $k) {
		$pesoIdeal = ($altura - 100) + $k;
		if ($peso < $pesoIdeal) {
			return 'BAJO';
		} else if ($peso == $pesoIdeal) {
			return 'IDEAL';
		} else {
			return 'ALTO';
		}
	}
	
}