<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2017, Hoa community. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Hoa nor the names of its contributors may be
 *       used to endorse or promote products derived from this software without
 *       specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS`.
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS AND CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

namespace Hoa\Irc;

/**
 * Class \Hoa\Irc\Codes.
 *
 * This class contains all well-known IRC codes, based on RFC1459, RFC2810,
 * RFC2811, RFC2812, RFC2813, RFC3675.
 *
 * Source: http://www.networksorcery.com/enp/protocol/irc.htm.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Codes
{
    /*
     * Replies in the range from 001 to 099 are used for client-server
     * connections only and should never travel between servers.
     */

    /**
     * `Welcome to the Internet Relay Network <nick>!<user>@<host>`.
     *
     * @const int
     */
    const RPL_WELCOME           = 1;

    /**
     * `Your host is <servername>, running version <ver>`.
     *
     * @const int
     */
    const RPL_YOURHOST          = 2;

    /**
     * `This server was created <date>`.
     *
     * @const int
     */
    const RPL_CREATED           = 3;

    /**
     * `<servername> <version> <available user modes> <available channel modes>`.
     *
     * @const int
     */
    const RPL_MYINFO            = 4;

    /**
     * `Try server <server name>, port <port number>`.
     *
     * @const int
     */
    const RPL_BOUNCE            = 5;

    /*
     * Replies generated in the response to commands are found in the range
     * from 200 to 399.
     */

    /**
     * `Link <version & debug level> <destination> <next server> V<protocol version> <link uptime in seconds> <backstream sendq> <upstream sendq>`.
     *
     * @const int
     */
    const RPL_TRACELINK         = 200;

    /**
     * `Try. <class> <server>`.
     *
     * @const int
     */
    const RPL_TRACECONNECTING   = 201;

    /**
     * `H.S. <class> <server>`.
     *
     * @const int
     */
    const RPL_TRACEHANDSHAKE    = 202;

    /**
     * `???? <class> [<client IP address in dot form>]`.
     *
     * @const int
     */
    const RPL_TRACEUNKNOWN      = 203;

    /**
     * `Oper <class> <nick>`.
     *
     * @const int
     */
    const RPL_TRACEOPERATOR     = 204;

    /**
     * `User <class> <nick>`.
     *
     * @const int
     */
    const RPL_TRACEUSER         = 205;

    /**
     * `Serv <class> <int>S <int>C <server> <nick!user|*!*>@<host|server> V<protocol version>`.
     *
     * @const int
     */
    const RPL_TRACESERVER       = 206;

    /**
     * `Service <class> <name> <type> <active type>`.
     *
     * @const int
     */
    const RPL_TRACESERVICE      = 207;

    /**
     * `<newtype> 0 <client name>`.
     *
     * @const int
     */
    const RPL_TRACENEWTYPE      = 208;

    /**
     * `Class <class> <count>`.
     *
     * @const int
     */
    const RPL_TRACECLASS        = 209;

    /**
     * Unused.
     *
     * @const int
     */
    const RPL_TRACERECONNECT    = 210;

    /**
     * `<linkname> <sendq> <sent messages> <sent Kbytes> <received messages> <received Kbytes> <time open>`.
     *
     * @const int
     */
    const RPL_STATSLINKINFO     = 211;

    /**
     * `<command> <count> <byte count> <remote count>`.
     *
     * @const int
     */
    const RPL_STATSCOMMANDS     = 212;

    /**
     * `<stats letter> :End of STATS report`.
     *
     * @const int
     */
    const RPL_ENDOFSTATS        = 219;

    /**
     * `<user mode string>`.
     *
     * @const int
     */
    const RPL_UMODEIS           = 221;

    /**
     * `<name> <server> <mask> <type> <hopcount> <info>`.
     *
     * @const int
     */
    const RPL_SERVLIST          = 234;

    /**
     * `<mask> <type> :End of service listing`.
     *
     * @const int
     */
    const RPL_SERVLISTEND       = 235;

    /**
     * `:Server Up %d days %d:%02d:%02d`.
     *
     * @const int
     */
    const RPL_STATSUPTIME       = 242;

    /**
     * `O <hostmask> * <name>`.
     *
     * @const int
     */
    const RPL_STATSOLINE        = 243;

    /**
     * `:There are <integer> users and <integer> services on <integer> servers`.
     *
     * @const int
     */
    const RPL_LUSERCLIENT       = 251;

    /**
     * `<integer> :operator(s) online`.
     *
     * @const int
     */
    const RPL_LUSEROP           = 252;

    /**
     * `<integer> :unknown connection(s)`.
     *
     * @const int
     */
    const RPL_LUSERUNKNOWN      = 253;

    /**
     * `<integer> :channels formed`.
     *
     * @const int
     */
    const RPL_LUSERCHANNELS     = 254;

    /**
     * `:I have <integer> clients and <integer> servers`.
     *
     * @const int
     */
    const RPL_LUSERME           = 255;

    /**
     * `<server> :Administrative info`.
     *
     * @const int
     */
    const RPL_ADMINME           = 256;

    /**
     * `:<admin info>`.
     *
     * @const int
     */
    const RPL_ADMINLOC1         = 257;

    /**
     * `:<admin info>`.
     *
     * @const int
     */
    const RPL_ADMINLOC2         = 258;

    /**
     * `:<admin info>`.
     *
     * @const int
     */
    const RPL_ADMINEMAIL        = 259;

    /**
     * `File <logfile> <debug level>`.
     *
     * @const int
     */
    const RPL_TRACELOG          = 261;

    /**
     * `<server name> <version & debug level> :End of TRACE`.
     *
     * @const int
     */
    const RPL_TRACEEND          = 262;

    /**
     * `<command> :Please wait a while and try again.`.
     *
     * @const int
     */
    const RPL_TRYAGAIN          = 263;

    /**
     * `<nick> :<away message>`.
     *
     * @const int
     */
    const RPL_AWAY              = 301;

    /**
     * `:*1<reply> *( " " <reply> )`.
     *
     * @const int
     */
    const RPL_USERHOST          = 302;

    /**
     * `:*1<nick> *( " " <nick> )`.
     *
     * @const int
     */
    const RPL_ISON              = 303;

    /**
     * `:You are no longer marked as being away`.
     *
     * @const int
     */
    const RPL_UNAWAY            = 305;

    /**
     * `:You have been marked as being away`.
     *
     * @const int
     */
    const RPL_NOWAWAY           = 306;

    /**
     * `<nick> <user> <host> * :<real name>`.
     *
     * @const int
     */
    const RPL_WHOISUSER         = 311;

    /**
     * `<nick> <server> :<server info>`.
     *
     * @const int
     */
    const RPL_WHOISSERVER       = 312;

    /**
     * `<nick> :is an IRC operator`.
     *
     * @const int
     */
    const RPL_WHOISOPERATOR     = 313;

    /**
     * `<nick> <user> <host> * :<real name>`.
     *
     * @const int
     */
    const RPL_WHOWASUSER        = 314;

    /**
     * `<name> :End of WHO list`.
     *
     * @const int
     */
    const RPL_ENDOFWHO          = 315;

    /**
     * `<nick> <integer> :seconds idle`.
     *
     * @const int
     */
    const RPL_WHOISIDLE         = 317;

    /**
     * `<nick> :End of WHOIS list`.
     *
     * @const int
     */
    const RPL_ENDOFWHOIS        = 318;

    /**
     * `<nick> :*( ( "@" / "+" ) <channel> " " )`.
     *
     * @const int
     */
    const RPL_WHOISCHANNELS     = 319;

    /**
     * Obsolete.
     *
     * @const int
     */
    const RPL_LISTSTART         = 321;

    /**
     * `<channel> <# visible> :<topic>`.
     *
     * @const int
     */
    const RPL_LIST              = 322;

    /**
     * `:End of LIST`.
     *
     * @const int
     */
    const RPL_LISTEND           = 323;

    /**
     * `<channel> <mode> <mode params>`.
     *
     * @const int
     */
    const RPL_CHANNELMODEIS     = 324;

    /**
     * `<channel> <nickname>`.
     *
     * @const int
     */
    const RPL_UNIQOPIS          = 325;

    /**
     * `<channel> :No topic is set`.
     *
     * @const int
     */
    const RPL_NOTOPIC           = 331;

    /**
     * `<channel> :<topic>`.
     *
     * @const int
     */
    const RPL_TOPIC             = 332;

    /**
     * `<channel> <nick>`.
     *
     * @const int
     */
    const RPL_INVITING          = 341;

    /**
     * `<user> :Summoning user to IRC`.
     *
     * @const int
     */
    const RPL_SUMMONING         = 342;

    /**
     * `<channel> <invitemask>`.
     *
     * @const int
     */
    const RPL_INVITELIST        = 346;

    /**
     * `<channel> :End of channel invite list`.
     *
     * @const int
     */
    const RPL_ENDOFINVITELIST   = 347;

    /**
     * `<channel> <exceptionmask>`.
     *
     * @const int
     */
    const RPL_EXCEPTLIST        = 348;

    /**
     * `<channel> :End of channel exception list`.
     *
     * @const int
     */
    const RPL_ENDOFEXCEPTLIST   = 349;

    /**
     * `<version>.<debuglevel> <server> :<comments>`.
     *
     * @const int
     */
    const RPL_VERSION           = 351;

    /**
     * `<channel> <user> <host> <server> <nick> ( "H" / "G" > ["*"] [ ( "@" / "+" ) ] :<hopcount> <real name>`.
     *
     * @const int
     */
    const RPL_WHOREPLY          = 352;

    /**
     * `( "=" / "*" / "@" ) <channel> :[ "@" / "+" ] <nick> *( " " [ "@" / "+" ] <nick> )`.
     *
     * @const int
     */
    const RPL_NAMREPLY          = 353;

    /**
     * `<mask> <server> :<hopcount> <server info>`.
     *
     * @const int
     */
    const RPL_LINKS             = 364;

    /**
     * `<mask> :End of LINKS list`.
     *
     * @const int
     */
    const RPL_ENDOFLINKS        = 365;

    /**
     * `<channel> :End of NAMES list`.
     *
     * @const int
     */
    const RPL_ENDOFNAMES        = 366;

    /**
     * `<channel> <banmask>`.
     *
     * @const int
     */
    const RPL_BANLIST           = 367;

    /**
     * `<channel> :End of channel ban list`.
     *
     * @const int
     */
    const RPL_ENDOFBANLIST      = 368;

    /**
     * `<nick> :End of WHOWAS`.
     *
     * @const int
     */
    const RPL_ENDOFWHOWAS       = 369;

    /**
     * `:<string>`.
     *
     * @const int
     */
    const RPL_INFO              = 371;

    /**
     * `:- <text>`.
     *
     * @const int
     */
    const RPL_MOTD              = 372;

    /**
     * `:End of INFO list`.
     *
     * @const int
     */
    const RPL_ENDOFINFO         = 374;

    /**
     * `:- <server> Message of the day - `.
     *
     * @const int
     */
    const RPL_MOTDSTART         = 375;

    /**
     * `:End of MOTD command`.
     *
     * @const int
     */
    const RPL_ENDOFMOTD         = 376;

    /**
     * `:You are now an IRC operator`.
     *
     * @const int
     */
    const RPL_YOUREOPER         = 381;

    /**
     * `<config file> :Rehashing`.
     *
     * @const int
     */
    const RPL_REHASHING         = 382;

    /**
     * `You are service <servicename>`.
     *
     * @const int
     */
    const RPL_YOURESERVICE      = 383;

    /**
     * `<server> :<string showing server's local time>`.
     *
     * @const int
     */
    const RPL_TIME              = 391;

    /**
     * `:UserID Terminal Host`.
     *
     * @const int
     */
    const RPL_USERSSTART        = 392;

    /**
     * `:<username> <ttyline> <hostname>`.
     *
     * @const int
     */
    const RPL_USERS             = 393;

    /**
     * `:End of users`.
     *
     * @const int
     */
    const RPL_ENDOFUSERS        = 394;

    /**
     * `:Nobody logged in`.
     *
     * @const int
     */
    const RPL_NOUSERS           = 395;

    /*
     * Error replies are found in the range from 400 to 599.
     */

    /**
     * `<nickname> :No such nick/channel`.
     *
     * @const int
     */
    const ERR_NOSUCHNICK        = 401;

    /**
     * `<server name> :No such server`.
     *
     * @const int
     */
    const ERR_NOSUCHSERVER      = 402;

    /**
     * `<channel name> :No such channel`.
     *
     * @const int
     */
    const ERR_NOSUCHCHANNEL     = 403;

    /**
     * `<channel name> :Cannot send to channel`.
     *
     * @const int
     */
    const ERR_CANNOTSENDTOCHAN  = 404;

    /**
     * `<channel name> :You have joined too many channels`.
     *
     * @const int
     */
    const ERR_TOOMANYCHANNELS   = 405;

    /**
     * `<nickname> :There was no such nickname`.
     *
     * @const int
     */
    const ERR_WASNOSUCHNICK     = 406;

    /**
     * `<target> :<error code> recipients. <abort message>`.
     *
     * @const int
     */
    const ERR_TOOMANYTARGETS    = 407;

    /**
     * `<service name> :No such service`.
     *
     * @const int
     */
    const ERR_NOSUCHSERVICE     = 408;

    /**
     * `:No origin specified`.
     *
     * @const int
     */
    const ERR_NOORIGIN          = 409;

    /**
     * `:No recipient given (<command>)`.
     *
     * @const int
     */
    const ERR_NORECIPIENT       = 411;

    /**
     * `:No text to send`.
     *
     * @const int
     */
    const ERR_NOTEXTTOSEND      = 412;

    /**
     * `<mask> :No toplevel domain specified`.
     *
     * @const int
     */
    const ERR_NOTOPLEVEL        = 413;

    /**
     * `<mask> :Wildcard in toplevel domain`.
     *
     * @const int
     */
    const ERR_WILDTOPLEVEL      = 414;

    /**
     * `<mask> :Bad Server/host mask`.
     *
     * @const int
     */
    const ERR_BADMASK           = 415;

    /**
     * `<command> :Unknown command`.
     *
     * @const int
     */
    const ERR_UNKNOWNCOMMAND    = 421;

    /**
     * `:MOTD File is missing`.
     *
     * @const int
     */
    const ERR_NOMOTD            = 422;

    /**
     * `<server> :No administrative info available`.
     *
     * @const int
     */
    const ERR_NOADMININFO       = 423;

    /**
     * `:File error doing <file op> on <file>`.
     *
     * @const int
     */
    const ERR_FILEERROR         = 424;

    /**
     * `:No nickname given`.
     *
     * @const int
     */
    const ERR_NONICKNAMEGIVEN   = 431;

    /**
     * `<nick> :Erroneous nickname`.
     *
     * @const int
     */
    const ERR_ERRONEUSNICKNAME  = 432;

    /**
     * `<nick> :Nickname is already in use`.
     *
     * @const int
     */
    const ERR_NICKNAMEINUSE     = 433;

    /**
     * `<nick> :Nickname collision KILL from <user>@<host>`.
     *
     * @const int
     */
    const ERR_NICKCOLLISION     = 436;

    /**
     * `<nick/channel> :Nick/channel is temporarily unavailable`.
     *
     * @const int
     */
    const ERR_UNAVAILRESOURCE   = 437;

    /**
     * `<nick> <channel> :They aren't on that channel`.
     *
     * @const int
     */
    const ERR_USERNOTINCHANNEL  = 441;

    /**
     * `<channel> :You're not on that channel`.
     *
     * @const int
     */
    const ERR_NOTONCHANNEL      = 442;

    /**
     * `<user> <channel> :is already on channel`.
     *
     * @const int
     */
    const ERR_USERONCHANNEL     = 443;

    /**
     * `<user> :User not logged in`.
     *
     * @const int
     */
    const ERR_NOLOGIN           = 444;

    /**
     * `:SUMMON has been disabled`.
     *
     * @const int
     */
    const ERR_SUMMONDISABLED    = 445;

    /**
     * `:USERS has been disabled`.
     *
     * @const int
     */
    const ERR_USERSDISABLED     = 446;

    /**
     * `:You have not registered`.
     *
     * @const int
     */
    const ERR_NOTREGISTERED     = 451;

    /**
     * `<command> :Not enough parameters`.
     *
     * @const int
     */
    const ERR_NEEDMOREPARAMS    = 461;

    /**
     * `:Unauthorized command (already registered)`.
     *
     * @const int
     */
    const ERR_ALREADYREGISTRED  = 462;

    /**
     * `:Your host isn't among the privileged`.
     *
     * @const int
     */
    const ERR_NOPERMFORHOST     = 463;

    /**
     * `:Password incorrect`.
     *
     * @const int
     */
    const ERR_PASSWDMISMATCH    = 464;

    /**
     * `:You are banned from this server`.
     *
     * @const int
     */
    const ERR_YOUREBANNEDCREEP  = 465;

    /**
     *  ":You will be banned from this server`.
     *
     * @const int
     */
    const ERR_YOUWILLBEBANNED   = 466;

    /**
     * `<channel> :Channel key already set`.
     *
     * @const int
     */
    const ERR_KEYSET            = 467;

    /**
     * `<channel> :Cannot join channel (+l)`.
     *
     * @const int
     */
    const ERR_CHANNELISFULL     = 471;

    /**
     * `<char> :is unknown mode char to me for <channel>`.
     *
     * @const int
     */
    const ERR_UNKNOWNMODE       = 472;

    /**
     * `<channel> :Cannot join channel (+i)`.
     *
     * @const int
     */
    const ERR_INVITEONLYCHAN    = 473;

    /**
     * `<channel> :Cannot join channel (+b)`.
     *
     * @const int
     */
    const ERR_BANNEDFROMCHAN    = 474;

    /**
     * `<channel> :Cannot join channel (+k)`.
     *
     * @const int
     */
    const ERR_BADCHANNELKEY     = 475;

    /**
     * `<channel> :Bad Channel Mask`.
     *
     * @const int
     */
    const ERR_BADCHANMASK       = 476;

    /**
     * `<channel> :Channel doesn't support modes`.
     *
     * @const int
     */
    const ERR_NOCHANMODES       = 477;

    /**
     * `<channel> <char> :Channel list is full`.
     *
     * @const int
     */
    const ERR_BANLISTFULL       = 478;

    /**
     * `:Permission Denied- You're not an IRC operator`.
     *
     * @const int
     */
    const ERR_NOPRIVILEGES      = 481;

    /**
     * `<channel> :You're not channel operator`.
     *
     * @const int
     */
    const ERR_CHANOPRIVSNEEDED  = 482;

    /**
     * `:You can't kill a server!`.
     *
     * @const int
     */
    const ERR_CANTKILLSERVER    = 483;

    /**
     * `:Your connection is restricted!`.
     *
     * @const int
     */
    const ERR_RESTRICTED        = 484;

    /**
     * `:You're not the original channel operator`.
     *
     * @const int
     */
    const ERR_UNIQOPPRIVSNEEDED = 485;

    /**
     * `:No O-lines for your host`.
     *
     * @const int
     */
    const ERR_NOOPERHOST        = 491;

    /**
     * `:Unknown MODE flag`.
     *
     * @const int
     */
    const ERR_UMODEUNKNOWNFLAG  = 501;

    /**
     * `:Cannot change mode for other users`.
     *
     * @const int
     */
    const ERR_USERSDONTMATCH    = 502;

    /**
     * Mapping of well-known error replies to their respective names.
     *
     * @var array
     */
    public static $errorMapping = [
        401 => 'ERR_NOSUCHNICK',
        402 => 'ERR_NOSUCHSERVER',
        403 => 'ERR_NOSUCHCHANNEL',
        404 => 'ERR_CANNOTSENDTOCHAN',
        405 => 'ERR_TOOMANYCHANNELS',
        406 => 'ERR_WASNOSUCHNICK',
        407 => 'ERR_TOOMANYTARGETS',
        408 => 'ERR_NOSUCHSERVICE',
        409 => 'ERR_NOORIGIN',
        411 => 'ERR_NORECIPIENT',
        412 => 'ERR_NOTEXTTOSEND',
        413 => 'ERR_NOTOPLEVEL',
        414 => 'ERR_WILDTOPLEVEL',
        415 => 'ERR_BADMASK',
        421 => 'ERR_UNKNOWNCOMMAND',
        422 => 'ERR_NOMOTD',
        423 => 'ERR_NOADMININFO',
        424 => 'ERR_FILEERROR',
        431 => 'ERR_NONICKNAMEGIVEN',
        432 => 'ERR_ERRONEUSNICKNAME',
        433 => 'ERR_NICKNAMEINUSE',
        436 => 'ERR_NICKCOLLISION',
        437 => 'ERR_UNAVAILRESOURCE',
        441 => 'ERR_USERNOTINCHANNEL',
        442 => 'ERR_NOTONCHANNEL',
        443 => 'ERR_USERONCHANNEL',
        444 => 'ERR_NOLOGIN',
        445 => 'ERR_SUMMONDISABLED',
        446 => 'ERR_USERSDISABLED',
        451 => 'ERR_NOTREGISTERED',
        461 => 'ERR_NEEDMOREPARAMS',
        462 => 'ERR_ALREADYREGISTRED',
        463 => 'ERR_NOPERMFORHOST',
        464 => 'ERR_PASSWDMISMATCH',
        465 => 'ERR_YOUREBANNEDCREEP',
        466 => 'ERR_YOUWILLBEBANNED',
        467 => 'ERR_KEYSET',
        471 => 'ERR_CHANNELISFULL',
        472 => 'ERR_UNKNOWNMODE',
        473 => 'ERR_INVITEONLYCHAN',
        474 => 'ERR_BANNEDFROMCHAN',
        475 => 'ERR_BADCHANNELKEY',
        476 => 'ERR_BADCHANMASK',
        477 => 'ERR_NOCHANMODES',
        478 => 'ERR_BANLISTFULL',
        481 => 'ERR_NOPRIVILEGES',
        482 => 'ERR_CHANOPRIVSNEEDED',
        483 => 'ERR_CANTKILLSERVER',
        484 => 'ERR_RESTRICTED',
        485 => 'ERR_UNIQOPPRIVSNEEDED',
        491 => 'ERR_NOOPERHOST',
        501 => 'ERR_UMODEUNKNOWNFLAG',
        502 => 'ERR_USERSDONTMATCH'
    ];
}
