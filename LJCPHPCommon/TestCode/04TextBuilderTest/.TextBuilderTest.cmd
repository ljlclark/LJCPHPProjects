call TextBuilderTestClear.cmd
php TextBuilderTest.php > TextBuilderOutput.log
call ParseError.cmd
call NotePad.exe TextBuilderOutput.log