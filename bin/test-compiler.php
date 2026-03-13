#!/usr/bin/env php
<?php
/**
 * BFG Component Compiler Test Runner
 *
 * Compiles test input files and compares against expected output
 *
 * Usage:
 *   php bin/test-compiler.php
 */

require_once __DIR__ . '/compile-templates.php';

class BFGCompilerTestRunner {
    private string $projectRoot;
    private string $testsDir;
    private string $inputDir;
    private string $expectedDir;
    private array $failures = [];
    private int $passed = 0;
    private int $failed = 0;

    public function __construct(string $projectRoot) {
        $this->projectRoot = $projectRoot;
        $this->testsDir = $projectRoot . '/tests';
        $this->inputDir = $this->testsDir . '/input';
        $this->expectedDir = $this->testsDir . '/expected';
    }

    /**
     * Run all tests
     */
    public function run(): int {
        echo "Running compiler tests...\n\n";

        // Find all test input files
        $inputFiles = glob($this->inputDir . '/*.bfg.php');

        if (empty($inputFiles)) {
            echo "No test files found in {$this->inputDir}\n";
            return 1;
        }

        foreach ($inputFiles as $inputFile) {
            $testName = basename($inputFile, '.bfg.php');
            $expectedFile = $this->expectedDir . '/' . $testName . '.php';

            if (!file_exists($expectedFile)) {
                echo "⚠️  SKIP: {$testName} (no expected output file)\n";
                continue;
            }

            $this->runTest($testName, $inputFile, $expectedFile);
        }

        // Summary
        echo "\n" . str_repeat('-', 50) . "\n";
        echo "Total: " . ($this->passed + $this->failed) . " | ";
        echo "Passed: {$this->passed} | ";
        echo "Failed: {$this->failed}\n";

        // Show failure details
        if (!empty($this->failures)) {
            $this->showFailureDetails();
        }

        return $this->failed > 0 ? 1 : 0;
    }

    /**
     * Run a single test
     */
    private function runTest(string $testName, string $inputFile, string $expectedFile): void {
        try {
            $compiler = new BFGComponentCompiler($this->projectRoot);
            $compiled = $compiler->compile($inputFile);
            $expected = file_get_contents($expectedFile);

            // Normalize whitespace for comparison
            $compiledNormalized = $this->normalizeWhitespace($compiled);
            $expectedNormalized = $this->normalizeWhitespace($expected);

            if ($compiledNormalized === $expectedNormalized) {
                echo "✅ PASS: {$testName}\n";
                $this->passed++;
            } else {
                echo "❌ FAIL: {$testName}\n";
                $this->failed++;
                $this->failures[] = [
                    'name' => $testName,
                    'expected' => $expected,
                    'actual' => $compiled,
                    'expected_normalized' => $expectedNormalized,
                    'actual_normalized' => $compiledNormalized
                ];
            }
        } catch (Exception $e) {
            echo "❌ ERROR: {$testName} - {$e->getMessage()}\n";
            $this->failed++;
            $this->failures[] = [
                'name' => $testName,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Normalize whitespace for comparison
     */
    private function normalizeWhitespace(string $content): string {
        // Trim each line and remove empty lines
        $lines = array_filter(
            array_map('trim', explode("\n", $content)),
            fn($line) => $line !== ''
        );
        return implode("\n", $lines);
    }

    /**
     * Show detailed failure information
     */
    private function showFailureDetails(): void {
        echo "\n" . str_repeat('=', 50) . "\n";
        echo "FAILURE DETAILS:\n";
        echo str_repeat('=', 50) . "\n\n";

        foreach ($this->failures as $failure) {
            echo "Test: {$failure['name']}\n";
            echo str_repeat('-', 50) . "\n";

            if (isset($failure['error'])) {
                echo "Error: {$failure['error']}\n\n";
            } else {
                echo "Expected:\n{$failure['expected']}\n\n";
                echo "Actual:\n{$failure['actual']}\n\n";

                // Show normalized diff for easier debugging
                echo "Normalized Diff:\n";
                $this->showDiff($failure['expected_normalized'], $failure['actual_normalized']);
                echo "\n";
            }
        }
    }

    /**
     * Show a simple diff between expected and actual
     */
    private function showDiff(string $expected, string $actual): void {
        $expectedLines = explode("\n", $expected);
        $actualLines = explode("\n", $actual);
        $maxLines = max(count($expectedLines), count($actualLines));

        for ($i = 0; $i < $maxLines; $i++) {
            $expectedLine = $expectedLines[$i] ?? '';
            $actualLine = $actualLines[$i] ?? '';

            if ($expectedLine !== $actualLine) {
                echo "  Line " . ($i + 1) . ":\n";
                echo "    Expected: " . ($expectedLine ?: '(empty)') . "\n";
                echo "    Actual:   " . ($actualLine ?: '(empty)') . "\n";
            }
        }
    }
}

// CLI entry point
if (php_sapi_name() === 'cli') {
    $projectRoot = dirname(__DIR__);
    $testRunner = new BFGCompilerTestRunner($projectRoot);
    $exitCode = $testRunner->run();
    exit($exitCode);
}
