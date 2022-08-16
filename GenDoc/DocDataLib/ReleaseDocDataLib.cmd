echo off
rem ** ReleaseDocDataLib.cmd
rem ** There must be no space on either side of "=".
rem ** md requires folder to end with "\".
if %1%. == . goto Error

set toPath=%1%
call ../../MkDir.cmd %toPath%\
copy *.php %toPath%
copy ReadMe*.txt %toPath%

set toPath=%1%\Doc
call ../../MkDir.cmd %toPath%\
copy Doc\*.xml %toPath%

goto Exit
:Error
echo ReleaseDocDataLib.cmd: Missing parameter 1 - toFolder.
pause
:Exit
