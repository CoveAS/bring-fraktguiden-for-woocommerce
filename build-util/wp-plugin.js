/* globals require, exports, ls, test,  __dirname, mkdir, sed, cd, cp, echo, cat, exec, rm, exit */
require( 'shelljs/global' );

exports.getVersionNumber = function ( readmeFile ) {
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
    return logs[0].replace( /=/g, '' ).trim();
};

/**
 *
 * @param repo {String}
 * @param srcDir {String}
 * @param commitMessage {String}
 * @param version {String} optional
 */
exports.commitToWordPressOrg = function ( repo, srcDir, commitMessage, version ) {
    exec( 'svn co ' + repo + ' .', {silent: false} );

    if ( version && test( '-d', 'tags/' + version ) ) {
        echo( 'SVN tag ' + version + ' exists. A new version should be created in readme.txt' );
        exit( 1 );
    }

    // Remove all files from trunk in order to pick up deleted files changes.
    rm( '-rf', 'trunk/*' );

    // Copy changes to trunk.
    cp( '-Rf', srcDir + '/*', 'trunk' );

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

    // Create version.
    if ( version ) {
        exec( 'svn cp trunk tags/' + version );
    }

    // Commit.
    //exec( 'svn commit -m "' + commitMessage + '"' );
};


//function Publisher( repo, version ) {
//    this.repo = repo;
//    if ( version && test( '-d', 'tags/' + version ) ) {
//        echo( version + ' exists. A new version should be created in readme.txt' );
//        exit( 1 );
//    }
//    this.version = version;
//    exec( 'svn co ' + this.repo + ' .', {silent: true} );
//}
//
//
//Publisher.prototype.addFiles = function ( srcDir ) {
//    // Remove all files from trunk in order to pick up deleted files changes.
//    rm( '-rf', 'trunk/*' );
//
//    // Copy changes to trunk.
//    cp( '-Rf', srcDir + '/*', 'trunk' );
//
//    var statusText = exec( 'svn status', {silent: true} ).output;
//    var statusLines = statusText.split( '\n' );
//    statusLines.forEach( function ( line ) {
//        if ( line.trim() != '' ) {
//            var parts = line.split( /\s+/ );
//            var status = parts[0];
//            var file = parts[1];
//            if ( status == '?' ) {
//                exec( 'svn add ' + file );
//            }
//            if ( status == '!' ) {
//                exec( 'svn delete ' + file );
//            }
//        }
//    } );
//};
//
//Publisher.prototype.commit = function ( commitMessage ) {
//    exec( 'svn commit -m "' + commitMessage + '"' );
//};
//
//exports.Publisher = Publisher;