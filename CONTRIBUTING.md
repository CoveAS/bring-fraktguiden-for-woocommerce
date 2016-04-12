# How to Contribute

Bring Fraktguiden for WooCommerce is 100% open source and we love pull requests and feedback from anyone. By participating in this project, you agree to abide by the Covenant [code of conduct](http://contributor-covenant.org/version/1/4/)

## Create a Ticket

* Make sure you have a [GitHub account](https://github.com/signup/free)
* Submit a ticket for your issue, assuming one does not already exist.
  * Clearly describe the issue including steps to reproduce when it is a bug.
  * Make sure you fill in the earliest version that you know has the issue.

## Make Changes

* Fork the project.
* Create a topic branch from the master branch.
  * To quickly create a topic branch based on master; `git checkout -b
    fix/master/my-contribution master`. Please avoid working directly on the
    `master` branch.
* Make sure you have tested your changes.
* Make commits of logical units.
* Check for unnecessary whitespace with `git diff --check` before committing.
* Make sure your commit messages are in the proper format.

````
$ git commit -m "A brief summary of the commit
> 
> A paragraph describing what changed and its impact."
````
* Create a [pull request](https://help.github.com/articles/using-pull-requests/)

## Creating a release

This section is for project maintainers only

* Make sure you have SVN installed and have read/write access to the WordPress.org project
* Install driv-cli
    * Add `git@bitbucket.org:drivdigital/driv-cli-wp.git` to your `${DRIV-CLI-HOME}/repositories.json`
* Navigate to the project
* Make sure the `build.json` has the correct version number
* Type `driv wp plugin` (assuming you have created an alias named `driv`)
* Choose `release`