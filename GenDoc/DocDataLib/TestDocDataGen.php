<?php
  // Copyright(c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCDocDtaGen.php
  // Must refer to exact same file everywhere in codeline.
  include_once "LJCDocDataGenLib.php";

  // LJCDocDataGenLib
  //   LJCDocDataGen
 
  $docDataGen = new LJCDocDataGen();
  $xmlString = $docDataGen->CreateDocDataXMLString("LJCDocDataGenLib.php", true);
  echo($xmlString);
?>