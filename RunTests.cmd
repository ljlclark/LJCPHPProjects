call GenDoc\GenCodeDoc\GenCodeDocFiles.cmd

call GenDoc\DocDataLib\TestDocDataGen.cmd

call GenTextLib\TextGenLib.cmd
del RunDebug\GenTextLib\GenDALData.txt
move GenDALData.txt RunDebug\GenTextLib\GenDALData.txt

call TextReader\TextReader.cmd
del RunDebug\TextReader\TextReader.txt
move TextReader.txt RunDebug\TextReader\TextReader.txt

call GenDALTest\GenDALData.cmd
del RunDebug\GenDALTest\GenDALData.txt
move GenDALData.txt RunDebug\GenDALTest\GenDALData.txt

call GenDALTest\TextGen.cmd
del RunDebug\GenDALTest\TextGenDebug.txt
move TextGenDebug.txt RunDebug\GenDALTest\TextGenDebug.txt

call TestApps\TestHTMLWriter\TestHTMLWriter.cmd
del RunDebug\TestHTMLWriter\TestHTMLWriterOutput.log
move TestHTMLWriterOutput.log RunDebug\TestHTMLWriter\TestHTMLWriterOutput.log

call LJCPHPCommon\HTMLBuilderTest\HTMLBuilderTest.cmd
del RunDebug\HTMLBuilderTest\HTMLBuilderOutput.log
move HTMLBuilderOutput.log RunDebug\HTMLBuilderTest\HTMLBuilderOutput.log

call LJCPHPCommon\HTMLTest\HTMLTest.cmd
del RunDebug\HTMLTest\HTMLOutput.log
move HTMLOutput.log RunDebug\HTMLTest\HTMLOutput.log

call LJCPHPCommon\HTMLTableTest\HTMLTableTest.cmd
del RunDebug\HTMLTableTest\HTMLTableOutput.log
del RunDebug\HTMLTableTest\HTMLTableOutput.html
move HTMLTableOutput.log RunDebug\HTMLTableTest\HTMLTableOutput.log
move HTMLTableOutput.html RunDebug\HTMLTableTest\HTMLTableOutput.html
