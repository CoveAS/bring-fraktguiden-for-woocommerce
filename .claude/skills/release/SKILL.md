---
name: release
description: Prepare, tag and push a release or a release candidate of the plugin, then hand the wordpress.org publish to the user. Use when the user asks to release, ship, tag or publish a version, or asks for a release candidate.
---

# Release

The skill prepares a version, tags it and pushes it to GitHub. The user then
runs `svn-publisher.php`, which sends the version to wordpress.org.

The publisher needs the SVN password and asks a `y/N` question. Do not run it.
Give the user the command at the end.

See "Releases" in `doc/dictionary.md` for the words release, release
candidate, Stable tag and trunk.

## 1. Pick the kind

Make a release. Make a release candidate only when the user asks for one.

## 2. Find the version

Read the `Stable tag` and the top `= X.Y.Z =` heading under `== Changelog ==`
in `readme.txt`.

Use the heading when it is newer than the `Stable tag`. Otherwise stop, and ask
the user for the number. Do not guess a bump.

A release candidate takes the next free suffix, such as `1.13.0-rc2` after
`1.13.0-rc1`. Read the existing tags with `git tag --list 'X.Y.Z-rc*'`.

## 3. Check the start

Stop when one of these fails:

1. The branch is `main`.
2. `git status --short` prints nothing.
3. The git tag for the version does not exist yet.

## 4. Check the changelog

1. List the commits since the last release tag with `git log --oneline <last tag>..HEAD`.
2. Name each commit that changes what a shop sees or does, but has no line under the version heading.
3. Draft the missing lines in the style of the existing ones, for example "Fixed the X, which did Y".
4. Show the drafts to the user. Write them only after the user agrees.

A release candidate writes its lines under the base version heading, such as
`= 1.13.0 =`.

## 5. Set the Stable tag

A release sets `Stable tag:` in `readme.txt` to the version.

A release candidate leaves the `Stable tag` alone. The publisher refuses a
release candidate that the `Stable tag` names.

Remind the user of `WC tested up to:`. Do not change it. A change needs a test
on the new WooCommerce.

## 6. Update the translations

Run the four commands from "Update the catalogs" in `CLAUDE.md`.

1. Discard the POT and `.po` changes when they hold only line references and dates.
2. Otherwise write a Norwegian translation for each new empty `msgstr` in the `.po` file.
3. Run `msgfmt` again after the edit.
4. Show the new Norwegian lines to the user before the commit.

## 7. Run the checks

Stop when one fails, and show the output.

1. `npm run test-js`
2. `npm run test-php-compiler`
3. `/opt/homebrew/opt/php@8.2/bin/php -l <file>` for each PHP file changed since the last release tag. Check the 8.2 features by eye, as `CLAUDE.md` says.
4. `npm run production`

## 8. Commit and tag

1. Commit the changes as "prepare the X.Y.Z release". Name in the body what changed and why the catalogs did or did not change.
2. Add a lightweight tag on that commit: `git tag X.Y.Z`.

## 9. Push

Ask the user once. Then push both:

```
git push origin main
git push origin X.Y.Z
```

Make no GitHub release.

## 10. Hand over the publish

Give the user this command. It runs from any folder.

```
php ~/Workspace/bringdemo/public/wp-content/plugins/bring-fraktguiden-for-woocommerce/svn-publisher.php
```

The publisher uses the SVN working copy in
`~/Workspace/svn-bring-fraktguiden-for-woocommerce`. A first argument names
another folder.

The publisher reads the version from the git tag on HEAD. So HEAD must stay
on the tagged commit until the publish ends.

A release candidate goes to trunk only. A tester downloads it as the
Development Version on wordpress.org.
