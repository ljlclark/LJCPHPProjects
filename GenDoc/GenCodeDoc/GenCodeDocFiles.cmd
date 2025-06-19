if exist "Debug" goto continue
rem mkdir "Debug"
:continue
del php_errors.log
PHP.exe GenCodeDocFiles.php > Debug/GenCodeDocFiles.txt
rem PHP.exe GenCodeDocFiles.php
