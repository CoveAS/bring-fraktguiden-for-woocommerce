## Install

See src/readme.txt

## Building

Before building the project, verify that you have npm (Node Package Manager) installed

Run `node build` with one of the following targets:

`zip`

- Creates a zip file of the plugin code in the release directory

`clean`

- Cleans the project. All zip files etc. will be deleted.

`release`

- (wordpress.org "committers" only) Used for publishing a new release - Creates a new version (svn-tag)  based on current source and pushes the tag and source to wordpress.org

`push`

- (wordpress.org "committers" only) Pushes the current source to wordpress.org trunk
