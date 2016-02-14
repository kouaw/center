<?php

/* This file is part of Jeedom.
*
* Jeedom is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Jeedom is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
*/

/* * ***************************Includes**********************************/
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';

class center extends eqLogic {
	/***************************Attributs*******************************/
	
	public static function dependancy_info() {
		$return = array();
		$return['log'] = 'center_update';
		$return['progress_file'] = '/tmp/dependancy_center_in_progress';
		if (file_exists('/usr/sbin/i2cdetect')) {
			$return['state'] = 'ok';
		} else {
			$return['state'] = 'nok';
		}
		return $return;
	}

	public static function dependancy_install() {
		if (file_exists('/tmp/dependancy_center_in_progress')) {
			return;
		}
		log::remove('center_update');
		$cmd = 'sudo /bin/bash ' .dirname(__FILE__) . '/../../3rdparty/install.sh';
		$cmd .= ' >> ' . log::getPathToLog('center_update') . ' 2>&1 &';
		exec($cmd);
	}

	public function getGpioInfo() {
		$relais1 = shell_exec('cat /sys/class/gpio/gpio67/value');
		$relais2 = shell_exec('cat /sys/class/gpio/gpio72/value');
		$rouge = shell_exec('cat /sys/class/gpio/gpio70/value');
		$vert = shell_exec('cat /sys/class/gpio/gpio71/value');
		$bleu = shell_exec('cat /sys/class/gpio/gpio73/value');
		$color = 'Off';

		$relais1cmd = centerCmd::byEqLogicIdAndLogicalId($this->getId(),'relais1info');
		if (is_object($relais1cmd)) {
			if ($relais1cmd->execCmd() == null || $relais1cmd->execCmd() != $relais1) {
				$relais1cmd->event($relais1);
			}
		}

		$relais2cmd = centerCmd::byEqLogicIdAndLogicalId($this->getId(),'relais2info');
		if (is_object($relais2cmd)) {
			if ($relais2cmd->execCmd() == null || $relais2cmd->execCmd() != $relais2) {
				$relais2cmd->event($relais2);
			}
		}

		if ($bleu > 0 && $rouge > 0 && $vert > 0) {
			$color = 'Blanc';
		} elseif ($bleu == 0 && $rouge == 0 && $vert > 0) {
			$color = 'Vert';
		} elseif ($bleu == 0 && $rouge > 0 && $vert == 0) {
			$color = 'Rouge';
		} elseif ($bleu > 0 && $rouge == 0 && $vert == 0) {
			$color = 'Bleu';
		} elseif ($bleu > 0 && $rouge > 0 && $vert == 0) {
			$color = 'Rose';
		} elseif ($bleu > 0 && $rouge == 0 && $vert > 0) {
			$color = 'Cyan';
		} elseif ($bleu == 0 && $rouge > 0 && $vert > 0) {
			$color = 'Jaune';
		}

		$colorinfo = centerCmd::byEqLogicIdAndLogicalId($this->getId(),'colorinfo');
		if (is_object($colorinfo)) {
			if ($colorinfo->execCmd() == null || $colorinfo->execCmd() != $color) {
				$colorinfo->event($color);
			}
		}
		$mc = cache::byKey('centerWidgetmobile' . $this->getId());
		$mc->remove();
		$mc = cache::byKey('centerWidgetdashboard' . $this->getId());
		$mc->remove();
		$this->toHtml('mobile');
		$this->toHtml('dashboard');
		$this->refreshWidget();
		return;
	}

	public function preUpdate() {
	}

	public function postSave() {
		if (!$this->getId())
		return;

		$colorinfo = $this->getCmd(null, 'colorinfo');
		if (!is_object($colorinfo)) {
			$colorinfo = new centerCmd();
			$colorinfo->setLogicalId('colorinfo');
			$colorinfo->setIsVisible(1);
			$colorinfo->setName(__('Etat Couleur', __FILE__));
		}
        $colorinfo->setType('info');
		$colorinfo->setSubType('string');
		$colorinfo->setEventOnly(1);
		$colorinfo->setEqLogic_id($this->getId());
		$colorinfo->save();

		$relais1info = $this->getCmd(null, 'relais1info');
		if (!is_object($relais1info)) {
			$relais1info = new centerCmd();
			$relais1info->setLogicalId('relais1info');
			$relais1info->setIsVisible(1);
			$relais1info->setName(__('Etat relais 1', __FILE__));
		}
        $relais1info->setType('info');
		$relais1info->setSubType('binary');
		$relais1info->setEventOnly(1);
		$relais1info->setEqLogic_id($this->getId());
		$relais1info->save();

		$relais2info = $this->getCmd(null, 'relais2info');
		if (!is_object($relais2info)) {
			$relais2info = new centerCmd();
			$relais2info->setLogicalId('relais2info');
			$relais2info->setIsVisible(1);
			$relais2info->setName(__('Etat relais 2', __FILE__));
		}
        $relais2info->setType('info');
		$relais2info->setSubType('binary');
		$relais2info->setEventOnly(1);
		$relais2info->setEqLogic_id($this->getId());
		$relais2info->save();

		$redcolor = $this->getCmd(null, 'redcolor');
		if (!is_object($redcolor)) {
			$redcolor = new centerCmd();
			$redcolor->setLogicalId('redcolor');
			$redcolor->setIsVisible(1);
			$redcolor->setName(__('Rouge', __FILE__));
		}
		$redcolor->setType('action');
		$redcolor->setSubType('other');
		$redcolor->setEqLogic_id($this->getId());
		$redcolor->save();

		$greencolor = $this->getCmd(null, 'greencolor');
		if (!is_object($greencolor)) {
			$greencolor = new centerCmd();
			$greencolor->setLogicalId('greencolor');
			$greencolor->setIsVisible(1);
			$greencolor->setName(__('Vert', __FILE__));
		}
		$greencolor->setType('action');
		$greencolor->setSubType('other');
		$greencolor->setEqLogic_id($this->getId());
		$greencolor->save();

		$bluecolor = $this->getCmd(null, 'bluecolor');
		if (!is_object($bluecolor)) {
			$bluecolor = new centerCmd();
			$bluecolor->setLogicalId('bluecolor');
			$bluecolor->setIsVisible(1);
			$bluecolor->setName(__('Bleu', __FILE__));
		}
		$bluecolor->setType('action');
		$bluecolor->setSubType('other');
		$bluecolor->setEqLogic_id($this->getId());
		$bluecolor->save();

		$yellowcolor = $this->getCmd(null, 'yellowcolor');
		if (!is_object($yellowcolor)) {
			$yellowcolor = new centerCmd();
			$yellowcolor->setLogicalId('yellowcolor');
			$yellowcolor->setIsVisible(1);
			$yellowcolor->setName(__('Jaune', __FILE__));
		}
		$yellowcolor->setType('action');
		$yellowcolor->setSubType('other');
		$yellowcolor->setEqLogic_id($this->getId());
		$yellowcolor->save();

		$pinkcolor = $this->getCmd(null, 'pinkcolor');
		if (!is_object($pinkcolor)) {
			$pinkcolor = new centerCmd();
			$pinkcolor->setLogicalId('pinkcolor');
			$pinkcolor->setIsVisible(1);
			$pinkcolor->setName(__('Rose', __FILE__));
		}
		$pinkcolor->setType('action');
		$pinkcolor->setSubType('other');
		$pinkcolor->setEqLogic_id($this->getId());
		$pinkcolor->save();

		$whitecolor = $this->getCmd(null, 'whitecolor');
		if (!is_object($whitecolor)) {
			$whitecolor = new centerCmd();
			$whitecolor->setLogicalId('whitecolor');
			$whitecolor->setIsVisible(1);
			$whitecolor->setName(__('Blanc', __FILE__));
		}
		$whitecolor->setType('action');
		$whitecolor->setSubType('other');
		$whitecolor->setEqLogic_id($this->getId());
		$whitecolor->save();
		
		$cyancolor = $this->getCmd(null, 'cyancolor');
		if (!is_object($cyancolor)) {
			$cyancolor = new centerCmd();
			$cyancolor->setLogicalId('cyancolor');
			$cyancolor->setIsVisible(1);
			$cyancolor->setName(__('Cyan', __FILE__));
		}
		$cyancolor->setType('action');
		$cyancolor->setSubType('other');
		$cyancolor->setEqLogic_id($this->getId());
		$cyancolor->save();

		$offcolor = $this->getCmd(null, 'offcolor');
		if (!is_object($offcolor)) {
			$offcolor = new centerCmd();
			$offcolor->setLogicalId('offcolor');
			$offcolor->setIsVisible(1);
			$offcolor->setName(__('Off', __FILE__));
		}
		$offcolor->setType('action');
		$offcolor->setSubType('other');
		$offcolor->setEqLogic_id($this->getId());
		$offcolor->save();

		$offrelais1 = $this->getCmd(null, 'offrelais1');
		if (!is_object($offrelais1)) {
			$offrelais1 = new centerCmd();
			$offrelais1->setLogicalId('offrelais1');
			$offrelais1->setIsVisible(1);
			$offrelais1->setName(__('Off relais 1', __FILE__));
		}
		$offrelais1->setType('action');
		$offrelais1->setSubType('other');
		$offrelais1->setEqLogic_id($this->getId());
		$offrelais1->save();

		$onrelais1 = $this->getCmd(null, 'onrelais1');
		if (!is_object($onrelais1)) {
			$onrelais1 = new centerCmd();
			$onrelais1->setLogicalId('onrelais1');
			$onrelais1->setIsVisible(1);
			$onrelais1->setName(__('On relais 1', __FILE__));
		}
		$onrelais1->setType('action');
		$onrelais1->setSubType('other');
		$onrelais1->setEqLogic_id($this->getId());
		$onrelais1->save();

		$offrelais2 = $this->getCmd(null, 'offrelais2');
		if (!is_object($offrelais2)) {
			$offrelais2 = new centerCmd();
			$offrelais2->setLogicalId('offrelais2');
			$offrelais2->setIsVisible(1);
			$offrelais2->setName(__('Off relais 2', __FILE__));
		}
		$offrelais2->setType('action');
		$offrelais2->setSubType('other');
		$offrelais2->setEqLogic_id($this->getId());
		$offrelais2->save();

		$onrelais2 = $this->getCmd(null, 'onrelais2');
		if (!is_object($onrelais2)) {
			$onrelais2 = new centerCmd();
			$onrelais2->setLogicalId('onrelais2');
			$onrelais2->setIsVisible(1);
			$onrelais2->setName(__('On relais 2', __FILE__));
		}
		$onrelais2->setType('action');
		$onrelais2->setSubType('other');
		$onrelais2->setEqLogic_id($this->getId());
		$onrelais2->save();
	}

	public function postAjax() {
		$this->getGpioInfo();
	}
	
	public function toHtml($_version = 'dashboard') {
		if ($this->getIsEnable() != 1) {
			return '';
		}
		if (!$this->hasRight('r')) {
			return '';
		}
		$version = jeedom::versionAlias($_version);
		if ($this->getDisplay('hideOn' . $version) == 1) {
			return '';
		}
		$mc = cache::byKey('centerWidget' . jeedom::versionAlias($_version) . $this->getId());
		if ($mc->getValue() != '') {
			return preg_replace("/" . preg_quote(self::UIDDELIMITER) . "(.*?)" . preg_quote(self::UIDDELIMITER) . "/", self::UIDDELIMITER . mt_rand() . self::UIDDELIMITER, $mc->getValue());
		}
		$replace = array(
			'#name#' => $this->getName(),
			'#id#' => $this->getId(),
			'#background_color#' => $this->getBackgroundColor(jeedom::versionAlias($_version)),
			'#eqLink#' => $this->getLinkToConfiguration(),
			'#uid#' => 'center' . $this->getId() . self::UIDDELIMITER . mt_rand() . self::UIDDELIMITER,
		);
		foreach ($this->getCmd() as $cmd) {
			if ($cmd->getType() == 'info') {
				$replace['#' . $cmd->getLogicalId() . '_history#'] = '';
				$replace['#' . $cmd->getLogicalId() . '#'] = $cmd->execCmd();
				$replace['#' . $cmd->getLogicalId() . '_id#'] = $cmd->getId();
				$replace['#' . $cmd->getLogicalId() . '_collectDate#'] = $cmd->getCollectDate();
				if ($cmd->getIsHistorized() == 1) {
					$replace['#' . $cmd->getLogicalId() . '_history#'] = 'history cursor';
				}
			} else {
				$replace['#' . $cmd->getLogicalId() . '_id#'] = $cmd->getId();
			}
		}
		
		$colorcmd = $this->getCmd(null, 'colorinfo');
		if (is_object($colorcmd)) {
			$colorinfo = $colorcmd->execCmd();
			$colorcode = '#000000';
			switch ($colorinfo) {
				case 'Blanc':
					$colorcode='#FFFFFF';
				break;
				case 'Bleu':
					$colorcode='#0000FF'; break;
				case 'Rouge':
					$colorcode='#FF0000'; break;
				case 'Vert':
					$colorcode='#008000'; break;
				case 'Jaune':
					$colorcode='#FFFF00'; break;
				case 'Rose':
					$colorcode='#FFC0CB'; break;
				case 'Cyan':
					$colorcode='#00FFFF'; break;
			}
			$replace['#colorcode#'] = $colorcode;
		}
		
		if (($_version == 'dview' || $_version == 'mview') && $this->getDisplay('doNotShowNameOnView') == 1) {
			$replace['#name#'] = '';
			$replace['#object_name#'] = (is_object($object)) ? $object->getName() : '';
		}
		if (($_version == 'mobile' || $_version == 'dashboard') && $this->getDisplay('doNotShowNameOnDashboard') == 1) {
			$replace['#name#'] = '<br/>';
			$replace['#object_name#'] = (is_object($object)) ? $object->getName() : '';
		}
		$parameters = $this->getDisplay('parameters');
		if (is_array($parameters)) {
			foreach ($parameters as $key => $value) {
				$replace['#' . $key . '#'] = $value;
			}
		}
		$html = template_replace($replace, getTemplate('core', $version, 'center' , 'center'));
		cache::set('centerWidget' . $version . $this->getId(), $html, 0);
		return $html;
	}
}

class centerCmd extends cmd {
	/***************************Attributs*******************************/


	/*************************Methode static****************************/


	/***********************Methode d'instance**************************/

	public function execute($_options = null) {
		if ($this->getType() == '') {
			return '';
		}
		$script_path = realpath(dirname(__FILE__) . '/../../3rdparty/');
		$action= $this->getLogicalId();
		$eqLogic = $this->getEqlogic();
		if (strpos($action, 'color') !== false) {
			$cmd = 'sudo /bin/bash ' . $script_path . '/initcolor.sh ' . $action;
		log::add('center','debug',$cmd);
		exec($cmd);
		$eqLogic->getGpioInfo();
		} elseif (strpos($action, 'relais') !== false) {
			$cmd = 'sudo /bin/bash ' . $script_path . '/initrelais.sh ' . $action;
		log::add('center','debug',$cmd);
		exec($cmd);
		$eqLogic->getGpioInfo();
		}
	}

	/************************Getteur Setteur****************************/
}
?>