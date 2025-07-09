<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // GenCodeDocFiles.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/GenDoc/GenCodeDoc/GenCodeDocLib.php";

  //Generate DocData XML, GenData XML and CodeDoc HTML pages.
  $genCodeDoc = new GenCodeDoc();
  $genCodeDoc->CreateFromList();
?>