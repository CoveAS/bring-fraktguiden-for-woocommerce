/* globals require, ls, test,  __dirname, mkdir, sed, cd, cp, echo, cat, exec, rm, exit */

require( 'shelljs/global' );

exports.getBranch = function () {
    return exec( 'git rev-parse --abbrev-ref HEAD', {silent: true} ).output.trim();
};

exports.getStatus = function () {
    return getStatus();
};

exports.isClean = function () {
    return getStatus().toLowerCase().indexOf( 'working directory clean' ) > -1;
};

exports.getCurrentCommitHash = function () {
    return exec( 'git rev-parse HEAD', {silent: true} ).output.replace( '\n', '' )
};

function getStatus() {
    return exec( 'git status', {silent: true} ).output;
}