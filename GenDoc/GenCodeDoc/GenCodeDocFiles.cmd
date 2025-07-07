if exist "Debug" goto continue
rem mkdir "Debug"
:continue
del php_errors.log
php GenCodeDocFiles.php > GenCodeDocFiles.txt
rem php GenCodeDocFiles.php
