# 1.17.08.08

## Fixes

  * `php-cs-fixer` Change method to apply fix with php-cs-fixer (Alexis von Glasow, 2017-08-07T13:48:14+02:00)

## Features

  * Add `expandflexentities`. (Pierre Tomasina, 2017-04-06T13:43:35+02:00)

## Chores
  
  * Run `hoa devtools:cs`. (Ivan Enderlin, 2017-04-06T13:44:46+02:00)
# 1.17.02.24

  * CS: Move to PHP CS Fixer 2.x. (Ivan Enderlin, 2017-02-24T16:29:54+01:00)
  * Documentation: Update an exception message. (Ivan Enderlin, 2017-02-21T17:07:54+01:00)
  * Snapshot: Force Github auth to use a token. (Ivan Enderlin, 2017-01-10T14:11:42+01:00)
  * Snapshot: Force tag to be signed. (Ivan Enderlin, 2017-01-10T14:11:19+01:00)

# 1.17.01.10

  * Quality: Happy new year! (Alexis von Glasow, 2017-01-09T21:37:03+01:00)
  * Documentation: New `README.md` file. (Ivan Enderlin, 2016-10-19T16:45:22+02:00)
  * Documentation: Update `support` properties. (Ivan Enderlin, 2016-10-05T20:30:45+02:00)
  * Router: Use HTTPS. (Ivan Enderlin, 2016-09-09T15:01:36+02:00)

# 1.16.09.06

  * Bin: Make `…:documentation --directories` optional. (Ivan Enderlin, 2016-08-29T11:42:55+02:00)
  * Bin: Add the `--open` option to `documentation`. (Ivan Enderlin, 2016-08-26T07:42:56+02:00)
  * Add a fixer to remove useless constructor return tag (Metalaka, 2016-04-28T10:13:58+02:00)

# 1.16.01.15

  * Composer: New stable libraries. (Ivan Enderlin, 2016-01-14T21:47:44+01:00)
  * CHANGELOG: Remove a snapshot. (Ivan Enderlin, 2016-01-14T19:15:54+01:00)

# 1.16.01.14

  * Snapshot: Fix repository root. (Ivan Enderlin, 2016-01-11T09:26:50+01:00)
  * Snapshot: Fix a missing variable. (Ivan Enderlin, 2016-01-11T09:23:29+01:00)
  * Quality: Drop PHP5.4. (Ivan Enderlin, 2016-01-11T09:15:26+01:00)
  * Quality: Run devtools:cs. (Ivan Enderlin, 2016-01-09T09:00:28+01:00)
  * Core: Remove `Hoa\Core`. (Ivan Enderlin, 2016-01-09T08:13:33+01:00)
  * Consistency: Use `Hoa\Consistency`. (Ivan Enderlin, 2015-12-08T11:04:13+01:00)
  * Event: Use `Hoa\Event`. (Ivan Enderlin, 2015-11-23T21:58:49+01:00)
  * Snapshot: Support no tag. (Ivan Enderlin, 2015-11-23T23:24:01+01:00)

# 0.15.10.21

  * Documentation: Unset XYL theme. (Ivan Enderlin, 2015-09-04T07:49:43+02:00)
  * Zsh support has been moved to a contribution. (Ivan Enderlin, 2015-09-02T14:10:31+02:00)

# 0.15.09.01

  * Add more rules in the documentation router. (Ivan Enderlin, 2015-09-01T15:54:00+02:00)
  * Add the `documentation` command. (Ivan Enderlin, 2015-09-01T08:45:05+02:00)
  * Add the `literature` router rule for the doc. (Ivan Enderlin, 2015-08-27T09:29:56+02:00)
  * Disable XYL theme when generating the doc. (Ivan Enderlin, 2015-08-20T07:54:41+02:00)
  * Skip unmodified chapters. (Ivan Enderlin, 2015-08-17T07:08:10+02:00)
  * Only compute the documentation once. (Ivan Enderlin, 2015-08-11T16:51:45+02:00)
  * Update `.gitignore` file. (Stéphane HULARD, 2015-08-03T11:26:57+02:00)
  * Merge `Hoathis\Documentation` into `Hoa\Devtools`. (Ivan Enderlin, 2015-07-28T16:55:43+02:00)

# 0.15.07.28

  * Require `hoa/cli`. (Ivan Enderlin, 2015-07-23T08:46:47+02:00)

# 0.15.04.17

  * Mention `hoa devtools:cs`. (Ivan Enderlin, 2015-04-17T11:40:32+02:00)
  * Move to PSR-1 and PSR-2. (Ivan Enderlin, 2015-04-17T11:35:47+02:00)
  * Add the `no_blank_lines_before_entity` fixer. (Ivan Enderlin, 2015-04-17T11:09:17+02:00)
  * Add `--dry-run` and `--diff`. `fix` always. (Ivan Enderlin, 2015-04-17T10:37:47+02:00)
  * Add licenses and API documentation. (Ivan Enderlin, 2015-04-17T10:37:30+02:00)
  * Use build-in fixers. (Ivan Enderlin, 2015-04-17T10:18:44+02:00)
  * Add `control_flow_statement` and `opening_tag`. (Ivan Enderlin, 2015-04-15T10:50:32+02:00)
  * Add new provided fixers. (Ivan Enderlin, 2015-04-15T10:50:17+02:00)
  * Automate the declaration of custom fixers. (Ivan Enderlin, 2015-04-15T10:49:33+02:00)
  * Add the `opening_tag` fixer. (Ivan Enderlin, 2015-04-15T10:46:51+02:00)
  * Add the `control_flow_statement` fixer. (Ivan Enderlin, 2015-04-15T10:46:30+02:00)
  * We must replace `@throw` by `@throws`. (Ivan Enderlin, 2015-03-07T17:21:45+01:00)
  * Remove the `return` fixer. (Ivan Enderlin, 2015-03-07T00:07:52+01:00)
  * Use the `no_blank_lines_after_class_opening` fixer. (Ivan Enderlin, 2015-03-06T23:47:19+01:00)
  * Add the `copyright` fixer. (Ivan Enderlin, 2015-03-06T23:23:39+01:00)
  * Fix CS. (Ivan Enderlin, 2015-03-06T23:18:59+01:00)
  * Add the `author` fixer. (Ivan Enderlin, 2015-03-06T23:09:06+01:00)
  * Add a wrapper around `php-cs-fixer`. (Ivan Enderlin, 2015-03-06T23:01:50+01:00)
  * Add the `phpdoc_throws` fixer. (Ivan Enderlin, 2015-03-06T23:00:54+01:00)
  * Rename a variable. (Ivan Enderlin, 2015-03-06T22:59:08+01:00)
  * Add the `phpdoc_access` fixer. (Ivan Enderlin, 2015-03-06T22:55:35+01:00)
  * Add the `phpdoc_var` fixer. (Ivan Enderlin, 2015-03-06T22:48:03+01:00)
  * Delete the temporary file when releasing on Github. (Ivan Enderlin, 2015-02-16T15:03:53+01:00)
  * Fix the Github release. (Ivan Enderlin, 2015-02-16T14:42:23+01:00)
  * No more library: Repository root instead. (Ivan Enderlin, 2015-02-16T13:58:05+01:00)
  * Add a mention about `hoa devtools:snapshot`. (Ivan Enderlin, 2015-02-13T17:11:58+01:00)
  * Set timezone to UTC. (Ivan Enderlin, 2015-02-13T17:01:46+01:00)
  * Remove SHA-1 in the changelog's entries. (Ivan Enderlin, 2015-02-13T17:00:06+01:00)
  * Support the `--only-*` options. (Ivan Enderlin, 2015-02-12T16:43:36+01:00)
  * More options, more command exec, Github release etc. (Ivan Enderlin, 2015-02-11T16:48:30+01:00)
  * Update API documentation. (Ivan Enderlin, 2015-02-09T21:12:57+01:00)
  * New workflow. (Ivan Enderlin, 2015-02-09T21:09:03+01:00)
  * Add the snapshot command. (Ivan Enderlin, 2015-02-05T18:49:35+01:00)
  * Add Zsh autocompletion for `hoa`. (Ivan Enderlin, 2015-01-23T17:55:06+01:00)

