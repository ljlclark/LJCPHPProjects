if exist "Debug" goto continue
mkdir "Debug"
:continue
PHP.exe GenCodeDocFiles.php > Debug/GenCodeDocFiles.txt
pause
