call HTMLBuilderTestClear.cmd
php HTMLBuilderTest.php > HTMLBuilderOutput.log
call ParseError.cmd
call NotePad.exe HTMLBuilderOutput.log