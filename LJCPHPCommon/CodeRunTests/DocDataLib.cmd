set src=..\GenDoc\DocDataLib
set out=DocDataLib
cd %src%
call TestDocDataGen.cmd
cd ..\..\CodeRunTests
copy %src%\Test.log %out%\Test.log
copy %src%\Debug\LJCDocDataParams.txt %out%\LJCDocDataParams.txt
copy %src%\php_errors.log %out%\php_errors.log
