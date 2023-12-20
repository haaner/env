<?php

namespace BirdWorX;

use OptX\Basic\Utils;

defined('PROJECT_PATH') || die('Please define PROJECT_PATH before using ' . __FILE__);

abstract class Env {
	public const PROJECT_PATH = PROJECT_PATH;

	private static string $baseUrl;

	private static string $cachePath;
	private static string $publicPath;
	private static string $varPath;

	private static bool $isCmdLineCall;

	private static bool $isInitialized = false;

	public static function getBaseUrl($add_protocol = false): string {
		$base_url = self::$baseUrl;

		if ($add_protocol) {
			if (!str_starts_with($base_url, 'http')) {
				$protocol = empty($_SERVER['HTTPS']) ? 'http' : 'https';
				$base_url = $protocol . ':' . $base_url;
			}
		}

		return $base_url;
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

	public static function isTestSystem(): bool {
		/** @noinspection PhpUndefinedConstantInspection */
		return SYSTEM_KEY === 'test';
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

		foreach (array(PROJECT_PATH . '.env.dist', PROJECT_PATH . '.env', PROJECT_PATH . '.env.testing') as $env_file) {
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

		foreach ($_ENV as $key => $val) {
			$env[$key] = $val;
		}

		foreach ($env as $var => $val) {
			define($var, $val);
		}
	}

	public static function createCachePath($fresh = false) {

		if ($fresh) {
			Utils::runlink(self::$cachePath);
		}

		if (!file_exists(self::$cachePath)) {
			mkdir(self::$cachePath, 0775, true);
		}

		@chmod(self::$cachePath, 0775);
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

		@chmod(self::$varPath, 0775);

		defined('CACHE_PATH') || define('CACHE_PATH', self::$varPath . 'cache/');
		self::$cachePath = CACHE_PATH;

		self::createCachePath();

		self::$isCmdLineCall = !array_key_exists('REQUEST_METHOD', $_SERVER);
/*
		setlocale(LC_ALL, 'de_DE.utf8', 'de_DE@euro', 'de_DE', 'de', 'ge', 'German');
		setlocale(LC_NUMERIC, 'en_US', 'C'); // WICHTIG: Dieses Setting legt fest, wie float-Zahlen in Strings umgewandelt werden: https://stackoverflow.com/questions/17587581/php-locale-dependent-float-to-string-cast
*/
		self::$isInitialized = true;
	}
}

Env::init();
