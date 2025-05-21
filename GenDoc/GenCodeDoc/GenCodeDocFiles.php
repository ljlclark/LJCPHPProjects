<?php
  // DocDataGenTest.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/GenDoc/GenCodeDoc/GenCodeDocLib.php";

  echo("Enter: GenCodeDocFiles.php");
  $genCodeDoc = new GenCodeDoc();
  $genCodeDoc->CreateFromList();
?>