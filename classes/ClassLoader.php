<?php

namespace Bring_Fraktguiden;

use Exception;

class ClassLoader
{
	/**
	 * @throws Exception
	 */
	public static function load($className): void
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
			['BringFraktguiden', ...$parts],
			$parts,
			$suggestion,
			self::getDashed($parts),
			self::getFolderDashed($parts),
		];

		foreach ($suggestions as $suggestion) {
			$fileName = array_pop($suggestion);
			$dir       = ($pro ? dirname(__DIR__) . '/pro' : __DIR__) . '/'. implode('/', $suggestion);
			if (str_ends_with($dir, '/')) {
				// Remove trailing slash
				$dir = substr($dir,0, -1);
			}

			$file = "{$dir}/{$fileName}.php";
			if (file_exists($file)) {
				require_once $file;
				return;
			}
		}
		throw new Exception('Class file not found for ' . $className);
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

	private static function getFolderDashed(array $parts): array
	{
		$fileName = array_pop($parts);
		$suggestion = array_map(
			fn($part) => strtolower(preg_replace('/(?<!^)([A-Z])/', '-$1', $part)),
			$parts
		);
		$suggestion[] = $fileName;
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
