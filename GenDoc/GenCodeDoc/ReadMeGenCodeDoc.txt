ReadMeGenCodeDoc.txt

The project contains classes to generate HTML files for specific PHP Source
files.

GenCodeDocFiles - Calls GenCodeDoc->CreateXML() for each PHP File where HTML
                  CodeDoc will be generated.

  Included Classes:
  GenCodeDocLib - Contains classes to generate the XML and HTML files.

    GenCodeDoc->CreateXML()
     For each specified PHP Source file it calls
     LJCDocDataGen->CreateDocXMLString() to create the DocXML string.

     The DocXML string is passed to LJCGenDataGen->CreateLibXMLString() to
     create the GenXML string.
 
     The data from the GenXML string and the appropriate Text Template are
     passed to LJCGenText->ProcessTemplate() method to generate the HTML file.
