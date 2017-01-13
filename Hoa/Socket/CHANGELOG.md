# 1.17.01.13

  * Quality: Happy new year! (Alexis von Glasow, 2017-01-12T14:00:54+01:00)
  * Documentation: New `README.md` file. (Ivan Enderlin, 2016-10-18T15:48:29+02:00)
  * Documentation: Update `support` properties. (Ivan Enderlin, 2016-10-11T11:50:47+02:00)

# 1.16.07.07

  * Quality: Fix CS. (Ivan Enderlin, 2016-07-07T22:56:21+02:00)
  * Test: Be more flexible about default transports. (Ivan Enderlin, 2016-07-06T09:22:29+02:00)
  * Client, Server: Add `ENCRYPTION_TLS*` constants. (Stéphane HULARD, 2016-02-19T15:50:42+01:00)

# 1.16.02.17

  * Test: Ensure broken pipes are correcty handled. (Ivan Enderlin, 2016-02-17T09:50:04+01:00)
  * Test: Ensure `…ection::write` throws `BrokenPipe`. (Ivan Enderlin, 2016-02-17T08:57:55+01:00)
  * Test: Write test suite of `…Exception\BrokenPipe`. (Ivan Enderlin, 2016-02-17T08:55:36+01:00)
  * Documentation: Complete API documentation. (Ivan Enderlin, 2016-02-17T08:49:51+01:00)
  * Handler: Catch broken pipe earlier. (Ivan Enderlin, 2016-02-17T08:46:55+01:00)
  * Quality: Fix CS. (Ivan Enderlin, 2016-02-17T08:46:48+01:00)
  * Handler: Detect and handle broken pipes while sending or broadcasting messages. (Metalaka, 2016-02-15T20:50:13+01:00)

# 1.16.02.10

  * Test: Write test suite of `Hoa\Socket\Server`. (Ivan Enderlin, 2016-02-08T21:13:22+01:00)
  * Server: Add protected getters. (Ivan Enderlin, 2016-02-08T21:13:16+01:00)
  * Server: Fix auto-LISTEN flag for TCP. (Ivan Enderlin, 2016-02-08T08:07:07+01:00)
  * Server: `isBinding` & `isListening` return bools. (Ivan Enderlin, 2016-02-08T08:06:02+01:00)
  * Connection: Add the `getIteratorValues` method. (Ivan Enderlin, 2016-02-05T08:25:29+01:00)
  * Test: Write test suite of `Hoa\Socket\Client`. (Ivan Enderlin, 2016-02-03T08:14:54+01:00)
  * Client: Add the protected `getStack` method. (Ivan Enderlin, 2016-02-03T08:14:19+01:00)
  * Client: Strictly check connection error number. (Ivan Enderlin, 2016-02-03T08:12:42+01:00)
  * Client: Fix auto-CONNECT flag. (Ivan Enderlin, 2016-02-03T08:12:31+01:00)
  * Quality: Fix CS and simplify a class identifier. (Ivan Enderlin, 2016-02-01T10:58:04+01:00)
  * Test: Write test suite of `…onnection\Connection`. (Ivan Enderlin, 2016-01-26T08:49:17+01:00)
  * Handler: Remove dead code. (Ivan Enderlin, 2016-01-26T08:16:46+01:00)
  * Test: Use the `atoum/visibility-extension`. (Ivan Enderlin, 2016-01-22T09:18:02+01:00)
  * Test: Write test suite of `…t\Connection\Handler`. (Ivan Enderlin, 2016-01-18T18:02:37+01:00)
  * Test: Reverse a `while` to a `do`/`while`. (Ivan Enderlin, 2016-01-18T08:24:49+01:00)
  * Quality: Fix CS. (Ivan Enderlin, 2016-01-17T17:57:29+01:00)
  * Handler: Add the `getMergedConnections` method. (Ivan Enderlin, 2016-01-17T17:56:54+01:00)
  * Test: Write test suite of `…ket\Connection\Group`. (Ivan Enderlin, 2016-01-17T15:05:06+01:00)
  * Quality: Run `devtools:cs`. (Ivan Enderlin, 2016-01-17T14:26:27+01:00)
  * Test: Write test suite of `Hoa\Socket\Node`. (Ivan Enderlin, 2016-01-15T16:51:45+01:00)
  * Composer: Update `hoa/test` dependency. (Ivan Enderlin, 2016-01-15T08:43:57+01:00)
  * Test: Write test suite of `Hoa\Socket\Exception`. (Ivan Enderlin, 2016-01-15T08:40:17+01:00)
  * Test: Write test suite of `Hoa\Socket\Socket`. (Ivan Enderlin, 2016-01-15T08:35:33+01:00)
  * Socket: Port is always an integer. (Ivan Enderlin, 2016-01-15T08:35:23+01:00)

# 1.16.01.15

  * Composer: New stable library. (Ivan Enderlin, 2016-01-14T22:14:42+01:00)

# 1.16.01.14

  * Quality: Drop PHP5.4. (Ivan Enderlin, 2016-01-11T09:15:26+01:00)
  * Quality: Run devtools:cs. (Ivan Enderlin, 2016-01-09T09:09:13+01:00)
  * Core: Remove `Hoa\Core`. (Ivan Enderlin, 2016-01-09T08:24:58+01:00)
  * Consistency: Update `dnew` calls. (Ivan Enderlin, 2015-12-09T16:48:36+01:00)
  * Consistency: Use `Hoa\Consistency`. (Ivan Enderlin, 2015-12-08T21:53:44+01:00)
  * Exception: Use `Hoa\Exception`. (Ivan Enderlin, 2015-11-20T13:15:06+01:00)
  * Connection: Fix an exception message variable. (Ivan Enderlin, 2015-12-12T18:38:04+01:00)
  * Fix CS. (Ivan Enderlin, 2015-12-06T23:29:26+01:00)
  * Socket: Add a security flag. (Stéphane HULARD, 2015-07-23T00:22:53+02:00)
  * Test: Write test suite of `Hoa\Socket\Transport`. (Ivan Enderlin, 2015-07-27T15:55:08+02:00)
  * Transport: Introduce vendor schemes. (Stéphane HULARD, 2015-07-23T00:22:14+02:00)
  * Fix phpDoc. (Metalaka, 2015-11-01T21:24:37+01:00)

# 0.15.09.08

  * Introduce read flags (PEEK and OOB). (Ivan Enderlin, 2015-08-28T09:52:51+02:00)
  * Add `.gitignore` file. (Stéphane HULARD, 2015-08-03T10:04:02+02:00)
  * Fix CS. (Ivan Enderlin, 2015-07-22T14:38:42+02:00)

# 0.15.07.20

  * Reintroduce flags and reverse when connecting a client. (Ivan Enderlin, 2015-07-20T18:43:09+02:00)
  * Fix an exception message. (Ivan Enderlin, 2015-06-01T15:15:37+02:00)

# 0.15.05.29

  * Move to PSR-1 and PSR-2. (Ivan Enderlin, 2015-05-20T09:48:38+02:00)
  * Remove `from`/`import` and drop PHP5.3. (Ivan Enderlin, 2015-03-24T10:13:43+01:00)

# 0.15.02.25

  * Add the `CHANGELOG.md` file. (Ivan Enderlin, 2015-02-25T09:39:09+01:00)
  * Happy new year! (Ivan Enderlin, 2015-01-05T14:50:55+01:00)

# 0.14.12.10

  * Move to PSR-4. (Ivan Enderlin, 2014-12-09T18:47:18+01:00)

# 0.14.12.08

  * Socket states connected after construct. (Stéphane PY, 2014-12-08T15:33:53+01:00)

# 0.14.09.23

  * Add `branch-alias`. (Stéphane PY, 2014-09-23T11:56:22+02:00)

# 0.14.09.17

  * Drop PHP5.3. (Ivan Enderlin, 2014-09-17T17:40:06+02:00)
  * Add the installation section. (Ivan Enderlin, 2014-09-17T17:39:51+02:00)

(first snapshot)
