echo off

cd 02CommonTest
call CommonTestClear.cmd

cd ..\03CollectionTest
call CollectionTestClear.cmd

cd ..\04TextBuilderTest
call TextBuilderTestClear.cmd

cd ..\05HTMLTest
call HTMLTestClear.cmd

cd ..\06DataConfigsTest

cd ..\07DBAccessTest
call DBAccessTestClear.cmd

cd ..\08DataManagerTest
call DataManagerTestClear.cmd

cd ..\09TextReaderTest

cd ..\10TextRangesTest

cd ..\11HTMLTableTest
call HTMLTableTestClear.cmd

cd ..\12TableServiceTest

cd ..\13DataServiceTest

cd ..\
pause