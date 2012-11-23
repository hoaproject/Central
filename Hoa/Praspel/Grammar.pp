//
// Hoa
//
//
// @license
//
// New BSD License
//
// Copyright © 2007-2012, Ivan Enderlin. All rights reserved.
//
// Redistribution and use in source and binary forms, with or without
// modification, are permitted provided that the following conditions are met:
//     * Redistributions of source code must retain the above copyright
//       notice, this list of conditions and the following disclaimer.
//     * Redistributions in binary form must reproduce the above copyright
//       notice, this list of conditions and the following disclaimer in the
//       documentation and/or other materials provided with the distribution.
//     * Neither the name of the Hoa nor the names of its contributors may be
//       used to endorse or promote products derived from this software without
//       specific prior written permission.
//
// THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
// AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
// IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
// ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS AND CONTRIBUTORS BE
// LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
// CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
// SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
// INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
// CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
// ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
// POSSIBILITY OF SUCH DAMAGE.
//
// Grammar \Hoa\Test\Praspel\Grammar.
//
// Provide grammar for Praspel.
//
// @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
// @copyright  Copyright © 2007-2012 Ivan Enderlin.
// @license    New BSD License
//


%skip   s               \s
%skip   block_comment   /\*(.|\n)*?\*/
%skip   inline_comment  //[^\n]*

// Clauses.
%token  is              @is
%token  requires        @requires
%token  ensures         @ensures
%token  throwable       @throwable
%token  invariant       @invariant
%token  behavior        @behavior
%token  forexample      @forexample

// Constructions.
%token  old             \\old
%token  result          \\result
%token  pred            \\pred

// Symbols.
%token  parenthesis_    \(
%token _parenthesis     \)
%token  brace_          \{
%token _brace           \}
%token  bracket_        \[
%token _bracket         \]
%token  comma           ,
%token  backslash       \\
%token  arrow           \->
%token  resolution      ::
%token  colon           :
%token  semicolon       ;
%token  range           \.\.
%token  count           #|count
%token  heredoc_        <<<                       -> hd
%token  hd:quote        '
%token  hd:identifier   [A-Z]+
%token  hd:content      ((\h[^\n]+)?\n)+
%token  hd:_heredoc     ;                         -> default

// Keywords.
%token  domainof        domainof
%token  from            from
%token  to              to
%token  this            this
%token  self            self
%token  static          static
%token  parent          parent
%token  and             and
%token  or              or
%token  xor             xor
%token  with            with
%token  pure            pure
%token  default         …|default
%token  contains        contains
%token  is              is

// Constants.
%token  null            null|void
%token  true            true
%token  false           false
%token  binary          [+-]?0b[01]+
%token  octal           [+-]?0[0-7]+
%token  hexa            [+-]?0[xX][0-9a-fA-F]+
%token  decimal         [+-]?(0|[1-9]\d*)(\.\d+)?([eE][\+\-]?\d+)?
%token  quote_          '                         -> string
%token  string:escaped  \\(['nrtvef\\b]|[0-7]{1,3}|[xX][0-9a-fA-F]{1,2})
%token  string:string   [^'\\]+
%token  string:concat   '\s*\.\s*'
%token  string:_quote   '                         -> default
%token  regex           /.*?(?<!\\)/[imsxADSUXJu]*
%token  identifier      [a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*

#specification:
    class()+ | method()+

class:
    ::this::

method:
    (
        is()
      | requires()
      | ensures()
      | throwable()
      | invariant()
    )
    ::semicolon::+
  | ( behavior() | forexample() ) ::semicolon::*

#is:
    ::is:: <pure>

#requires:
    ::requires:: expression()?

#ensures:
    ::ensures:: expression()?

#throwable:
    ::throwable:: exceptional_expression()?

#invariant:
    ::invariant:: expression()?

behavior:
    ::behavior:: behavior_content()
    ( ::and:: behavior_content() )*

behavior_content:
    <identifier> ::brace_::
    (
        (
            requires()
          | ensures()
          | throwable()
          | invariant()
        )
        ::semicolon::
      | behavior() ::semicolon::?
    )+
    ::_brace:: #behavior

#forexample:
    ::forexample:: string()

expression:
              ( declaration() | constraint() | domainof() | predicate() )
    ( ::and:: ( declaration() | constraint() | domainof() | predicate() ) )*

exceptional_expression:
    exception() ( ::and:: exception() )*

exception:
    classname() ( ::or:: classname() )* #exception_list
  | classname() <identifier> ( ::or:: classname() <identifier> )*
    ::with:: declaration() #exception_with
//              NO

#declaration:
    extended_identifier() ::colon:: representation()

representation:
    disjunction()
// ( ::xor:: disjunction() #exclusive_disjunction )*

constraint:
    qualification() | contains()

#qualification:
    identifier() ::is:: <identifier> ( ::comma:: <identifier> )*

#contains:
    extended_identifier() ::contains:: disjunction_of_constants()

#domainof:
    identifier() ::domainof:: identifier()

#predicate:
    ::pred:: ::parenthesis_:: string()? ::_parenthesis::

disjunction:
    ( constant() | realdom() ) ( ::or:: disjunction() #disjunction )*

disjunction_of_constants:
    constant() ( ::or:: disjunction_of_constants() #disjunction )*

#realdom:
    <identifier> ::parenthesis_::
    ( argument() ( ::comma:: argument() )* )?
    ::_parenthesis::

argument:
    <default> | realdom() | constant() | array()

constant:
    scalar() | array()

scalar:
    <null> | boolean() | number() | string() | <regex> | range()

boolean:
    <true> | <false>

number:
    <binary> | <octal> | <hexa> | <decimal>

string:
    quoted_string() | herestring()

quoted_string:
    ::quote_::
    ( <escaped> | <string> | ::concat:: #concatenation )
    ( ( <escaped> | <string> | ::concat:: ) #concatenation )*
    ::_quote::

#array:
    ::bracket_::
    ( pair() ( ::comma:: pair() )* )?
    ::_bracket::

pair:
    ( ::from::? representation() ::to:: representation() #pair )
  | ::to::? representation()

#range:
    number() ::range:: number()
  | number() ::range:: #left_range
  | ::range:: number() #right_range

extended_identifier:
    ( ::count:: #count )? arrayaccess()

arrayaccess:
    identifier()
    (
        ::bracket_:: scalar() ::_bracket:: #arrayaccessbykey
      | ::brace_::   scalar() ::_brace::   #arrayaccessbyvalue
    )?

identifier:
    <identifier>
  | ::this:: ::arrow:: <identifier> ( ::arrow:: <identifier> )* #this_identifier
  | (
        ::self::   #self_identifier
      | ::static:: #static_identifier
      | ::parent:: #parent_identifier
    )
    ::resolution:: <identifier> ( ::resolution:: <identifier> )*
  | ::old:: ::parenthesis_:: extended_identifier() ::_parenthesis:: #old
  | <result>

#classname:
    ::backslash::? <identifier> ( ::backslash:: <identifier> )*

herestring:
    ::heredoc_::
    (
        ::quote:: <identifier[0]> ::quote:: #nowdoc
      | <identifier[0]>                     #heredoc
    )
    <content>?
    ::identifier[0]:: ::_heredoc::
