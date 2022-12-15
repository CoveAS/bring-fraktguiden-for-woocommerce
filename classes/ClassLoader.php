<?php

namespace Bring_Fraktguiden;

class ClassLoader
{
	public static function load($className)
	{
		if (!preg_match('/^Bring_?Fraktguiden_?(Pro)?(\\\.*)$/', $className, $matches)) {
			return;
		}
		$pro = $matches[1];
		$name = $matches[2];
		$path      = substr($name, 1);
		$parts     = explode('\\', $path);

		$suggestion = $parts;
		$fileName = array_pop($suggestion);
		$suggestion = array_map(
			fn($part) => strtolower($part),
			$suggestion
		);
		$suggestion[] = $fileName;

		$suggestions = [
			$parts,
			$suggestion,
			self::getDashed($parts),
		];

		foreach ($suggestions as $suggestion) {
			$fileName = array_pop($suggestion);
			$dir       = ($pro ? dirname(__DIR__) . '/pro' : __DIR__) . '/'. implode('/', $suggestion);
			if (substr($dir, -1) === '/') {
				// Remove trailing slash
				$dir = substr($dir,0, -1);
			}

			$file = "{$dir}/{$fileName}.php";
			if (file_exists($file)) {
				require_once $file;
				return;
			}
		}
	}

	private static function getDashed(array $parts): array
	{
		$suggestion = array_map(
			fn($part) => str_replace('_', '-', strtolower($part)),
			$parts
		);
		$fileName = array_pop($suggestion);
		$suggestion[] = "class-{$fileName}";
		return $suggestion;
	}

	private static function getCommon(array $parts)
	{

		$suggestion = self::getDashed($parts);
		return [
			'common',
			array_pop($suggestion)
		];
	}
}
