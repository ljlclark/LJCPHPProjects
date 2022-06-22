Copyright (c) Lester J. Clark 2022 - All Rights Reserved
ReadMeDocDataLib.txt

This project contains classes to generate DocData XML.

DocData XML is generated from the PHP source files.

The DocData XML is intended to contain the XML Comments based on the PHP file
structure of Classes, Methods and Properties.

This is a standard layout of the PHP file structure and XML Comments that are
in the DocData XML. These elements and values can be used for additional
processing.

<LJCDocDataFile>
  <Name/>
  <Summary/>
  <Remarks/>
  <Classes>
    <Class>
      <Name/>
      <Summary/>
      <Remarks/>
      <Methods>
        <Method>
          <Name/>
          <Summary/>
          <Params>
            <Param>
              <Name/>
              <Summary/>
            </Param>
          </Params>
          <Remarks/>
          <Returns/>
          <Syntax/>
          <Code/>
        </Method>
      </Methods>
      <Properties>
        <Property>
          <Name/>
          <Summary/>
          <Syntax/>
        </Property>
      </Properties>
    </Class>
  </Classes>
</LJCDocDataFile>

LJCDocDataGenLib - Contains Classes to generate DocData XML strings and
                   optionally files.
                   The files are generated to ..\XMLDocData if the $writeXML
                   property is set to 'true' in class LJCDocDataGen, method
                   CreateDocXMLString().

  Included Classes:
  LJCDocDataLib - Contains Classes to represent DocData and for Serialization
	                and Deserialization.

  Main Object Graph
  LJCDocDataFile
    LJCDocDataClasses
      LJCDocDataClass
        LJCDocDataMethods
          LJCDocDataMethod
            LJCDocDataParams
              LJCDocDataParam
        LJCDocDataProperties
          LJCDocDataProperty
    LJCDocDataMethods

  LJCCommentsLib - The LJCComments object parses the code XML Comments and holds
                   them for the next generation point. A generation point is the
                   start of a File, Class, Method or Property.

  LJCIncludeLib -  The LJCInclude object parses the include XML Comments from a
                   referenced Include file. After the XML Comments are parsed
                   for that generation point, they are copied to the LJCComments
                   object.

  LJCParamCommentLib - The LJCParamComment object represents the Param XML
                       Comments.