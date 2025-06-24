<?php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/GenTextLib/LJCGenTextLib.php";
  include_once "$prefix/GenTextLib/LJCGenTextSectionLib.php";

  $genText = new LJCGenText();
  $templateFileSpec = "..\GenDoc\GenDataLib\Templates\ClassTemplate.html";
  $sections = LJCSections::Deserialize("LJCHTMLTable.xml");

  // Add Groups
  $section = $sections->Retrieve("Function");
  $section->Groups[] = "Array of Arrays";
  $section->Groups[] = "Collection";
  $section->Groups[] = "Array of Objects";
  $section->Groups[] = "Array of Rows";

  // Add Member Groups
  $items = $section->RepeatItems;
  $item = $items->Retrieve("ArrayArrayHeadings");
  $item->MemberGroup = "Array of Arrays";
  $item = $items->Retrieve("ArrayArrayHTML");
  $item->MemberGroup = "Array of Arrays";
  $item = $items->Retrieve("ArrayArrayRows");
  $item->MemberGroup = "Array of Arrays";

  $item = $items->Retrieve("CollectionHeadings");
  $item->MemberGroup = "Collection";
  $item = $items->Retrieve("CollectionHTML");
  $item->MemberGroup = "Collection";
  $item = $items->Retrieve("CollectionRows");
  $item->MemberGroup = "Collection";

  $item = $items->Retrieve("ObjectArrayHeadings");
  $item->MemberGroup = "Array of Objects";
  $item = $items->Retrieve("ObjectArrayHTML");
  $item->MemberGroup = "Array of Objects";
  $item = $items->Retrieve("ObjectArrayRows");
  $item->MemberGroup = "Array of Objects";

  $item = $items->Retrieve("ResultHeadings");
  $item->MemberGroup = "Array of Rows";
  $item = $items->Retrieve("ResultHTML");
  $item->MemberGroup = "Array of Rows";
  $item = $items->Retrieve("ResultRows");
  $item->MemberGroup = "Array of Rows";

  // *****
  LJCSections::Serialize("Modified.xml", $sections, "Data");
  // *****

  $html = $genText->ProcessTemplate($templateFileSpec, $sections);
  echo($html);
?>