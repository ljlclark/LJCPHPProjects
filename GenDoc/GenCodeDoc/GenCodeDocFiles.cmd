if exist "Debug" goto continue
rem mkdir "Debug"
:continue
del php_errors.log
rem PHP.exe GenCodeDocFiles.php > Debug/GenCodeDocFiles.txt
PHP.exe GenCodeDocFiles.php
