call HTMLTestClear.cmd
php HTMLTest.php > HTMLOutput.log
call ParseError.cmd
call NotePad.exe HTMLOutput.log