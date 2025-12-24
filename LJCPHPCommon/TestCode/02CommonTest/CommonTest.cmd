call CommonTestClear.cmd
php CommonTest.php > CommonOutput.log
call ParseError.cmd
call NotePad.exe CommonOutput.log