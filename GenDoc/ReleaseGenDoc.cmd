echo off
rem ** ReleaseGenDoc.cmd
rem ** There must be no space on either side of "=".
rem ** md requires folder to end with "\".
if %1%. == . goto Error

set toPath=%1%
call ../MkDir.cmd %toPath%\
copy ReadMe*.txt %toPath%

goto Exit
:Error
echo ReleaseGenDoc.cmd: Missing parameter 1 - toFolder.
pause
:Exit
