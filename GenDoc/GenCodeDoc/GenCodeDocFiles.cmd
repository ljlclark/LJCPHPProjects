if exist "Debug" goto continue
rem mkdir "Debug"
:continue
rem PHP.exe GenCodeDocFiles.php > Debug/GenCodeDocFiles.txt
PHP.exe GenCodeDocFiles.php
