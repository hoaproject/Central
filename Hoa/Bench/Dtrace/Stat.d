#!/usr/bin/env dtrace -s

#pragma D option quiet
#pragma D option aggsortrev

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

struct call_s {
    uint64_t time;
    size_t   depth;
    string   name;
};
struct call_s calls[uint64_t];

uint64_t execute_start;
uint64_t execute_stop;

BEGIN
{
    self->i = 0;
}

php*:::execute-entry
{
    execute_start = timestamp;
}

php*:::function-entry
/copyinstr(arg0) != ""/
{
    calls[self->i].name  = strjoin(
        copyinstr(arg3),
        strjoin(copyinstr(arg4), copyinstr(arg0))
    );
    calls[self->i].time  = timestamp;
    calls[self->i].depth = self->i;

    self->i++;
}

php*:::function-return
/copyinstr(arg0) != ""/
{
    self->i--;

    @c[calls[self->i].name] = count();
    @a[calls[self->i].name] = quantize(
        (timestamp - calls[self->i].time) / 1000
    );
}

php*:::execute-return
{
    execute_stop = timestamp;
}

END
{
    printf("Count calls:\n");
    printa("  • %-80s%@u\n", @c);

    printf("\nExecution distribution (values are in nanoseconds):\n");
    printa(@a);

    printf("Total execution time: %dms", (execute_stop - execute_start) / 1000);

    exit(0);
}
