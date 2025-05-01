ReadMeGenDoc.txt

The GenDoc Project Group:
The GenCodeDoc  generates HTML documentation from PHP source file XML comments.
It also uses external XML comment files which are referenced by source file
"include" statements.

The GenDoc project group has three projects:

GenCodeDoc - Calls DocDataLib and GenDataLib for each PHP file to generate the
             CodeDoc HTML files.
             App start command file: GenCodeDocFiles.cmd
DocDataLib - Creates the DocData XML from a PHP source file.
GenDataLib - Creates the GenData XML from a DocData XML file. Also creates the
             HTML files by combining the GenData and an HTML Text Template.