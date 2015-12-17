/* globals require, __dirname, mkdir, sed, cd, cp, echo, cat, exec, rm, exit */

require( 'shelljs/make' );

const PLUGIN_NAME = 'woocommerce-bring-fraktguiden';
const SRC_DIR = 'src';
const RELEASE_DIR = 'release';
const TEMP = 'temp';
const TEMP_DIR = TEMP + '/woocommerce-bring-fraktguiden';
const WORDPRESS_REPO = 'https://plugins.svn.wordpress.org/bring-fraktguiden-for-woocommerce';

/**
 * Build targets
 * @type {{release: release, publish: publish, clean: Function}}
 */
target = {
    release: release,
    publish: publish,
    zip: zip,
    version: function () {
        echo( getVersionNumber() );
    },
    clean: function() {
        clean(true);
    }
};

function zip() {
    // Create a temporary directory and work with files from there.
    prepare();

    cd( TEMP );
    var versionNumber = getVersionNumber();
    var dateString = createDateString(new Date());
    var fileName = PLUGIN_NAME + '-' + versionNumber + '-' + dateString + '.zip';
    var destination = __dirname + '/' + RELEASE_DIR + '/' + fileName;
    exec( 'zip -r ' + destination + ' ' + PLUGIN_NAME );

    clean();
}

function release() {
    zip();
    publish( getVersionNumber() );
}

/**
 * Publishes the git repo to the wordpress.org svn repo.

 * @param version String if given, a new tag will be created in the svn repository.
 */
function publish( version ) {
    prepare();

    var svnDir = 'wordpress.org';

    cd( __dirname + '/' + RELEASE_DIR );
    if ( test( '-d', svnDir ) ) {
        rm( '-rf', svnDir );
    }
    mkdir( '-p', svnDir );

    // Checkout the repo.
    cd( svnDir );
    exec( 'svn co ' + WORDPRESS_REPO + ' .', {silent: true} );

    // Remove all files from trunk in order to pick up deleted files changes.
    rm( '-rf', 'trunk/*' );

    // Copy changes to trunk.
    cp( '-Rf', __dirname + '/' + TEMP_DIR + '/*', 'trunk' );

    // Add and delete files from the repo based on svn-status.
    var statusText = exec( 'svn status', {silent: true} ).output;
    var statusLines = statusText.split( '\n' );
    statusLines.forEach( function ( line ) {
        if ( line.trim() != '' ) {
            var parts = line.split( /\s+/ );
            var status = parts[0];
            var file = parts[1];
            if ( status == '?' ) {
                exec( 'svn add ' + file );
            }
            if ( status == '!' ) {
                exec( 'svn delete ' + file );
            }
        }
    } );

    // Start committing the changes.
    var gitRevision = exec('git rev-parse HEAD', {silent:true} ).output.replace('\n','');
    var commitMessage = 'Sync with git repository (' + gitRevision + ')';

    // Create a new svn tag if version is given.
    if ( version ) {
        exec( 'svn cp trunk tags/' + version );
        commitMessage += ' - tagging version ' + version;
    }

    echo( commitMessage );

    // Commit.
    exec( 'svn commit -m "' + commitMessage + '"' );

    clean();
}

function clean( all ) {
    cd( __dirname );
    rm( '-rf', TEMP );
    if ( all ) {
        rm( '-rf', RELEASE_DIR );
    }
}

function prepare() {
    clean();
    cd( __dirname );

    var versionNumber = getVersionNumber();

    mkdir( '-p', TEMP_DIR );
    if ( ! test( '-d', RELEASE_DIR ) ) mkdir( RELEASE_DIR );

    cp( '-R', SRC_DIR + '/', TEMP_DIR );

    sed( '-i', '##VERSION##', versionNumber, TEMP_DIR + '/woocommerce-bring-fraktguiden.php' );
    sed( '-i', '##VERSION##', versionNumber, TEMP_DIR + '/readme.txt' );
}

function getVersionNumber() {
    var readmeFile = __dirname + '/' + SRC_DIR + '/readme.txt';
    var contents = cat( readmeFile );

    var parts = contents.split( '== Changelog ==' );
    if ( parts.length != 2 ) {
        echo( 'Could not find "== Changelog ==" in ' + readmeFile + '. Aborting' );
        exit( 1 );
    }

    var changeLog = parts[1];
    var logs = changeLog.match( /^= \d\.\d\.\d(|[-|\s|\w]+) =/gm );
    if ( ! logs ) {
        echo( 'Could not find a version number. Aborting.' );
        exit( 1 );
    }
    var versionNumber = logs[0].replace( /=/g, '' ).trim();
    return versionNumber;
}

function createDateString( d ) {
    function pad( n ) {
        return n < 10 ? '0' + n : n
    }
    return d.getUTCFullYear()+'-'
        + pad(d.getUTCMonth()+1)+'-'
        + pad(d.getUTCDate())+'-'
        + pad(d.getUTCHours())+''
        + pad(d.getUTCMinutes())+''
        + pad(d.getUTCSeconds())+''
}
