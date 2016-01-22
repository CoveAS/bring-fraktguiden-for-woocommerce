/* globals require, ls, test,  __dirname, mkdir, sed, cd, cp, echo, cat, exec, rm, exit */

require( 'shelljs/make' );
var git = require( './build-util/git' );
var wp = require( './build-util/wp-plugin' );

const SELF_DIR = __dirname;
const PLUGIN_NAME = 'woocommerce-bring-fraktguiden';
const SRC_DIR = SELF_DIR + '/src';
const RELEASE_DIR = SELF_DIR + '/release';
const BUILD_DIR = SELF_DIR + '/temp';
const TEMP_DIR = BUILD_DIR + '/woocommerce-bring-fraktguiden';
const WP_REPO = 'https://plugins.svn.wordpress.org/bring-fraktguiden-for-woocommerce';

var versionNumber = wp.getVersionNumber( SRC_DIR + '/readme.txt' );

/**
 * Build targets
 * Usage:
 *  node build <target-name>
 */
target = {

    /**
     * Creates a release and pushes the version to wordpress.org
     */
    release: release,

    /**
     * Pushes the current source code to wordpress.org trunk
     */
    push: push,

    /**
     * Creates a zip file in the release directory
     */
    zip: zip,

    /**
     * Prints the current version number
     */
    version: function () {
        echo( versionNumber );
    },

    /**
     * Cleans the project
     */
    clean: function () {
        cleanPropject( true );
    }
};

function release() {
    push( versionNumber );
}

/**
 * Publishes the git repo to the wordpress.org svn repo.
 * @param version String If given, a new tag will be created in the svn repository.
 */
function push( version ) {
    if ( !git.isClean() ) {
        echo( 'Repo is not clean. Please add the files and commit or stash your changes.' );
        cleanPropject();
        exit( 1 );
    }
    createSourceCopy();

    cd( RELEASE_DIR );

    var svnDir = 'wordpress.org';

    if ( test( '-d', svnDir ) ) {
        rm( '-rf', svnDir );
    }
    mkdir( '-p', svnDir );
    cd( svnDir );

    var commitMessage = 'Sync with git repository (@' + git.getCurrentCommitHash() + ')';
    if ( version ) {
        commitMessage += ' - tagging version ' + version;
    }

    wp.commitToWordPressOrg(
        WP_REPO,
        TEMP_DIR,
        commitMessage,
        version
    );

    // Create git tag.
    if ( version ) {
        exec( 'git tag -a ' + version + ' -m "Tagging version ' + version + '"' );
        echo( '\nRemember to push the new git tag (' + version + ')' );
    }
    cleanPropject();
}

function zip() {
    createSourceCopy();
    var fileName = PLUGIN_NAME + '-' + versionNumber + '-' + createDateString( new Date() ) + '.zip';
    var zipFile = RELEASE_DIR + '/' + fileName;

    cd( BUILD_DIR );
    exec( 'zip -r ' + zipFile + ' ' + PLUGIN_NAME );
    cleanPropject();
}

function cleanPropject( all ) {
    cd( __dirname );
    rm( '-rf', BUILD_DIR );
    if ( all ) {
        rm( '-rf', RELEASE_DIR );
    }
}

function createSourceCopy() {
    cleanPropject();

    // Create a temporary directory
    mkdir( '-p', TEMP_DIR );

    // Create the release directory
    if ( !test( '-d', RELEASE_DIR ) ) mkdir( RELEASE_DIR );

    // Copy the source to the temporary directory
    cp( '-R', SRC_DIR + '/', TEMP_DIR );

    // Replace macros in the source copy
    ls( '-R', TEMP_DIR + '/*' ).forEach( function ( file ) {
        if ( !test( '-d', file ) ) {
            sed( '-i', /##VERSION##/g, versionNumber, file );
        }
    } );
}

function createDateString( d ) {
    function pad( n ) {
        return n < 10 ? '0' + n : n
    }

    return d.getUTCFullYear() + '-'
        + pad( d.getUTCMonth() + 1 ) + '-'
        + pad( d.getUTCDate() ) + '-'
        + pad( d.getUTCHours() ) + ''
        + pad( d.getUTCMinutes() ) + ''
        + pad( d.getUTCSeconds() ) + ''
}