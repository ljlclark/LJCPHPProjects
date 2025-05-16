<?php
  // Copyright(c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // TestDocDataGen.php
  // Must refer to exact same file everywhere in codeline.
  include_once "LJCDocDataGenLib.php";
  // LJCDocDataGenLib: LJCDocDataGen
 
  $writeXML = true;
  $docDataGen = new LJCDocDataGen();
  $xmlString = $docDataGen->CreateDocDataXMLString("../GenCodeDoc/GenCodeDocLib.php"
    , $writeXML);
  echo($xmlString);
?>