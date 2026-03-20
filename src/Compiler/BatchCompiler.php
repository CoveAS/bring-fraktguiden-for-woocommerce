<?php

class BFG_BatchCompiler
{
	private string $projectRoot;
	private string $sourceDir;
	private string $outputDir;

	public function __construct(string $projectRoot)
	{
		$this->projectRoot = $projectRoot;
		$this->sourceDir = $projectRoot . '/src/templates';
		$this->outputDir = $projectRoot . '/build/templates';
	}

	/**
	 * Compile all .bfg.php templates in the source directory
	 *
	 * @return BFG_CompilationResult
	 */
	public function compileAll(): BFG_CompilationResult
	{
		$sourceFiles = $this->findSourceFiles();
		$compiler = new BFGComponentCompiler($this->projectRoot);

		$compiled = 0;
		$failed = 0;
		$errors = [];

		foreach ($sourceFiles as $sourceFile) {
			try {
				$output = $compiler->compile($sourceFile);
				$outputPath = $this->getOutputPath($sourceFile);

				// Create output directory if needed
				$outputDir = dirname($outputPath);
				if (!is_dir($outputDir)) {
					mkdir($outputDir, 0755, true);
				}

				// Write compiled output
				file_put_contents($outputPath, $output);
				$compiled++;
			} catch (Exception $e) {
				$failed++;
				$errors[] = new BFG_CompilationError(
					basename($sourceFile),
					$e->getMessage()
				);
			}
		}

		return new BFG_CompilationResult($compiled, $failed, $errors);
	}

	/**
	 * Find all .bfg.php files in the source directory
	 *
	 * @return array
	 */
	private function findSourceFiles(): array
	{
		$sourceFiles = [];
		$iterator = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($this->sourceDir, RecursiveDirectoryIterator::SKIP_DOTS)
		);

		foreach ($iterator as $file) {
			if ($file->isFile() && str_ends_with($file->getFilename(), '.bfg.php')) {
				$sourceFiles[] = $file->getPathname();
			}
		}

		return $sourceFiles;
	}

	/**
	 * Calculate output path for a source file
	 *
	 * @param string $sourceFile
	 * @return string
	 */
	private function getOutputPath(string $sourceFile): string
	{
		$relativePath = str_replace($this->sourceDir . '/', '', $sourceFile);
		return $this->outputDir . '/' . str_replace('.bfg.php', '.php', $relativePath);
	}
}

/**
 * Result object for batch compilation
 */
class BFG_CompilationResult
{
	public int $compiled;
	public int $failed;
	/** @var BFG_CompilationError[] */
	public array $errors;

	public function __construct(int $compiled, int $failed, array $errors)
	{
		$this->compiled = $compiled;
		$this->failed = $failed;
		$this->errors = $errors;
	}

	public function hasErrors(): bool
	{
		return $this->failed > 0;
	}

	public function getErrorMessages(): array
	{
		return array_map(fn($error) => $error->file . ': ' . $error->message, $this->errors);
	}
}

/**
 * Compilation error object
 */
class BFG_CompilationError
{
	public string $file;
	public string $message;

	public function __construct(string $file, string $message)
	{
		$this->file = $file;
		$this->message = $message;
	}
}
