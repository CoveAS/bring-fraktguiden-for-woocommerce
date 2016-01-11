## Install

See src/readme.txt

## Building

Before building the project, verify that you have npm (Node Package Manager) installed

Run `node build` with one of the following targets:

`zip`

- Creates a zip file of the plugin code in the release directory

`push`

- (wordpress.org "committers" only) Commits the current source to wordpress.org trunk

`clean`

- Cleans the project. All zip files etc. will be deleted.

`release`

- (wordpress.org "committers" only) Used for publishing a new release - Creates a new version (tag) based on current source and commits the tag and source to wordpress.org
