call DataManagerTestClear.cmd
php DataManagerTest.php > DataManagerOutput.log
call ParseError.cmd
call NotePad.exe DataManagerOutput.log