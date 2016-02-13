<?php

/*
	Copyright (c) 2011-2014 Lucinda Brown <info@trillamar.com>
	Copyright (c) 2011-2014 Jinsoo Kang <info@trillamar.com>

	Secure HTML5 Video Player is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/*
To detect client browser:

	SH5VP_BrowserDetect::detect()->isIE(); //returns TRUE if Internet Explorer

	$bd = SH5VP_BrowserDetect::detect();
	$bd->isChrome(); //returns TRUE if Google Chrome

There is no penality to constructing the class repeatedly with each call because it is a singleton.
*/

final class SH5VP_BrowserDetect {

	const AGENT_BOUNDARY_CHARS = " \t/;:()";
	const DIGITS = '0123456789';
	private $__agent;
	private $__isMac;
	private $__isLinux;
	private $__isWindows;
	private $__isIPhone;
	private $__isIPad;
	private $__isAndroid;
	private $__isSilk;
	private $__isWinPhone;
	private $__isBlackberry;
	private $__isMeego;
	private $__isChrome;
	private $__isSafari;
	private $__isFirefox;
	private $__versionFirefox;
	private $__isOpera;
	private $__isIE;
	private $__versionIE;
	private $__isMobileBrowser;

	private $__tokens;
	
	public static function detect($_agent = '', $_force = FALSE) {
		static $_instance = NULL;
		if (! $_agent) $_agent = $_SERVER['HTTP_USER_AGENT'];
		if ($_instance === NULL || $_force || $_instance->agent() != $_agent) {
			$_instance = new SH5VP_BrowserDetect($_agent);
		}
		return $_instance;
	}

	private function __construct($_agent) {
		$this->__agent = $_agent;
		$this->__tokens = array();
		{
			$_last_token = '';
			$_curr_token = '';
			$tokenIndex = 0;
			$agent_len = strlen($this->__agent);
			for ($i = 0; $i < $agent_len; $i++) {
				$curr_c = $this->__agent[$i];
				$isLastChar = ($i+1 >= $agent_len);
				if (strpos(SH5VP_BrowserDetect::AGENT_BOUNDARY_CHARS, $curr_c) !== FALSE || $isLastChar) {
					if ($isLastChar) {
						$_curr_token .= $curr_c;
					}
					if ($_curr_token != '') {
						$tokenIndex += 1;
						if (strpos(SH5VP_BrowserDetect::DIGITS, $_curr_token[0]) !== FALSE && $this->__tokens[$_last_token] && ( $_last_token == 'msie' || $_last_token == 'trident' || $_last_token == 'firefox')) {
							$this->__tokens[$_last_token]['v'] = $_curr_token;
						}
						else {
							if (isset($this->__tokens[$_curr_token])) {
								$this->__tokens[$_curr_token]['t'][] = $tokenIndex;
							}
							else {
								$this->__tokens[$_curr_token] = array('t' => array($tokenIndex));
							}
						}
						$_last_token = $_curr_token;
						$_curr_token = '';
					}
				}
				else {
					$_curr_token .= strtolower($curr_c);
				}
			}
		}

		$this->__isMac = isset( $this->__tokens['mac'] );
		$this->__isLinux = isset( $this->__tokens['linux'] );
		$this->__isWindows = isset( $this->__tokens['windows'] );
		$this->__isIPhone = isset( $this->__tokens['iphone'] );
		$this->__isIPad = isset( $this->__tokens['ipad'] );
		$this->__isSilk = isset( $this->__tokens['silk'] );
		$this->__isAndroid = isset( $this->__tokens['android'] );

		$this->__isWinPhone = $this->tokensAreInSequence(array('windows', 'phone'));
		$this->__isBlackberry = (
			isset( $this->__tokens['blackberry'] )
			|| isset( $this->__tokens['bb10'] )
			|| $this->tokensAreInSequence( array('bb', 'version') )
			|| $this->tokensAreInSequence( array('rim', 'tablet', 'os') )
		);
		$this->__isMeego = isset( $this->__tokens['meego'] );
		$this->__isChrome = isset( $this->__tokens['chrome'] ) || isset( $this->__tokens['crios'] );
		$this->__isSafari = !$this->__isChrome && isset( $this->__tokens['safari'] );
		$this->__isFirefox = isset( $this->__tokens['firefox'] );
		if ($this->__isFirefox) {
			$this->__versionFirefox = floor( floatval($this->__tokens['firefox']['v']) );
		}		
		$this->__isOpera = isset( $this->__tokens['opera'] );

		$is_trident = isset( $this->__tokens['trident'] );
		$this->__isIE = isset( $this->__tokens['msie'] ) || $is_trident;
		$this->__versionIE = -1;
		if ($this->__isIE) {
			if ($is_trident) {
				$this->__versionIE = 4 + floor( floatval($this->__tokens['trident']['v']) );
			}
			else {
				$this->__versionIE = floor( floatval($this->__tokens['msie']['v']) );
			}
		}
		$this->__isMobileBrowser = $this->__isIPhone || $this->__isIPad || $this->__isAndroid || $this->__isSilk || $this->__isWinPhone || $this->__isBlackberry || $this->__isMeego;

		if ($this->__isMobileBrowser ) {
			$this->__isMac = $this->__isLinux = $this->__isWindows = FALSE;
			if (!$this->__isIPhone && !$this->__isIPad) {
				$this->__isSafari = FALSE;
			}
		}
	}

	private function tokensAreInSequence($ary) {
		$last_t = NULL;
		foreach ($ary as $curr => $val) {
			$curr_token = $this->__tokens[ $val ];
			if ($curr_token == NULL) return FALSE;
			$curr_t = $curr_token['t'];
			if ($last_t != NULL) {
				$found = FALSE;
				foreach ($curr_t as $curr_t_pos => $curr_t_val) {
					foreach ($last_t as $last_t_pos => $last_t_val) {
						if ($last_t_val + 1 == $curr_t_val) {
							$found = TRUE;
							break;
						}
					}
					if ($found) break;
				}
				if (!$found) return FALSE;
			}
			$last_t = $curr_t;
		}
		return TRUE;
	}


	public function agent() {
		return $this->__agent;
	}

	public function isMac() {
		return $this->__isMac;
	}

	public function isLinux() {
		return $this->__isLinux;
	}

	public function isWindows() {
		return $this->__isWindows;
	}

	public function isIPhone() {
		return $this->__isIPhone;
	}

	public function isIPad() {
		return $this->__isIPad;
	}

	public function isAndroid() {
		return $this->__isAndroid;
	}

	public function isSilk() {
		return $this->__isSilk;
	}

	public function isWinPhone() {
		return $this->__isWinPhone;
	}

	public function isBlackberry() {
		return $this->__isBlackberry;
	}

	public function isMeego() {
		return $this->__isMeego;
	}

	public function isChrome() {
		return $this->__isChrome;
	}

	public function isSafari() {
		return $this->__isSafari;
	}

	public function isFirefox() {
		return $this->__isFirefox;
	}

	public function versionFirefox() {
		return $this->__versionFirefox;
	}

	public function isOpera() {
		return $this->__isOpera;
	}

	public function versionIE() {
		return $this->__versionIE;
	}

	public function isIE($vers = -1) {
		if (!$this->__isIE || $vers == -1) {
			return $this->__isIE;
		}
		return floor(abs(floor(floatval($vers)) - $this->__versionIE)) == 0;
	}

	public function isMobileBrowser() {
		return $this->__isMobileBrowser;
	}

}

?>
