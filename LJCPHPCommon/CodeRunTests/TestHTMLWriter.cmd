set src=..\TestApps\TestHTMLWriter
set out=TestHTMLWriter
cd %src%
call TestHTMLWriter.cmd
cd ..\..\CodeRunTests
copy %src%\TestHTMLWriterOutput.log %out%\TestHTMLWriterOutput.log
copy %src%\php_errors.log %out%\php_errors.log
