<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2016, Hoa community. All rights reserved.
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
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
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

namespace Hoa\Irc\Exception;

/**
 * Class \Hoa\Irc\Exception\ErrorReplies.
 *
 * Extending the \Hoa\Irc\Exception class.
 * Represent all IRC error replies, see
 * https://tools.ietf.org/html/rfc1459#section-6.1.
 *
 * @copyright  Copyright © 2007-2016 Hoa community
 * @license    New BSD License
 */
class ErrorReplies extends Exception
{
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
