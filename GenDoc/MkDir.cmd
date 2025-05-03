echo off
rem MkDir.cmd
rem ** There must be no space on either side of "=".
rem ** md requires folder to end with "\".
if %1%. == . goto Error

if exist %1% goto skip1
md %1%
:skip1

goto Exit
:Error
echo MkDir.cmd: Missing parameter 1 - Folder
pause
:Exit
