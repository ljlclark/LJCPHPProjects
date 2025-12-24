call HTMLTableTestClear.cmd
php HTMLTableTest.php > HTMLTableOutput.log
call ParseError.cmd
copy HTMLTableOutput.log *.html
call NotePad.exe HTMLTableOutput.log
