<?php

namespace BirdWorX;

defined('PROJECT_PATH') || die('Please define PROJECT_PATH before using ' . __FILE__);

class Env {
	public const PROJECT_PATH = PROJECT_PATH;

	private static string $baseUrl;

	private static string $cachePath;
	private static string $publicPath;
	private static string $varPath;

	private static bool $isCmdLineCall;

	private static bool $isInitialized = false;

	public static function getBaseUrl(): string {
		return self::$baseUrl;
	}

	public static function getCachePath(): string {
		return self::$cachePath;
	}

	public static function getPublicPath(): string {
		return self::$publicPath;
	}

	public static function getVarPath(): string {
		return self::$varPath;
	}

	public static function isCmdLineCall(): bool {
		return self::$isCmdLineCall;
	}

	public static function isDevSystem(): bool {
		/** @noinspection PhpUndefinedConstantInspection */
		return SYSTEM_KEY === 'dev';
	}

	public static function isProdSystem(): bool {
		/** @noinspection PhpUndefinedConstantInspection */
		return SYSTEM_KEY === 'prod';
	}

	/**
	 * Liest .env - Dateien aus und initialisiert entsprechende define-Variable
	 */
	private static function setDefinesByEnvFiles(): void {
		$env = array();

		foreach (array(PROJECT_PATH . '.env.dist', PROJECT_PATH . '.env') as $env_file) {
			if (file_exists($env_file)) {
				$env_lines = file($env_file);

				if ($env_lines !== false) {
					foreach ($env_lines as $env_line) {
						$env_line = trim(preg_replace('/#.*/', '', $env_line));
						if ($env_line !== '') {
							list($var, $val) = explode('=', $env_line, 2);
							$env[$var] = $val;
						}
					}
				}
			}
		}

		foreach ($env as $var => $val) {
			define($var, $val);
		}
	}

	public static function init() {

		if (self::$isInitialized) {
			return;
		}

		self::setDefinesByEnvFiles();

		defined('BASE_URL') || die('Please define BASE_URL before using ' . __FILE__);
		defined('SYSTEM_KEY') || die('Please define SYSTEM_KEY ("dev|test|prod") before using ' . __FILE__);

		self::$baseUrl = BASE_URL;

		defined('PUBLIC_PATH') || define('PUBLIC_PATH', self::PROJECT_PATH . 'public/');
		self::$publicPath = PUBLIC_PATH;

		defined('VAR_PATH') || define('VAR_PATH', self::PROJECT_PATH . 'var/');
		self::$varPath = VAR_PATH;

		defined('CACHE_PATH') || define('CACHE_PATH', self::$varPath . 'cache/');
		self::$cachePath = CACHE_PATH;

		self::$isCmdLineCall = !array_key_exists('REQUEST_METHOD', $_SERVER);
/*
		setlocale(LC_ALL, 'de_DE.utf8', 'de_DE@euro', 'de_DE', 'de', 'ge', 'German');
		setlocale(LC_NUMERIC, 'en_US', 'C'); // WICHTIG: Dieses Setting legt fest, wie float-Zahlen in Strings umgewandelt werden: https://stackoverflow.com/questions/17587581/php-locale-dependent-float-to-string-cast
*/
		self::$isInitialized = true;
	}
}

Env::init();