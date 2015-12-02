## Install

See src/readme.txt

## Hacking

Make sure you have and npm installed.

Run `npm install` in the project root.

### Build targets

Run `node build release` with one of the following targets:

`release`

Creates an release in the release folder.
The target reads the latest version number from the `src/readme.txt` file and updates the version number macro in all files before creating the zip file.

` > shjs build release`

`clean`

Removes the release and tmp folder.

`> shjs build clean`