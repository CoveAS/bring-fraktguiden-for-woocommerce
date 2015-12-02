## Install


See src/readme.tx

## Hacking

Make sure you have and npm installed.

Install shelljs

`npm install -g shelljs`

### Build targets

Run `shjs build` with one of the following targets:

`release`

Creates an release in the release folder.
The target reads the latest version number from the `src/readme.txt` file and updates the version number macro in all files before creating the zip file.


` > shjs build release`

`clean`

Removes the release and tmp folder.

`> shjs build clean`