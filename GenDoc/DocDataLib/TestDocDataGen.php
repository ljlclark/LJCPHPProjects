<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // TestDocDataGen.php
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/GenDoc/DocDataLib/LJCDocDataGenLib.php";
  // LJCDocDataGenLib: LJCDocDataGen
 
  $writeXML = true;
  $docDataGen = new LJCDocDataGen();
  $xmlString = $docDataGen->CreateDocDataXMLString("../GenCodeDoc/GenCodeDocLib.php"
    , $writeXML);
  echo($xmlString);
?>