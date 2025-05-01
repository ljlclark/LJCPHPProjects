<?php
  // Copyright(c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCDocDtaGen.php
include_once "LJCDocDataGenLib.php";

// LJCDocDataGenLib
//   LJCDocDataGen
 
$docDataGen = new LJCDocDataGen();
$xmlString = $docDataGen->CreateDocDataXMLString("LJCDocDataGenLib.php", true);
echo($xmlString);
?>