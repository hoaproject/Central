# 0.17.01.13

  * Quality: Happy new year! (Alexis von Glasow, 2017-01-11T23:21:11+01:00)
  * Documentation: New `README.md` file. (Ivan Enderlin, 2016-10-18T16:47:53+02:00)
  * Documentation: Update `support` properties. (Ivan Enderlin, 2016-10-11T17:06:52+02:00)

# 0.16.05.24

  * Composer: Fix dependencies. (Ivan Enderlin, 2016-05-24T10:06:31+02:00)
  * SMTP: Support SMTPS. (Ivan Enderlin, 2016-01-27T10:31:26+01:00)

# 0.16.01.11

  * Quality: Drop PHP5.4. (Ivan Enderlin, 2016-01-11T09:15:26+01:00)
  * Quality: Run devtools:cs. (Ivan Enderlin, 2016-01-09T09:04:44+01:00)
  * Core: Remove `Hoa\Core`. (Ivan Enderlin, 2016-01-09T08:19:02+01:00)
  * Consistency: Use `Hoa\Consistency`. (Ivan Enderlin, 2015-12-08T11:19:54+01:00)
  * Exception: Use `Hoa\Exception`. (Ivan Enderlin, 2015-11-20T07:59:28+01:00)

# 0.15.10.29

  * Test: Specify file type with `hoa://Test/Vfs`. (Ivan Enderlin, 2015-10-29T22:24:14+01:00)
  * Format API documentation. (Ivan Enderlin, 2015-08-28T16:03:14+02:00)

# 0.15.08.28

  * Fix CS. (Ivan Enderlin, 2015-08-28T10:41:59+02:00)
  * Format API documentation. (Ivan Enderlin, 2015-08-28T09:51:09+02:00)
  * Add reference to RFC2821. (Ivan Enderlin, 2015-08-28T09:44:05+02:00)
  * Better line reading and timeout support for SMTP. (Ivan Enderlin, 2015-08-28T09:39:10+02:00)
  * Complete the API documentation. (Ivan Enderlin, 2015-08-28T09:34:43+02:00)
  * Force TCP connection to be in blocking mode for SMTP. (Ivan Enderlin, 2015-08-28T09:44:59+02:00)

# 0.15.08.17

  * Add a `.gitignore` file. (Stéphane HULARD, 2015-08-03T11:36:08+02:00)
  * Avoid sending a second `EHLO` if no `STARTTLS`. (Ivan Enderlin, 2015-06-11T09:35:23+02:00)
  * Update an exception message. (Ivan Enderlin, 2015-06-11T09:35:07+02:00)
  * Reference RFC2487 and RFC3207 in `composer.json`. (Ivan Enderlin, 2015-06-11T09:17:28+02:00)
  * s/authentification/authentication/. (Ivan Enderlin, 2015-06-11T09:07:45+02:00)

# 0.15.06.03

  * Content-ID must be surrounded by `<` and `>`. (Ivan Enderlin, 2015-05-27T15:05:51+02:00)
  * Add quotes around boundary definition. (Ivan Enderlin, 2015-05-27T14:56:37+02:00)
  * If attachment' size is unknown, remove it. (Ivan Enderlin, 2015-05-27T11:14:05+02:00)
  * List all implemented RFC. (Ivan Enderlin, 2015-05-26T18:00:29+02:00)
  * Now we can use encoders in `formatHeaders`. (Ivan Enderlin, 2015-05-26T17:56:17+02:00)
  * Add exception numbers. (Ivan Enderlin, 2015-05-26T17:56:11+02:00)
  * Implement RFC2047 on encoders. (Ivan Enderlin, 2015-05-26T17:42:26+02:00)
  * Text's content encoding is `quoted-printable`. (Ivan Enderlin, 2015-05-26T17:21:04+02:00)
  * Start implementing the `QuotedPrintable` encoder. (Ivan Enderlin, 2015-05-26T16:58:56+02:00)
  * Rename test cases. (Ivan Enderlin, 2015-05-26T16:58:25+02:00)
  * Mention RFC20245 Section 6.8 too. (Ivan Enderlin, 2015-05-26T15:31:43+02:00)
  * Use the Base64 encoder. (Ivan Enderlin, 2015-05-26T15:22:24+02:00)
  * Add the Base64 encoder. (Ivan Enderlin, 2015-05-26T15:21:51+02:00)
  * Fix CS. (Ivan Enderlin, 2015-05-26T12:31:44+02:00)
  * Add related contents example. (Ivan Enderlin, 2015-05-26T12:21:01+02:00)
  * Attachments set their own size. (Ivan Enderlin, 2015-05-26T12:04:27+02:00)
  * Add related contents. (Ivan Enderlin, 2015-05-26T12:03:16+02:00)
  * Add the `getId` and `getIdUrl` methods. (Ivan Enderlin, 2015-05-26T12:05:06+02:00)
  * Force quotes around `filename` in an attachment. (Ivan Enderlin, 2015-05-26T11:21:28+02:00)
  * More tests about the content top-class. (Ivan Enderlin, 2015-05-26T10:50:42+02:00)
  * Test the main content class. (Ivan Enderlin, 2015-05-26T10:08:31+02:00)
  * Rename the verdict variable to `$result`. (Ivan Enderlin, 2015-05-26T09:21:56+02:00)
  * Test alternative content. (Ivan Enderlin, 2015-05-26T09:21:44+02:00)
  * More tests for the message content. (Ivan Enderlin, 2015-05-26T09:15:18+02:00)
  * Remove an always-true condition. (Ivan Enderlin, 2015-05-26T09:14:47+02:00)
  * Test the message content. (Ivan Enderlin, 2015-05-25T21:59:56+02:00)
  * `getRecipients` extracts address from `to` header. (Ivan Enderlin, 2015-05-25T21:58:15+02:00)
  * `date` header is defined in the constructor. (Ivan Enderlin, 2015-05-25T21:57:07+02:00)
  * Test attachment content. (Ivan Enderlin, 2015-05-25T21:25:47+02:00)
  * Catch all kind of `Hoa\Mime` exceptions. (Ivan Enderlin, 2015-05-25T21:19:26+02:00)
  * Fix CS. (Ivan Enderlin, 2015-05-25T21:07:31+02:00)
  * Test HTML content. (Ivan Enderlin, 2015-05-25T20:57:58+02:00)
  * Try to extract encoder into `Encoder\*` classes. (Ivan Enderlin, 2015-05-25T20:57:36+02:00)
  * Test the text content. (Ivan Enderlin, 2015-05-25T11:35:43+02:00)
  * Add RFC2047 as a reference. (Ivan Enderlin, 2015-05-25T11:37:24+02:00)
  * Encode text as non-ASCII instead of base64. (Ivan Enderlin, 2015-05-25T11:36:07+02:00)

# 0.15.05.20

  * Force MIME type with the constructor. (Ivan Enderlin, 2015-05-20T15:04:16+02:00)
  * Move to PSR-1 and PSR-2. (Ivan Enderlin, 2015-05-18T09:01:31+02:00)

# 0.15.02.23

  * Add the `CHANGELOG.md` file. (Ivan Enderlin, 2015-02-23T09:20:29+01:00)
  * Remove `from`/`import` and update to PHP5.4. (Ivan Enderlin, 2015-01-23T22:53:19+01:00)
  * Happy new year! (Ivan Enderlin, 2015-01-05T14:40:14+01:00)

# 0.14.12.10

  * Move to PSR-4. (Ivan Enderlin, 2014-12-09T13:56:48+01:00)

# 0.14.09.23

  * Add `branch-alias`. (Stéphane PY, 2014-09-23T12:01:03+02:00)

# 0.14.09.17

  * Drop PHP5.3. (Ivan Enderlin, 2014-09-17T18:04:43+02:00)
  * Add the installation section. (Ivan Enderlin, 2014-09-17T18:04:29+02:00)

(first snapshot)
