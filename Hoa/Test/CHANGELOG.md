# 2.17.08.18

  * fix(doctest) Always add Kitab output directory. (Ivan Enderlin, 2017-08-18T10:42:18+02:00)

# 2.17.08.16

  * fix(run) Kitab must not override default directory. (Ivan Enderlin, 2017-08-15T14:26:04+02:00)

# 2.17.08.15

  * fix(doctest) Remove the `assert` asserter. (Ivan Enderlin, 2017-08-15T11:57:15+02:00)
  * chore(composer) Require at least hoa/kitab 0.6. (Ivan Enderlin, 2017-08-15T11:51:46+02:00)
  * feat(doctest) Use Kitab. (Ivan Enderlin, 2017-08-15T11:47:22+02:00)
  * chore(cs) Use spaces, rename a variable, fix doc. (Ivan Enderlin, 2017-08-15T11:25:02+02:00)
  * feat(run) Add the --no-code-coverage option. (Grummfy, 2017-07-06T19:52:54+02:00)
  * Quality: Extract the reducer in its own variable. (Ivan Enderlin, 2017-04-06T12:47:11+02:00)
  * Quality: Clean up the code. (Ivan Enderlin, 2017-04-06T12:41:53+02:00)
  * Generator: Support `use` and `#`. (Ivan Enderlin, 2017-03-07T08:21:45+01:00)
  * Generator: Allow `must_throw(E)`. (Ivan Enderlin, 2017-03-06T17:07:52+01:00)
  * Generator: Clean the output. (Ivan Enderlin, 2017-03-06T08:27:33+01:00)
  * Generator: Support `must_throw` in code block type. (Ivan Enderlin, 2017-03-06T08:23:40+01:00)
  * Generator: Support `ignore` in code block type. (Ivan Enderlin, 2017-03-06T08:13:47+01:00)
  * Generator: Ignore code block whose is not PHP. (Ivan Enderlin, 2017-03-06T08:05:17+01:00)
  * Documentation: Generate classes that need to. (Ivan Enderlin, 2017-03-03T16:58:43+01:00)
  * Run: Auto-generate doc API tests when running. (Ivan Enderlin, 2017-03-03T08:22:35+01:00)
  * Generator: Move arguments from ctor to `generate`. (Ivan Enderlin, 2017-03-03T08:21:34+01:00)
  * Integration: Allow `Documentation` in namespace. (Ivan Enderlin, 2017-02-28T21:29:54+01:00)
  * Bin: Generate test suites from API documentations. (Ivan Enderlin, 2017-02-28T21:22:39+01:00)
  * Asserter: Add the `assert` asserter. (Ivan Enderlin, 2017-02-28T21:20:38+01:00)
  * Integration: Add the `do` asserter. (Ivan Enderlin, 2017-02-28T11:32:37+01:00)
  * Unit: Enable assertions/expectations. (Ivan Enderlin, 2017-02-28T11:32:15+01:00)

# 2.17.02.27

  * Test: Restrict code coverage to current lib. (Ivan Enderlin, 2017-02-24T19:40:30+01:00)

# 2.17.02.24

  * Dependency: atoum/praspel-ext. 0.17.* released. (Ivan Enderlin, 2017-02-24T14:57:38+01:00)
  * Replace reserved keyword "Void" to "Nil" (Alexis von Glasow, 2017-02-24T14:48:41+01:00)
  * Dependency: Move to atoum 3.0. (Ivan Enderlin, 2017-02-24T14:46:03+01:00)
  * Bin: Simplify path to atoum. (Ivan Enderlin, 2017-02-14T09:49:40+01:00)

# 2.17.01.16

  * Quality: Happy new year! (Alexis von Glasow, 2017-01-16T08:47:57+01:00)
  * Dependency: Back to atoum 2.8. (Ivan Enderlin, 2017-01-16T08:46:58+01:00)
  * Quality: Add the `.gitignore` file. (Ivan Enderlin, 2016-10-25T11:40:23+02:00)

# 2.16.10.25

  * Report: Add Travis and Coveralls.io support. (Ivan Enderlin, 2016-10-25T11:34:43+02:00)
  * Documentation: New `README.md` file. (Ivan Enderlin, 2016-10-18T16:42:16+02:00)
  * Documentation: Update `support` properties. (Ivan Enderlin, 2016-10-11T08:40:01+02:00)
  * Documentation: Use TLS for `central.hoa`. (Ivan Enderlin, 2016-09-09T15:02:31+02:00)

# 2.16.08.17

  * Bin: Do not set `HOA_PRELUDE_FILES` if empty. (Ivan Enderlin, 2016-08-16T10:36:46+02:00)

# 2.16.08.16

  * Bin: Wait atoum children to finish before exiting. (Ivan Enderlin, 2016-08-15T15:53:43+02:00)

# 2.16.08.15

  * Bin: Exit code comes from atoum. (Ivan Enderlin, 2016-08-15T15:44:21+02:00)
  * Bin: Run test suites of another library. (Ivan Enderlin, 2016-07-05T23:49:08+02:00)
  * Dependency: Require `atoum/atoum` 2.8. (Ivan Enderlin, 2016-07-05T22:22:53+02:00)

# 2.16.06.20

  * Suite: Introduce the integration test suite. (Ivan Enderlin, 2016-06-20T09:36:55+02:00)
  * Suite: Optimize default namespace. (Ivan Enderlin, 2016-06-20T09:24:05+02:00)
  * Suite: Fix test case prefix. (Ivan Enderlin, 2016-06-20T09:22:33+02:00)

# 2.16.03.03

  * Report: Memory unit is now kilo-bytes. (Ivan Enderlin, 2016-03-02T11:19:16+01:00)
  * Report: Enhance fields. (Ivan Enderlin, 2016-03-02T11:07:45+01:00)
  * Report: New colors. (Ivan Enderlin, 2016-03-02T10:24:22+01:00)
  * Quality: Fix CS. (Ivan Enderlin, 2016-03-02T10:04:47+01:00)
  * Composer: Fix `hoa/console` version. (Ivan Enderlin, 2016-03-02T09:33:28+01:00)
  * Report: Use `hoa/console` colorizer. (jubianchi, 2016-02-24T14:57:58+01:00)
  * Report: Added atoum version & path in CLI report. (jubianchi, 2015-10-25T10:39:43+01:00)
  * Report: Add custom atoum report. (jubianchi, 2015-09-23T11:40:35+02:00)

# 2.16.01.19

  * atoum: Use the `visibility` extension. (Ivan Enderlin, 2016-01-19T12:44:10+01:00)
  * Composer: Use `atoum/ruler-extension` new release. (Ivan Enderlin, 2016-01-19T12:38:52+01:00)
  * CLI: Make `--directories` optional. (Ivan Enderlin, 2016-01-18T09:19:37+01:00)
  * Mocker: Remove our constant mocker implementation. (Ivan Enderlin, 2016-01-18T08:07:35+01:00)
  * Document: Fix a typo. (Ivan Enderlin, 2016-01-16T06:33:12+01:00)

# 2.16.01.14

  * Composer: New stable release. (Ivan Enderlin, 2016-01-14T22:19:00+01:00)
  * Autoload: Remove `Hoa\Core`. (Ivan Enderlin, 2016-01-11T11:27:37+01:00)

# 2.16.01.11

  * Quality: Drop PHP5.4. (Ivan Enderlin, 2016-01-11T09:15:27+01:00)
  * Quality: Run devtools:cs. (Ivan Enderlin, 2016-01-09T09:10:25+01:00)
  * Core: Remove `Hoa\Core`. (Ivan Enderlin, 2016-01-09T08:25:59+01:00)
  * Protocol: Use `Hoa\Protocol`. (Ivan Enderlin, 2015-12-13T23:03:31+01:00)
  * Atoum: Remove consistency err. & excep. handlers. (Ivan Enderlin, 2015-12-10T10:26:34+01:00)
  * Consistency: Use `Hoa\Consistency`. (Ivan Enderlin, 2015-12-09T07:03:21+01:00)
  * Exception: Use `Hoa\Exception`. (Ivan Enderlin, 2015-12-09T07:03:08+01:00)
  * Consistency: Update `getPHPBinary` and `uuid`. (Ivan Enderlin, 2015-12-08T23:47:48+01:00)
  * Consistency: Use `Hoa\Consistency`. (Ivan Enderlin, 2015-12-08T22:04:18+01:00)
  * Event: Use `Hoa\Event`. (Ivan Enderlin, 2015-11-23T22:19:34+01:00)
  * Mock: Add ability to mock constant. (Ivan Enderlin, 2015-11-10T09:33:02+01:00)

# 1.15.10.29

  * Documentation: Update VFS and `type` query string. (Ivan Enderlin, 2015-10-29T22:44:36+01:00)
  * VFS: Force `type=file`. If absent, no resolution. (Ivan Enderlin, 2015-10-29T22:29:27+01:00)
  * Documentation: Fix typos, markup, format… (Ivan Enderlin, 2015-10-27T14:48:12+01:00)
  * Documentation: Fix a typo. (Simon Mönch, 2015-10-21T18:00:59+02:00)

# 1.15.10.21

  * Documentation: Fix English one. (Raphaël Emourgeon, 2015-09-29T23:51:46+02:00)
  * Documentation: Format. (Ivan Enderlin, 2015-09-10T08:15:22+02:00)
  * Documentation: Minor English fixes. (Raphaël Emourgeon, 2015-09-09T22:15:34+02:00)
  * Force `atoum` to run in a specific CWD. (Ivan Enderlin, 2015-09-01T12:03:27+02:00)
  * Force timezone. (Ivan Enderlin, 2015-09-01T12:03:18+02:00)
  * Documentation: Fix schema loading error. (Ivan Enderlin, 2015-09-04T08:26:02+02:00)
  * Documentation: Translate to English. (Ivan Enderlin, 2015-08-12T08:02:15+02:00)

# 1.15.07.30

  * Require `hoa/devtools`. (Ivan Enderlin, 2015-07-23T08:57:11+02:00)
  * Require `hoa/ustring`. (Ivan Enderlin, 2015-07-23T08:47:45+02:00)
  * Require `hoa/cli`. (Ivan Enderlin, 2015-07-23T08:47:36+02:00)
  * Move to PSR-1 and PSR-2. (Ivan Enderlin, 2015-07-06T13:12:33+02:00)
  * Use `Hoa\Ustring` instead of `Hoa\String`. (Ivan Enderlin, 2015-07-06T13:12:09+02:00)
  * Fix CS. (Ivan Enderlin, 2015-06-27T16:06:54+02:00)
  * Fix usage wording. (Ivan Enderlin, 2015-06-25T16:43:43+02:00)
  * Add `--php-binary` & `--concurrent-processes`. (Ivan Enderlin, 2015-06-16T14:33:03+02:00)

# 1.15.06.25

  * Fix ruler-extension namespace. (Metalaka, 2015-06-22T20:17:36+02:00)
  * Skip library whose name is a PHP keyword. (Ivan Enderlin, 2015-06-01T10:12:40+02:00)

# 1.15.05.29

  * Update atoum/atoum. (Ivan Enderlin, 2015-05-29T14:53:57+02:00)
  * Move to PSR-1 and PSR-2. (Ivan Enderlin, 2015-05-21T09:34:33+02:00)

# 1.15.04.16

  * Add `atoum/ruler-extension`. (Ivan Enderlin, 2015-02-02T12:03:36+01:00)
  * Add the `CHANGELOG.md` file. (Ivan Enderlin, 2015-02-26T08:26:18+01:00)
  * Use short-array syntax. (Ivan Enderlin, 2015-02-26T08:21:04+01:00)

# 1.15.02.20

  * Upgrade atoum version number. (Alexis von Glasow, 2015-02-20T14:28:36+01:00)
  * Happy new year! (Ivan Enderlin, 2015-01-05T14:54:05+01:00)

# 1.14.12.10

  * Finalize the move to PSR-4 with Composer. (Ivan Enderlin, 2014-12-10T09:32:04+01:00)
  * Move to PSR-4. (Ivan Enderlin, 2014-12-09T18:50:33+01:00)

# 1.14.12.01

  * Re-order requirements. (Ivan Enderlin, 2014-12-01T08:56:48+01:00)
  * Lock atoum on `~1.0` (no more fork). (Julien Bianchi, 2014-11-30T16:30:42+01:00)

# 1.14.11.26

  * Install `hoa/dispatcher` & `hoa/router` to use `hoa`. (Ivan Enderlin, 2014-11-26T10:47:47+01:00)

# 1.14.11.24

  * Finalizing `Hoa\Test`! (Ivan Enderlin, 2014-11-24T15:32:59+01:00)
  * Look for `atoum` in `vendor/bin/`. (Ivan Enderlin, 2014-11-24T10:49:46+01:00)
  * Format code. #mania (Ivan Enderlin, 2014-11-24T10:49:32+01:00)
  * Fork atoum because there is no tag. (Ivan Enderlin, 2014-11-21T15:47:32+01:00)

# 0.14.11.09

  * Auto-substream in `hoa://Test/Vfs/`. (Ivan Enderlin, 2014-10-10T00:36:09+02:00)
  * Fix stat values in `hoa://Test/Vfs/`. (Ivan Enderlin, 2014-10-10T00:01:02+02:00)
  * Support `?type=directory` on `hoa://Test/Vfs/`. (Ivan Enderlin, 2014-10-09T23:47:54+02:00)
  * Add queries on `hoa://Test/Vfs/`. (Ivan Enderlin, 2014-10-09T10:23:10+02:00)
  * Set default namespace in `atoum\phpMocker`. (Ivan Enderlin, 2014-10-06T15:20:59+02:00)
  * Fix method name. (Ivan Enderlin, 2014-10-06T15:20:50+02:00)
  * Automatically filter by namespaces when possible. (Ivan Enderlin, 2014-10-06T10:40:44+02:00)
  * `atoum/praspel-extension` has tags. (Ivan Enderlin, 2014-09-24T13:35:40+02:00)

# 0.14.09.24

  * Add the `-D`/`--debug` option. (Ivan Enderlin, 2014-09-23T20:05:05+02:00)

# 0.14.09.23

  * Add `branch-alias`. (Stéphane PY, 2014-09-23T12:00:48+02:00)

# 0.14.09.22

  * Create `hoa://Test/`. (Ivan Enderlin, 2014-09-17T23:25:05+02:00)
  * Decorrelate test suite name of classname. (Ivan Enderlin, 2014-09-17T22:51:46+02:00)
  * Remove `from`/`import`. (Ivan Enderlin, 2014-09-17T22:51:20+02:00)

# 0.14.09.17

  * Drop PHP5.3. (Ivan Enderlin, 2014-09-17T17:56:58+02:00)
  * Add the installation section. (Ivan Enderlin, 2014-09-17T17:56:46+02:00)

(first snapshot)
