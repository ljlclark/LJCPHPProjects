if exist "Debug" goto continue
rem mkdir "Debug"
:continue
del php_errors.log
rem php GenCodeDocFiles.php > GenCodeDocFiles.txt
php GenCodeDocFiles.php
