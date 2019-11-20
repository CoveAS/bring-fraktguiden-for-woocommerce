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
if ( ! preg_match( '/^\d+\.\d+\.\d+$/', $version ) ) {
	die( "ERROR: Invalid version number in git tag, \"$version\". Should be \"#.#.#\".\n" );
}
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

$esc_version = str_replace( '.', '\.', $version );
echo "Checking readme.txt version number\n";
$content = `head -n 20 $dir/readme.txt`;
if ( ! preg_match( '/Stable tag:\s+' . $esc_version . '/', $content, $matches ) ) {
	die( "Stable tag doesn't match $version in readme.txt" );
}

echo "Checking bring-fraktguiden-for-woocommerce.php version number\n";
$content = `head -n 20 $dir/bring-fraktguiden-for-woocommerce.php`;
if ( ! preg_match( '/\* Version:\s+' . $esc_version . '/', $content, $matches ) ) {
	die( "Version doesn't match $version in bring-fraktguiden-for-woocommerce.php" );
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
// Cleanup
`find . -name ".DS_Store" -type d -delete`;
`rm -rf .git .gitignore composer.json svn-publisher.php README.md CONTRIBUTING.md`;
`rm -rf node_modules package.json package.lock webpack.mix.js`;
if ( file_exists( '.gitignore' ) ) {
	die( "ERROR: Cleanup failed.\n" );
}

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
unset( $output );
exec( 'svn st', $output, $result );
// echo "\nStatus: \n";
$lines = $output;
echo implode( "\n", $output );
foreach ( $lines as $line ) {
	$line = trim( $line );
	if ( ! $line ) {
		continue;
	}
	if ( ! preg_match( '/^([ACDIMRX\?~\!])\s+(\S.*)/', $line, $parts ) ) {
		die( "ERROR: Unidentified SVN modifier, \"$line\". Please investigate!\n" );
	}
	$modifier = $parts[1];
	$file     = $parts[2];
	if ( $modifier == '!' ) {
		`svn rm --force $file`;
	}
	if ( $modifier == '~' ) {
		die( "ERROR: SVN has a problem with one of the files, \"$file\". Please investigate!\n" );
	}
}

unset( $output );
exec( 'svn st', $output, $result );
echo "\nStatus: \n";
foreach ( $lines as $line ) {
	$line = trim( $line );
	if ( ! $line ) {
		continue;
	}
	if ( ! preg_match( '/^([ACDIMRX\?~\!])\s+(\S.*)/', $line, $parts ) ) {
		die( "ERROR: Unidentified SVN modifier, \"$line\". Please investigate!\n" );
	}
	$modifier = $parts[1];
	$file     = $parts[2];
	echo "[{$map[$modifier]}] {$file}\n";
}

$answer = trim( readline( 'Does this look ok? [y/N]' ) );
if ( $answer !== 'y' ) {
	die( "Exiting without committing!\n" );
}

echo "Committing to SVN\n";
exec( 'svn commit --username drivdigital -m "Synchronized trunk with master branch from Github"', $output, $result );
if ( $result ) {
	die( "ERROR: Failed to commit to SVN.\n" );
}
chdir( '..' );
exec( "svn cp trunk tags/$version && svn commit -m \"Updated the version number to $version\"", $output, $result );
if ( $result ) {
	die( "ERROR: Failed to copy the trunk to a new tag.\n" );
}
