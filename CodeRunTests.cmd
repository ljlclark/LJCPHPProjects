set outRoot=CodeRunTestsOutput

set command=GenDoc\GenCodeDoc\GenCodeDocFiles.cmd
set outFolder=GenCodeDoc
set outFile=GenCodeDocFiles.txt
call CodeRunTest.cmd

rem call GenDoc\DocDataLib\TestDocDataGen.cmd

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

set command=LJCPHPCommon\HTMLBuilderTest\HTMLBuilderTest.cmd
set outFolder=HTMLBuilderTest
set outFile=HTMLBuilderOutput.log
call CodeRunTest.cmd

set command=LJCPHPCommon\HTMLTest\HTMLTest.cmd
set outFolder=HTMLTest
set outFile=HTMLOutput.log
call CodeRunTest.cmd

set command=LJCPHPCommon\HTMLTableTest\HTMLTableTest.cmd
set outFolder=HTMLTableTest
set outFile=HTMLTableOutput.log
call CodeRunTest.cmd
move HTMLTableOutput.html CodeRunTestsDebug\%outFolder%\HTMLTableOutput.html
