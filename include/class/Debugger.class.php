<?php
class Debugger {
	const ON = true;
	const OFF = false;

	private $switch;
	private $trace = array();
	private $autoflush;

	public $caca;

	// TODO: Implement debug level

	public function error(Exception $e, $autoflush = false) {
		if (!$this->is_on()) {
			return;
		}

		$trace = $e->getTrace();
		if ($autoflush || $this->autoflush) {
			Debugger::println('[ERROR] '.$e->getMessage());
			foreach ($trace as $el) {
				Debugger::println(tab(4).$el['file'].' at line '.
						$el['line'].': '.$el['function'].'('.
						Debugger::a2s($el['args'], true).')');
			}
			Debugger::println('');
		} else {
			$this->trace[] = '[ERROR] '.$e->getMessage();
			foreach ($trace as $el) {
				$args = Debugger::a2s($el['args'], true);
				$args = preg_replace('#^Array \(#', '', $args);
				$args = preg_replace('#'.PHP_EOL.'(&emsp;)*\)$#', '', $args);
				$this->trace[] = tab(4).$el['file'].' at line '.
						$el['line'].': '.$el['function'].'('.
						$args.')';
			}
			$this->trace[] = '';
		}
	}

	public function debug($txt, $autoflush = false, $e = null) {
		if (!$this->is_on()) {
			return;
		}

		if (is_array($txt)) {
			$txt = Debugger::a2s($txt);
		} else if (is_object($txt)) {
			if (method_exists($txt, '__toString')) {
				$txt = (string) $txt.PHP_EOL;
			} else {
				$txt = Debugger::o2s($txt).PHP_EOL;
			}
		}

		if ($e == null) {
			$e = new Exception("");
		}
		$traces = $e->getTrace();
		$trace = $traces[0];
		if ($autoflush || $this->autoflush) {
			Debugger::println('[DEBUG] '.$trace['file'].':'.$trace['line']);
			Debugger::println(tab(4).$txt."<br/>");
		} else {
			$this->trace[] = '[DEBUG] '.$trace['file'].':'.$trace['line'];
			$lines = explode('<br />', nl2br($txt));
			foreach ($lines as $line) {
				$line = trim($line);
				$this->trace[] = tab(4).$line;
			}
			$this->trace[] = '';
		}
	}

	public function flush() {
		if (!$this->is_on()) {
			return null;
		}
		$str = join('<br />'.PHP_EOL, $this->trace);
		$this->trace = array();
		return $str;
	}
	
	public function set_autoflush($b) {
		if (!is_bool($b)) {
			throw new Exception('set_autoflush expect Argument 1 to be bool');
		}
		$this->autoflush = $b;
	}

	public function is_on() {
		return $this->switch;
	}

	public function on() {
		$this->switch = Debugger::ON;
	}

	public function off() {
		$this->switch = Debugger::OFF;
	}

// PRIVATE STATIC FUNCTIONS

	private static function println($txt) {
		echo $txt.'<br />'.PHP_EOL;
	}

	private static function a2s($txt, $only_values = false, $tab = 4) {
		$array = array();

		$i = 0;
		foreach ($txt as $key => $value) {
			if (is_array($value)) {
				$value = Debugger::a2s($value, $only_values, $tab);
			} else if (is_object($value)) {
				if (method_exists($txt, '__toString')) {
					$value = (string) $txt;
				} else {
					$value = Debugger::o2s($value, $tab);
				}
			}

			if ($only_values) {
				$array[] = Debugger::to_string($value, $tab);
			} else {
				$debut = '';
				if ($i == 0) {
					$debut = PHP_EOL.Debugger::tab($tab);
				}
				$array[] = $debut.'['.$key.'] => '.$value;
			}
			$i++;
		}
		$fin = '';
		if (count($array) > 0) {
			$fin = PHP_EOL.Debugger::tab($tab - 1);
		}

		$str = '';
		if ($only_values) {
			$str = join(', ', $array);
		} else {
			$str = join(PHP_EOL.Debugger::tab($tab), $array);
		}
		return 'Array ('.$str.$fin.')';
	}

	private static function o2s($o, $tab = 0) {
		$reflect = new ReflectionClass($o);
		$tab++;

		$array = array();
		//$array[] = 'Object {';
		$public_vars = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
		$vars = Debugger::get_values($o, $public_vars, $tab + 1);
		if (count($vars) > 0) {
			$val = preg_replace('#^Array \('.PHP_EOL.'#', '', Debugger::a2s($vars, false, $tab));
			$val = preg_replace('#'.PHP_EOL.'(&emsp;)*\)$#', '', $val);
			$str = '[PUBLIC]'.PHP_EOL.tab($tab);
			$str .= $val;
			$array[] = $str;
		}

		$private_vars = $reflect->getProperties(ReflectionProperty::IS_PRIVATE);
		$vars = Debugger::get_values($o, $private_vars, $tab + 1);
		if (count($vars) > 0) {
			$val = preg_replace('#^Array \('.PHP_EOL.'#', '', Debugger::a2s($vars, false, $tab));
			$val = preg_replace('#'.PHP_EOL.'(&emsp;)*\)$#', '', $val);
			$str = '[PRIVATE]'.PHP_EOL.tab($tab);
			$str .= $val;
			$array[] = $str;
		}

		$protected_vars = $reflect->getProperties(ReflectionProperty::IS_PROTECTED);
		$vars = Debugger::get_values($o, $protected_vars, $tab + 1);
		if (count($vars) > 0) {
			$val = preg_replace('#^Array \('.PHP_EOL.'#', '', Debugger::a2s($vars, false, $tab));
			$val = preg_replace('#'.PHP_EOL.'(&emsp;)*\)$#', '', $val);
			$str = '[PROTECTED]'.PHP_EOL.tab($tab);
			$str .= $val;
			$array[] = $str;
		}

		$result = join(PHP_EOL.tab($tab - 1), $array);
		$result = str_replace('<br />', '', $result);

		return $result;
	}

	private static function get_values($o, $vars, $tab) {
		$result = array();
		foreach ($vars as $var) {
			if ($var->isStatic()) {
				continue;
			}
			$var->setAccessible(true);
			$result[$var->getName()] = Debugger::to_string($var->getValue($o), $tab);
		}
		return $result;
	}

	private static function to_string($var, $tab = 0) {
		$result = null;
		if (is_array($var)) {
			$result = Debugger::a2s($var, false, $tab);
		} else if (is_object($var)) {
			if (method_exists($var, '__toString')) {
				$classname = 'Object '.get_class($var).': '.(string) $var;
			} else {
				$result = Debugger::o2s($var, $tab);
			}
		} else if (is_bool($var)) {
			$result = ($var)? 'B_TRUE' : 'B_FALSE';
		} else if (is_null($var)) {
			$result = 'NULL_VALUE';
		} else {
			$result = strval($var);
		}
		return $result;
	}

	private static function tab($qty = 1) {
		if (!is_int($qty)) {
			throw new Exception('tab expect Argument 1 to be integer');
		}

		$result = '';
		for ($i = 0; $i < $qty; $i++) {
			$result .= '&emsp;';
		}
		return $result;
	}
}
?>