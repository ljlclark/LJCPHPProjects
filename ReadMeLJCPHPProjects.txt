PHP Codelines

A PHP Codeline consists of a root folder and subfolders. The code in these
folders use only the code in their codeline and do not refer to code
outside of their codeline except for third-party software.

Usually codelines are grouped together under a main path. The main path
could be something like "C:\Users\User\Documents\Visual Studio 2022". In
this case the main IDE used is Visual Studio which creates the root folder
"Projects" in this main folder. So the PHP codelines can be created here
also.

LJCPHPProjects Codeline

This is the main codeline and is under source control online in GitHub at
https://github.com/ljlclark/LJCPHPProjects . Local code in this codeline
can be commited to GitHub.

LJCPHPProjectsDev Codeline

This is the development codeline. Development work is done here and then
promoted (backed up) to the main codeline LJCPHPProjects.

LJC PHP Projects

A Project is defined as a collection of files that all work together for a
primary purpose. They are usually all located together in a single folder.
This folder will often contain a command file (.cmd) which is the entry
point for the stand-alone utility program or test program.

Project Groups

A Project Group is several projects that work together for a common
purpose. Project Groups are organized under a group or "solution" folder
and may represent a utility or a reusable code module.

  GenDoc - A Program to Generate HTML Code Documentation from Individual
           Files.

Common PHP Classes

  LJCPHPCommon - Root Folder
    LJCCollectionLib.php
    LJCCommonLib.php
    LJCDataManagerLib.php
    LJCDbAccessLib.php
    LJCTextLib.php

TextReader - Reads Lines from a Text File as column data.

  TextReader - Project Folder
    LJCTextRangesLib.php
    LJCTextReaderLib.php
    TestData.txt
    TextReader.cmd
    TextReader.php

CodeDoc - HTML Code Documentation

  LJCPHPCodeDoc - Root Folder
    CodeDoc.css
    index.html
    LJCPHPCodeDoc.html - Opening iframe Page
    (Other CodeDoc Lib iframe pages and folders with Class pages)
    ReadMeLJCPHPCodeDoc.txt
