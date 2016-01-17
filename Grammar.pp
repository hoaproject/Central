//
// Hoa
//
//
// @license
//
// New BSD License
//
// Copyright © 2007-2016, Hoa community. All rights reserved.
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
// Grammar \Hoa\Praspel\Grammar.
//
// Provide grammar for Praspel.
//
// @copyright  Copyright © 2007-2016 Hoa community.
// @license    New BSD License
//


%skip   s               \s
%skip   block_comment   /\*(.|\n)*?\*/
%skip   inline_comment  //[^\n]*

// Clauses.
%token  at_is           @is
%token  at_requires     @requires
%token  at_ensures      @ensures
%token  at_throwable    @throwable
%token  at_invariant    @invariant
%token  at_behavior     @behavior
%token  at_default      @default
%token  at_description  @description

// Constructions.
%token  old             \\old
%token  result          \\result
%token  pred            \\pred
%token  nothing         \\nothing

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
%token  with            with
%token  pure            pure
%token  default         …|default
%token  contains        contains
%token  is              is
%token  let             let

// Constants.
%token  null            null|void
%token  true            true
%token  false           false
%token  binary          [\+\-]?0b[01]+
%token  octal           [\+\-]?0[0-7]+
%token  hexa            [\+\-]?0[xX][0-9a-fA-F]+
%token  decimal         [\+\-]?(0|[1-9]\d*)(\.\d+)?([eE][\+\-]?\d+)?
%token  quote_          '                         -> string
%token  string:escaped  \\(['nrtvef\\b]|[0-7]{1,3}|[xX][0-9a-fA-F]{1,2})
%token  string:accepted \\
%token  string:string   [^'\\]+
%token  string:concat   '\s*\.\s*'
%token  string:_quote   '                         -> default
%token  regex           /.*?(?<!\\)/[imsxADSUXJu]*
%token  identifier      [a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*

#specification:
    method()*

method:
    (
        is()
      | requires()
      | ensures()
      | throwable()
      | invariant()
    )
    ::semicolon::+
  | ( behavior() ::semicolon::* )+ default()? ::semicolon::*
  | description() ::semicolon::*

#is:
    ::at_is:: <pure>

#requires:
    ::at_requires:: expression()?

#ensures:
    ::at_ensures:: expression()?

#throwable:
    ::at_throwable:: exceptional_expression()?

#invariant:
    ::at_invariant:: expression()?

behavior:
    ::at_behavior:: behavior_content()
    ( ::and:: behavior_content() )*

behavior_content:
    <identifier> ::brace_::
    (
        (
            requires()
          | ensures()
          | throwable()
        )
        ::semicolon::+
      | ( behavior() ::semicolon::* )+ default()? ::semicolon::*
      | description() ::semicolon::*
    )+
    ::_brace:: #behavior

#default:
    ::at_default:: ::brace_::
    (
        (
            ensures()
          | throwable()
        )
        ::semicolon::+
      | description() ::semicolon::*
    )+
    ::_brace::

#description:
    ::at_description:: string()

expression:
              ( declaration() | constraint() | domainof() | predicate() )
    ( ::and:: ( declaration() | constraint() | domainof() | predicate() ) )*
  | <nothing>

exceptional_expression:
    exception() ( ::or:: exception() )*

exception:
    exception_identifier() ( ::or:: exception_identifier() )*
    ( ::with:: exception_with() )?

#exception_identifier:
    classname() <identifier>

#exception_with:
    expression()

#declaration:
    ( ::let:: #local_declaration )?
    extended_identifier() ::colon:: disjunction()

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
    ( constant() | realdom() | extended_identifier() )
    ( ::or:: disjunction() #disjunction )*

disjunction_of_constants:
    constant() ( ::or:: disjunction_of_constants() #disjunction )*

#realdom:
    <identifier> ::parenthesis_::
    ( argument() ( ::comma:: argument() )* )?
    ::_parenthesis::

argument:
    <default> | realdom() | constant() | array() | extended_identifier()

constant:
    scalar() | array()

scalar:
    <null> | boolean() | number() | string() | regex() | class() | range()

boolean:
    <true> | <false>

number:
    <binary> | <octal> | <hexa> | <decimal>

string:
    quoted_string() | herestring()

quoted_string:
    ::quote_::
    ( <escaped> | <accepted> | <string> | ::concat:: #concatenation )
    ( ( <escaped> | <accepted> | <string> | ::concat:: ) #concatenation )*
    ::_quote::

#regex:
    <regex>

#class:
    classname()

#range:
    number() ::range:: number()
  | number() ::range:: #left_range
  | ::range:: number() #right_range

#array:
    ::bracket_::
    ( pair() ( ::comma:: pair() )* )?
    ::_bracket::

pair:
    ( ::from::? disjunction() ::to:: disjunction() #pair )
  | ::to::? disjunction()

extended_identifier:
    ( ::count:: #count )? arrayaccess()

arrayaccess:
    identifier()
    (
        ::bracket_:: scalar() ::_bracket:: #arrayaccessbykey
      | ::brace_::   scalar() ::_brace::   #arrayaccessbyvalue
    )?

identifier:
    ( <identifier> | <this> )
    ( ::arrow:: <identifier> ( ::arrow:: <identifier> )* #dynamic_resolution )?
  | (
        ::self::   #self_identifier
      | ::static:: #static_identifier
      | ::parent:: #parent_identifier
    )
    ( ::resolution:: <identifier> )+
  | ::old:: ::parenthesis_:: extended_identifier() ::_parenthesis:: #old
  | <result>

#classname:
    ::backslash:: <identifier> ( ::backslash:: <identifier> )*

herestring:
    ::heredoc_::
    (
        ::quote:: <identifier[0]> ::quote:: #nowdoc
      | <identifier[0]>                     #heredoc
    )
    <content>?
    ::identifier[0]:: ::_heredoc::
