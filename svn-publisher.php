<?php

$url = 'https://plugins.svn.wordpress.org/bring-fraktguiden-for-woocommerce';
$cwd = getcwd();
$dir = __DIR__;

if ( strpos( $cwd, $dir ) ) {
	echo "Don't run this command inside the bring repo please.\n";
	die;
}

chdir( $dir );

exec( 'git status -s', $output, $result );
if ( $result ) {
	die( "ERROR: Could not get changes from git repo.\n" );
}
unset( $output );
// if ( ! empty( $output ) ) die( "ERROR: There are uncommited changes in the git repo.\n" );
exec( 'git tag --contains', $output, $result );
if ( $result ) {
	die( "ERROR: Could not get tags from git repo.\n" );
}
if ( empty( $output ) ) {
	die( "ERROR: No version tag found for the current git commit.\n" );
}
$version = trim( $output[0] );
if ( ! preg_match( '/^\d+\.\d+\.\d+(-rc\d+)?$/', $version, $rc ) ) {
	die( "ERROR: Invalid version number in git tag, \"$version\". Should be \"#.#.#\" or \"#.#.#-rc#\".\n" );
}
// A release candidate goes to trunk only. WordPress serves the stable tag, so no
// site updates to it. Testers download it as the Development Version.
$is_dev = ! empty( $rc[1] );
echo $is_dev ? "Publishing development version $version to trunk only.\n" : "Publishing release $version.\n";
unset( $output );
// Go back
chdir( $cwd );
exec( "svn list $url/tags", $output, $result );
if ( $result ) {
	die( "ERROR: Could not get tags from svn repo.\n" );
}
$tags           = $output;
$version_exists = false;
foreach ( $tags as $tag ) {
	$tag = substr( trim( $tag ), 0, -1 );
	if ( ! $tag ) {
		continue;
	}
	echo "$version != $tag ";
	if ( $version == $tag ) {
		echo '✗';
		$version_exists = true;
	} else {
		echo '✓';
	}
	echo "\n";
}
$version_exists && die( "ERROR: Version, $version, already exists" );

$esc_version = preg_quote( $version, '/' );
echo "Checking readme.txt version number\n";
$content        = file_get_contents( "$dir/readme.txt" );
$stable_is_this = (bool) preg_match( '/Stable tag:\s+' . $esc_version . '\s/', $content );
if ( $is_dev && $stable_is_this ) {
	die( "ERROR: Stable tag names $version in readme.txt. A development version has no tag to serve.\n" );
}
if ( ! $is_dev && ! $stable_is_this ) {
	die( "Stable tag doesn't match $version in readme.txt" );
}

if ( 'svn-bring-fraktguiden-for-woocommerce' !== basename( $cwd ) ) {
	// Create a new dir
	if ( ! is_dir( 'svn-bring-fraktguiden-for-woocommerce' ) ) {
		if ( ! mkdir( 'svn-bring-fraktguiden-for-woocommerce' ) ) {
			echo "Could not make the directory.\n";
			die;
		}
	}
	chdir( 'svn-bring-fraktguiden-for-woocommerce' );
}

if ( is_dir( '.svn' ) ) {
	// Update an existing SVN
	echo "Updating SVN.\n";
	exec( 'svn up', $output, $result );
	if ( $result ) {
		die( "ERROR: SVN update failed.\n" );
	}
} else {
	echo "Checking out from SVN.\n";
	// Checkout a new shallow copy
	exec( "svn co --depth immediates $url .", $output, $result );
	if ( $result ) {
		die( "ERROR: SVN checkout failed.\n" );
	}
	exec( 'svn update --set-depth infinity trunk', $output, $result );
	if ( $result ) {
		die( "ERROR: SVN checkout failed.\n" );
	}
}

// Build assets in git repo before copying
echo "Building production assets in git repo.\n";
$current_dir = getcwd();
chdir( $dir );
exec( 'npm install --production=false 2>&1', $output, $result );
if ( $result ) {
	die( "ERROR: npm install failed.\n" . implode("\n", $output) . "\n" );
}
unset( $output );
exec( 'npm run production 2>&1', $output, $result );
if ( $result ) {
	die( "ERROR: npm run production failed.\n" . implode("\n", $output) . "\n" );
}
// Verify build outputs exist
$required_files = [
	'build/js/admin.js',
	'build/js/checkout.js',
	'build/js/customs-fields.js',
	'build/js/field-validation.js',
	'build/js/shared/vue-runtime.js',
	'build/js/shipping-services.js',
	'build/css/admin.css',
	'build/css/shipping-services.css',
];
foreach ( $required_files as $file ) {
	if ( ! file_exists( $file ) ) {
		die( "ERROR: Required build output missing: $file\n" );
	}
}
echo "✓ Build successful, all assets present.\n";
chdir( $current_dir );
unset( $output );

// Remove existing trunk
echo "Copying from git repo.\n";
exec( 'rm -rf trunk', $output, $result );
if ( $result ) {
	die( "ERROR: Could not remove trunk.\n" );
}

// Copy from git repo
exec( 'cp -r "' . $dir . '/" trunk/', $output, $result );
if ( $result ) {
	die( "ERROR: Copying git repo failed.\n" );
}

// Check that files were copied
if ( ! file_exists( 'trunk/svn-publisher.php' ) ) {
	die( "ERROR: svn-publisher.php script (this file) was not copied.\n" );
}

// Go into the trunk
chdir( 'trunk' );

// Replace version number placeholder
echo "Replacing bring-fraktguiden-for-woocommerce.php version number\n";
$content = file_get_contents('bring-fraktguiden-for-woocommerce.php');
$content = str_replace('###BRING_VERSION###', $version, $content);
file_put_contents('bring-fraktguiden-for-woocommerce.php', $content);

echo "Replacing classes/class-bring-fraktguiden.php version number\n";
$content = file_get_contents('classes/class-bring-fraktguiden.php');
$content = str_replace('###BRING_VERSION###', $version, $content);
file_put_contents('classes/class-bring-fraktguiden.php', $content);

echo "Checking bring-fraktguiden-for-woocommerce.php version number\n";
$content = file_get_contents( 'bring-fraktguiden-for-woocommerce.php' );
if ( ! preg_match( '/\* Version:\s+' . $esc_version . '/', $content, $matches ) ) {
	die( "Version doesn't match $version in bring-fraktguiden-for-woocommerce.php" );
}

echo "Checking classes/class-bring-fraktguiden.php version number\n";
$content = file_get_contents( 'classes/class-bring-fraktguiden.php' );
if ( ! preg_match( '/\sVERSION\s+=\s+\'' . $esc_version . '\';/', $content, $matches ) ) {
	die( "Version doesn't match $version in classes/class-bring-fraktguiden.php\n\n". $content );
}
// Cleanup. Everything the shop does not run stays out of the release.
exec( 'find . -name ".DS_Store" -delete' );
$development_only = [
	'.claude',
	'.git',
	'.gitignore',
	'.idea',
	'.sublime-settings',
	'bin',
	'bring-timeslot.md',
	'CLAUDE.md',
	'composer.json',
	'composer.lock',
	'CONTRIBUTING.md',
	'doc',
	'memory',
	'node_modules',
	'package-lock.json',
	'package.json',
	'phpmd.xml',
	'postcss.config.mjs',
	'pro/resources',
	'README.md',
	'resources',
	'src',
	'svn-publisher.php',
	'tags',
	'tests',
	'vendor',
	'vite.config.mjs',
];
foreach ( $development_only as $path ) {
	exec( 'rm -rf ' . escapeshellarg( $path ) );
}
if ( file_exists( '.gitignore' ) ) {
	die( "ERROR: Cleanup failed.\n" );
}

/**
 * Find every quoted path in the release that names a folder the cleanup
 * removed. The shop would request a file that the release does not hold.
 *
 * Write "bfg-release-ignore" in a comment on a line that names such a path on
 * purpose, for example a path that only a local development build reaches.
 */
function bfg_dangling_paths( array $removed ): array {
	$pattern = '#[\'"]/?(' . implode( '|', array_map( fn( $path ) => preg_quote( $path, '#' ), $removed ) ) . ')/#';
	$files   = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator( '.', FilesystemIterator::SKIP_DOTS )
	);
	$found = [];
	foreach ( $files as $file ) {
		if ( ! preg_match( '/\.(php|js)$/', $file->getFilename() ) ) {
			continue;
		}
		foreach ( file( $file->getPathname() ) as $index => $line ) {
			if ( str_contains( $line, 'bfg-release-ignore' ) ) {
				continue;
			}
			if ( preg_match( $pattern, $line ) ) {
				$found[] = $file->getPathname() . ':' . ( $index + 1 ) . '  ' . trim( $line );
			}
		}
	}
	return $found;
}

echo "Checking the release for paths the cleanup removed.\n";
$dangling = bfg_dangling_paths( $development_only );
if ( $dangling ) {
	die( "ERROR: The release names files that the cleanup removed:\n  " . implode( "\n  ", $dangling ) . "\n" );
}
echo "\u{2713} No release file names a removed folder.\n";

// Add and commit changes
exec( 'svn --force --depth infinity add .', $output, $result );
if ( $result ) {
	die( "ERROR: Could not stage svn changes.\n" );
}

// Confirm staged changes
$map = [
	' ' => 'No changes',
	'A' => 'Added',
	'C' => 'Conflicted',
	'D' => 'Deleted',
	'I' => 'Ignored',
	'M' => 'Modified',
	'R' => 'Replaced',
	'X' => 'Unversioned dir',
	'?' => 'Unstaged',
	'~' => 'Error',
	'!' => 'Missing',
];
/**
 * Read one line of "svn st". The first column holds the state of the item, and
 * six more columns follow before the path. So "!M" means a missing item with a
 * changed property.
 */
function bfg_svn_status_line( string $line ): ?array {
	if ( ! preg_match( '/^(.)(.{6})\s+(\S.*)$/', rtrim( $line ), $parts ) ) {
		return null;
	}
	return [ $parts[1], trim( $parts[3] ) ];
}

function bfg_svn_status( array $map ): array {
	$output = [];
	exec( 'svn st', $output );
	$rows = [];
	foreach ( $output as $line ) {
		if ( ! trim( $line ) ) {
			continue;
		}
		$row = bfg_svn_status_line( $line );
		if ( null === $row ) {
			die( "ERROR: Unreadable SVN status line, \"$line\". Please investigate!\n" );
		}
		if ( ! isset( $map[ $row[0] ] ) ) {
			die( "ERROR: Unidentified SVN modifier, \"$line\". Please investigate!\n" );
		}
		if ( '~' === $row[0] ) {
			die( "ERROR: SVN has a problem with one of the files, \"{$row[1]}\". Please investigate!\n" );
		}
		$rows[] = $row;
	}
	return $rows;
}

// Tell SVN about every file the cleanup removed. A parent covers its children,
// so removing a child afterwards fails with "is not a working copy".
$missing = [];
foreach ( bfg_svn_status( $map ) as [ $modifier, $file ] ) {
	if ( '!' === $modifier ) {
		$missing[] = $file;
	}
}
sort( $missing );
$removed = [];
foreach ( $missing as $file ) {
	foreach ( $removed as $parent ) {
		if ( str_starts_with( $file, $parent . '/' ) ) {
			continue 2;
		}
	}
	$output = [];
	exec( 'svn rm --force ' . escapeshellarg( $file ), $output, $result );
	if ( $result ) {
		die( "ERROR: Could not remove \"$file\" from SVN.\n" );
	}
	$removed[] = $file;
}

echo "\nStatus: \n";
foreach ( bfg_svn_status( $map ) as [ $modifier, $file ] ) {
	echo "[{$map[$modifier]}] {$file}\n";
}

$answer = trim( readline( 'Does this look ok? [y/N]' ) );
if ( $answer !== 'y' ) {
	die( "Exiting without committing!\n" );
}

echo "Committing to SVN\n";
$message = $is_dev ? "Published development version $version" : 'Synchronized trunk with master branch from Github';
exec( "svn commit --username Forsvunnet -m \"$message\"", $output, $result );
if ( $result ) {
	die( "ERROR: Failed to commit to SVN.\n" );
}
if ( $is_dev ) {
	echo "Done. Testers get $version from the Development Version download.\n";
	exit;
}
chdir( '..' );
exec( "svn cp trunk tags/$version && svn commit -m \"Updated the version number to $version\"", $output, $result );
if ( $result ) {
	die( "ERROR: Failed to copy the trunk to a new tag.\n" );
}
