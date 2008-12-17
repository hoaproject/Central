@echo off

REM **
REM * Hoa Framework
REM *
REM *
REM * @license
REM *
REM * GNU General Public License
REM *
REM * This file is part of Hoa Open Accessibility.
REM * Copyright (c) 2007, 2008 Ivan ENDERLIN. All rights reserved.
REM *
REM * HOA Open Accessibility is free software; you can redistribute it and/or
REM * modify it under the terms of the GNU General Public License as published by
REM * the Free Software Foundation; either version 2 of the License, or
REM * (at your option) any later version.
REM *
REM * HOA Open Accessibility is distributed in the hope that it will be useful,
REM * but WITHOUT ANY WARRANTY; without even the implied warranty of
REM * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
REM * GNU General Public License for more details.
REM *
REM * You should have received a copy of the GNU General Public License
REM * along with HOA Open Accessibility; if not, write to the Free Software
REM * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
REM *
REM *
REM * @category    Data
REM *
REM **
REM
REM **
REM * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
REM * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
REM * @license     http://gnu.org/licenses/gpl.txt GNU GPL
REM * @since       PHP 5
REM * @version     0.1
REM **

BREAK=ON
set PHP="php.exe"
set SCRIPT_DIR=%~dp0
set HOA=%SCRIPT_DIR%Hoa.php

"%PHP%" "%HOA%" %*
