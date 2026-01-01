call CollectionTestClear.cmd
php CollectionTest.php > CollectionOutput.log
call ParseError.cmd
call NotePad.exe CollectionOutput.log