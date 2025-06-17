set outRoot=CodeRunTestsOutput

set command=GenDoc\GenCodeDoc\GenCodeDocFiles.cmd
set outFolder=GenCodeDoc
set outFile=GenCodeDocFiles.txt
call CodeRunTest.cmd

rem call GenDoc\DocDataLib\TestDocDataGen.cmd

set command=GenTextLib\TextGenLib.cmd
set outFolder=GenTextLib
set outFile=GenDALData.txt
call CodeRunTest.cmd

set command=TextReader.cmd
set outFolder=TextReader
set outFile=TextReader.txt
call CodeRunTest.cmd

set command=GenDALData.cmd
set outFolder=GenDALTest
set outFile=GenDALData.txt
call CodeRun.cmd
set outFile=TextGenDebug.txt
call CodeRunTest.cmd

set command=TestApps\TestHTMLWriter.cmd
set outFolder=TestHTMLWriter
set outFile=TestHTMLWriterOutput.log
call CodeRunTest.cmd

set command=GenDALData.cmd
set outFolder=GenDALTest
set outFile=GenDALData.txt
call LJCPHPCommon\HTMLBuilderTest\HTMLBuilderTest.cmd
del RunDebug\HTMLBuilderTest\HTMLBuilderOutput.log
move HTMLBuilderOutput.log CodeRunTestsDebug\HTMLBuilderTest\HTMLBuilderOutput.log

call LJCPHPCommon\HTMLTest\HTMLTest.cmd
del RunDebug\HTMLTest\HTMLOutput.log
move HTMLOutput.log CodeRunTestsDebug\HTMLTest\HTMLOutput.log

call LJCPHPCommon\HTMLTableTest\HTMLTableTest.cmd
del RunDebug\HTMLTableTest\HTMLTableOutput.log
del RunDebug\HTMLTableTest\HTMLTableOutput.html
move HTMLTableOutput.log CodeRunTestsDebug\HTMLTableTest\HTMLTableOutput.log
move HTMLTableOutput.html CodeRunTestsDebug\HTMLTableTest\HTMLTableOutput.html
