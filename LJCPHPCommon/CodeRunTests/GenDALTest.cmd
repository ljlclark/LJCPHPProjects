set src=..\GenDALTest
set out=GenDALTest
cd %src%
call GenDALData.cmd
Call TextGen.cmd
cd ..\CodeRunTests
copy %src%\GenDALData.txt %out%\GenDALData.txt
copy %src%\TextGenDebug.txt %out%\TextGenDebug.txt
copy %src%\php_errors.log %out%\php_errors.log
