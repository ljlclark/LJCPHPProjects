Copyright (c) Lester J. Clark 2022 - All Rights Reserved
ReadMeGenDataLib.txt

This project contains classes to generate GenData XML and CodeDoc HTML files.

GenData XML data is generated from the DocData XML data.

The GenData XML is intended to display the XML Comments based on the Text
Template that will be used. The Text Section and Replacement names must match
those expected by the Template.

This is the standard layout for the GenData XML files.

  <Data xmlns:xsd="http://www.w3.org/2001/XMLSchema"
   xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <Sections>
      <Section>
        <Begin/>
        <Name>Main</Name>
        <Items>
          <Item>
            <Replacements>
              <Replacement>
                <Name></Name>
                <Value></Value>
              <Replacement>
            <Replacements>
          <Item>
        <Items>
      <Section>
    <Sections>
  </Data>

The number and names of sections, items and replacements depends on the GenData
from the PHP files and the HTML Template that is used.

LJCGenDataGenLib - Contains Classes to generate GenData XML strings and
                   optionally files.
                   The files are generated to ..\XMLGenData if the $writeXML
                   property is set to 'true' in class LJCGenDataGen, method
                   CreateLibXMLString().
                   Uses the LJCGenTextLib class LJCGenText to generate the
                   CodeDoc HTML files from the GenDoc XML and HTML Text
                   Templates.

  Included Classes:
  LJCGenDataXMLLib - The LJCGenDataXML class contains static methods for
                     generating static GenData XML text such as beginning
                     or ending an Item element, etc.

In this implementation there is a GenData XML file created for each PHP file,
Class, Method/Function and Property.

An HTML page is generated for each GenData XML file and the corresponding HTML
Text Template. The templates are in the Templates subfolder.

PHP File - LibTemplate.html
Class - ClassTemplate.html
Method - FunctionTemplate.html
Property - PropertyTemplate.html

The PHP HTML files are output to the folder "DocPages".

A subfolder is created for each class. This folder contains the Class HTML,
Methods HTML and Properties HTML files. The Methods and Properties HTML names
are prefixed with the Class name to prevent overwriting items with the same
name.
