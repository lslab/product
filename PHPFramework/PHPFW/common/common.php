<?php


/**
 * Returns an array of all the given parameters.
 *
 * Example:
 *
 * `a('a', 'b')`
 *
 * Would return:
 *
 * `array('a', 'b')`
 *
 * @return array Array of given parameters
 * @link http://book.cakephp.org/view/1122/a
 * @deprecated Will be removed in 2.0
 */
function a() {
	$args = func_get_args();
	return $args;
}

/**
 * Constructs associative array from pairs of arguments.
 *
 * Example:
 *
 * `aa('a','b')`
 *
 * Would return:
 *
 * `array('a'=>'b')`
 *
 * @return array Associative array
 * @link http://book.cakephp.org/view/1123/aa
 * @deprecated Will be removed in 2.0
 */
function aa() {
	$args = func_get_args();
	$argc = count($args);
	for ($i = 0; $i < $argc; $i++) {
		if ($i + 1 < $argc) {
			$a[$args[$i]] = $args[$i + 1];
		} else {
			$a[$args[$i]] = null;
		}
		$i++;
	}
	return $a;
}


/**
 * Convenience method for strtolower().
 *
 * @param string $str String to lowercase
 * @return string Lowercased string
 * @link http://book.cakephp.org/view/1134/low
 * @deprecated Will be removed in 2.0
 */
function low($str) {
	return strtolower($str);
}

/**
 * Convenience method for strtoupper().
 *
 * @param string $str String to uppercase
 * @return string Uppercased string
 * @link http://book.cakephp.org/view/1139/up
 * @deprecated Will be removed in 2.0
 */
function up($str) {
	return strtoupper($str);
}

/**
 * Convenience method for str_replace().
 *
 * @param string $search String to be replaced
 * @param string $replace String to insert
 * @param string $subject String to search
 * @return string Replaced string
 * @link http://book.cakephp.org/view/1137/r
 * @deprecated Will be removed in 2.0
 */
function r($search, $replace, $subject) {
	return str_replace($search, $replace, $subject);
}

/**
 * Merge a group of arrays
 *
 * @param array First array
 * @param array Second array
 * @param array Third array
 * @param array Etc...
 * @return array All array parameters merged into one
 * @link http://book.cakephp.org/view/1124/am
 */
function am() {
	$r = array();
	$args = func_get_args();
	foreach ($args as $a) {
		if (!is_array($a)) {
			$a = array($a);
		}
		$r = array_merge($r, $a);
	}
	return $r;
}

/**
 * Gets an environment variable from available sources, and provides emulation
 * for unsupported or inconsistent environment variables (i.e. DOCUMENT_ROOT on
 * IIS, or SCRIPT_NAME in CGI mode).  Also exposes some additional custom
 * environment information.
 *
 * @param  string $key Environment variable name.
 * @return string Environment variable setting.
 * @link http://book.cakephp.org/view/1130/env
 */
function env($key) {
	if ($key == 'HTTPS') {
		if (isset($_SERVER['HTTPS'])) {
			return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
		}
		return (strpos(env('SCRIPT_URI'), 'https://') === 0);
	}

	if ($key == 'SCRIPT_NAME') {
		if (env('CGI_MODE') && isset($_ENV['SCRIPT_URL'])) {
			$key = 'SCRIPT_URL';
		}
	}

	$val = null;
	if (isset($_SERVER[$key])) {
		$val = $_SERVER[$key];
	} elseif (isset($_ENV[$key])) {
		$val = $_ENV[$key];
	} elseif (getenv($key) !== false) {
		$val = getenv($key);
	}

	if ($key === 'REMOTE_ADDR' && $val === env('SERVER_ADDR')) {
		$addr = env('HTTP_PC_REMOTE_ADDR');
		if ($addr !== null) {
			$val = $addr;
		}
	}

	if ($val !== null) {
		return $val;
	}

	switch ($key) {
		case 'SCRIPT_FILENAME':
			if (defined('SERVER_IIS') && SERVER_IIS === true) {
				return str_replace('\\\\', '\\', env('PATH_TRANSLATED'));
			}
			break;
		case 'DOCUMENT_ROOT':
			$name = env('SCRIPT_NAME');
			$filename = env('SCRIPT_FILENAME');
			$offset = 0;
			if (!strpos($name, '.php')) {
				$offset = 4;
			}
			return substr($filename, 0, strlen($filename) - (strlen($name) + $offset));
			break;
		case 'PHP_SELF':
			return str_replace(env('DOCUMENT_ROOT'), '', env('SCRIPT_FILENAME'));
			break;
		case 'CGI_MODE':
			return (PHP_SAPI === 'cgi');
			break;
		case 'HTTP_BASE':
			$host = env('HTTP_HOST');
			if (substr_count($host, '.') !== 1) {
				return preg_replace('/^([^.])*/i', null, env('HTTP_HOST'));
			}
			return '.' . $host;
			break;
	}
	return null;
}


/**
 * Convenience method for htmlspecialchars.
 *
 * @param string $text Text to wrap through htmlspecialchars
 * @param string $charset Character set to use when escaping.  Defaults to config value in 'App.encoding' or 'UTF-8'
 * @return string Wrapped text
 * @link http://book.cakephp.org/view/1132/h
 */
function h($text, $charset = null) {
	if (is_array($text)) {
		return array_map('h', $text);
	}

	static $defaultCharset = false;
	if ($defaultCharset === false) {
		$defaultCharset = App::config('page_charset');
		if ($defaultCharset === null) {
			$defaultCharset = 'UTF-8';
		}
	}
	if ($charset) {
		return htmlspecialchars($text, ENT_QUOTES, $charset);
	} else {
		return htmlspecialchars($text, ENT_QUOTES, $defaultCharset);
	}
}

/**
 * 添加到文件
 *
 * @param string $file
 * @param string $content
 * @return boolean
 */
function append($file,$content) {
	if ($fp=@fopen($file,'a+')) {
		fwrite($fp,$content);
		fclose($fp);
		return true;
	} else {
		return false;
	}
}

/**
 * 保存文件
 *
 * @param string $file
 * @param string $content
 * @return boolean
 */
function save($file,$content) {
	return file_put_contents($file,$content);
}

/**
 * 读取文件内容
 *
 * @param string $file
 * @return string
 */
function read($file) {
	return file_get_contents($file);
}
?>