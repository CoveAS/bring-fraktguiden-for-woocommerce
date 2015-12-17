/* globals require, __dirname, mkdir, sed, cd, cp, echo, cat, exec, rm, exit */

require( 'shelljs/make' );

const PLUGIN_NAME = 'woocommerce-bring-fraktguiden';
const SRC_DIR = 'src';
const RELEASE_DIR = 'release';
const TEMP_DIR = 'tmp/woocommerce-bring-fraktguiden';

/**
 * Build targets
 * @type {{release: release, publish: publish, clean: Function}}
 */
target = {
    release: release,
    publish: publish,
    clean:   function () {
        clean( true )
    }
};

function release() {
    var versionNumber = getVersionNumber();
    var zipFileName = PLUGIN_NAME + '-' + versionNumber + '.zip';

    clean( true );

    // 1. Create the directories used for the build process.
    mkdir( '-p', TEMP_DIR );
    mkdir( RELEASE_DIR );

    // 2. Copy the source files to the temporary directory.
    cp( '-R', SRC_DIR + '/', TEMP_DIR );

    // 3. Replace occurences of the version macro with the version number found in the plugin readme file.

    sed( '-i', '##VERSION##', versionNumber, TEMP_DIR + '/woocommerce-bring-fraktguiden.php' );
    sed( '-i', '##VERSION##', versionNumber, TEMP_DIR + '/readme.txt' );

    // 4. Create the zip.
    cd( TEMP_DIR );
    // :)
    cd( '../');
    exec( 'zip -r ' + '../' + RELEASE_DIR + '/' + zipFileName + ' ' + PLUGIN_NAME );

    cp( '-R', TEMP_DIR, RELEASE_DIR );
    clean();
}

function publish() {
    cd( __dirname + '/' + RELEASE_DIR );
    mkdir('svn');
    cd('svn');
    exec('svn co https://plugins.svn.wordpress.org/bring-fraktguiden-for-woocommerce/ .');
}

function clean( all ) {
    cd(__dirname);
    rm( '-rf', 'tmp' );
    if ( all ) {
        rm( '-rf', RELEASE_DIR );
    }
}

function getVersionNumber() {
    var readmeFile = SRC_DIR + '/readme.txt';
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
