<?php
  // DocDataGenTest.php
  declare(strict_types=1);
  // Must refer to exact same file everywhere in codeline.
  require_once "GenCodeDocLib.php";

  echo("Enter: GenCodeDocFiles.php");
  $genCodeDoc = new GenCodeDoc();
  $genCodeDoc->CreateFromList();
?>