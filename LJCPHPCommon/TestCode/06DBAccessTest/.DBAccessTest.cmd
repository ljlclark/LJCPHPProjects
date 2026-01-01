call DBAccessTestClear.cmd
php DBAccessTest.php > DBAccessOutput.log
call ParseError.cmd
call NotePad.exe DBAccessOutput.log