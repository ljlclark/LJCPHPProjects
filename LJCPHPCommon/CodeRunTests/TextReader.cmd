set src=..\TextReader
set out=TextReader
cd %src%
call TextReader.cmd
cd ..\CodeRunTests
copy %src%\TextReader.txt %out%\TextReader.txt
copy %src%\php_errors.log %out%\php_errors.log
