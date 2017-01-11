#!/usr/bin/env dtrace -s

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

#pragma D option quiet

BEGIN
{
}

php*:::request-startup
{
    printf("[38;5;0;48;5;226mRequest start[0m\n");
    self->up    = 0;
    self->depth = 0;
}

php*:::function-entry
/self->up == 0/
{
    self->time_last = timestamp;
}

php*:::function-entry
{
    self->depth += 2;
    printf(
        "[38;5;143m%6dms [38;5;94m%*s [38;5;74m%s[38;5;220m%s[38;5;106m%s[38;5;94m()\t\t\t[38;5;240m%s/[38;5;143m%s[38;5;240m:[38;5;143m%03d[0m\n",
        (timestamp - self->time_last) / 1000,
        self->depth, "➜",
        copyinstr(arg3),
        copyinstr(arg4),
        copyinstr(arg0),
        dirname(copyinstr(arg1)),
        basename(copyinstr(arg1)),
        arg2
    );
    self->up        = 1;
    self->time_last = timestamp;
}

php*:::exception-thrown
/arg0 != NULL/
{
    printf(
        "[38;5;166m●      %*s %s has been thrown[0m\n",
        self->depth, "",
        copyinstr(arg0)
    );
}

php*:::exception-caught
/arg0 != NULL/
{
    printf(
        "[38;5;106m✔         %*s %s has been caught[0m\n",
        self->depth, "",
        copyinstr(arg0)
    );
}

php*:::error
{
    printf(
        "[38;5;196m✖       %*s %s \t[38;5;240m%s/[38;5;143m%s[38;5;240m:[38;5;143m%03d[0m\n",
        self->depth, "",
        copyinstr(arg0),
        dirname(copyinstr(arg1)),
        basename(copyinstr(arg1)),
        arg2
    );
}

php*:::function-return
{
    printf(
        "[38;5;143m%6dms [38;5;94m%*s [38;5;74m%s[38;5;220m%s[38;5;106m%s[38;5;94m()[0m\n",
        (timestamp - self->time_last) / 1000,
        self->depth, "←",
        copyinstr(arg3),
        copyinstr(arg4),
        copyinstr(arg0)
    );
    self->depth -= 2;
}

php*:::request-shutdown
{
    printf("[38;5;0;48;5;226mRequest end[0m");
    self->up = 0;
}

END
{
    exit(0);
}
