require( 'shelljs/make' );

const PLUGIN_NAME = 'woocommerce-bring-fraktguiden';
const SRC_DIR = './src';
const RELEASE_DIR = './release';
const TMP_DIR = './tmp';

target.all = function () {
};

target.release = function () {
    clean();

    if ( !test( '-d', TMP_DIR ) ) {
        mkdir( TMP_DIR );
    }
    if ( !test( '-d', RELEASE_DIR ) ) {
        mkdir( RELEASE_DIR );
    }

    var dir = TMP_DIR + '/' + PLUGIN_NAME;

    mkdir( dir );

    cp( '-R', SRC_DIR + '/', dir );

    var versionNumber = getVersionNumber();

    sed( '-i', '##VERSION##', versionNumber, dir + '/woocommerce-bring-fraktguiden.php' );

    cd( TMP_DIR );
    var zipfile = '../' + RELEASE_DIR + '/' + PLUGIN_NAME + '-' + versionNumber + '.zip';
    exec( 'zip -r ' + zipfile + ' ' + PLUGIN_NAME );
    cd( '../' );
    clean();
};

target.clean = function () {
    clean( true );
};

function clean( full ) {
    rm( '-rf', TMP_DIR );
    if ( full ) {
        rm( '-rf', RELEASE_DIR );
    }
}

function getVersionNumber() {
    var readmeFile = TMP_DIR + '/' + PLUGIN_NAME + '/readme.txt';
    var contents = cat( readmeFile );
    var parts = contents.split( '== Changelog ==' );
    if ( parts.length != 2 ) {
        echo( 'Could not find "== Changelog ==" in ' + readmeFile + '. Aborting' );
        exit( 1 );
    }

    var changeLog = parts[1];
    var logs = changeLog.match( /^= \d\.\d\.\d[-|\w]+ =/gm );
    if ( !logs ) {
        echo( 'Could not find a version number. Aborting.' );
        exit( 1 );
    }
    var versionNumber = logs[0].replace( /=/g, '' ).trim();
    return versionNumber;
}
