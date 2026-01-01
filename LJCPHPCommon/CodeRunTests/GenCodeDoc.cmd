set src=..\GenDoc\GenCodeDoc
set out=GenCodeDoc
cd %src%
call GenCodeDocFiles.cmd
cd ..\..\CodeRunTests
copy %src%\GenCodeDocFiles.txt %out%\GenCodeDocFiles.txt
copy %src%\php_errors.log %out%\php_errors.log