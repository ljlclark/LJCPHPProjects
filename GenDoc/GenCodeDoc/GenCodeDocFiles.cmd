if exist "Debug" goto continue
rem mkdir "Debug"
:continue
call GenCodeDocFilesClear.cmd
php GenCodeDocFiles.php > GenCodeDocFiles.txt
rem php GenCodeDocFiles.php
