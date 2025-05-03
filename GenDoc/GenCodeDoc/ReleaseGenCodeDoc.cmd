echo off
rem ** ReleaseGenCodeDoc.cmd
rem ** There must be no space on either side of "=".
rem ** md requires folder to end with "\".
if %1%. == . goto Error

set toPath=%1%
call ../../MkDir.cmd %toPath%\
copy *.php %toPath%
copy ReadMe*.txt %toPath%
copy *.cmd %toPath%

goto Exit
:Error
echo ReleaseGenCodeDoc.cmd: Missing parameter 1 - toFolder.
pause
:Exit
