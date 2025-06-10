<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // HTMLObjectTableTest.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/LJCPHPCommon/LJCHTMLBuilderLib.php";
  include_once "$prefix/LJCPHPCommon/LJCHTMLObjectTableLib.php";
  // LJCHTMLBuilderLib: LJCHTMLBuilder, LJCAttribute, LJCAttributes
  //   , LJCTextState
  // LJCHTMLObjectTableLib: LJCHTMLObjectTable

  /// <summary>The HTML Object Table test class library</summary>
  /// LibName: LJCHTMLObjectTableLib
  //  Classes: TestHTMLObjectTable

  $testBuilder = new TestHTMLObjectTable();
  $testBuilder->Run();

  // ********************
  // Methods: 
  /// <summary>Represents a built string value.</summary>
  /// <include path='items/LJCHTMLBuilder/*' file='Doc/LJCHTMLBuilder.xml'/>
  class TestHTMLObjectTable
  {
    public static function Run()
    {
      echo("\r\n");
      echo("*** LJCHTMLObjectTable ***");

      $textState = new LJCTextState();
      self::GetArraysTable($textState);
      self::GetCollectionTable($textState);
      self::GetObjectsTable($textState);
    }

    // --------------------
    // Methods

    // Get Array data table.
    private static function GetArraysTable(LJCTextState $textState)
    {
      $dataItems = [
        [ "Name" => "border", "Value" => "1" ],
        [ "Name" => "cellspacing", "Value" => "2" ],
      ];

      $propertyNames = self::GetPropertyNames();
      $result = LJCHTMLObjectTable::DataHtml($dataItems, $propertyNames, $textState);
      $compare = self::GetTableCompare();
      LJCCommon::WriteCompare("GetTable()", $result, $compare);
    }

    // Get Collection data table.
    private static function GetCollectionTable(LJCTextState $textState)
    {
      $dataItems = new LJCAttributes();
      $dataItems->Add("border", "1");
      $dataItems->Add("cellspacing", "2");

      $propertyNames = self::GetPropertyNames();
      $result = LJCHTMLObjectTable::DataHtml($dataItems, $propertyNames, $textState);
      $compare = self::GetTableCompare();
      LJCCommon::WriteCompare("GetTable()", $result, $compare);
    }

    // Get Object Array data table.
    private static function GetObjectsTable(LJCTextState $textState)
    {
      $dataItems = [];
      $dataItems[] = new LJCAttribute("border", "1");
      $dataItems[] = new LJCAttribute("cellspacing", "2");

      $propertyNames = self::GetPropertyNames();
      $result = LJCHTMLObjectTable::DataHtml($dataItems, $propertyNames, $textState);
      $compare = self::GetTableCompare();
      LJCCommon::WriteCompare("GetTable()", $result, $compare);
    }

    // Get test property names.
    private static function GetPropertyNames()
    {
      $retNames = [
        "Name",
        "Value",
      ];
      return $retNames;
    }

    // Gets the table compare value.
    private static function GetTableCompare()
    {
      $hb = new LJCHTMLBuilder();

      $hb->AddLine("<table border=\"1\" cellspacing=\"0\" cellpadding=\"2\">");
      $hb->AddLine("  <tr>");
      $hb->AddLine("    <th>Name</th>");
      $hb->AddLine("    <th>Value</th>");
      $hb->AddLine("  </tr>");
      $hb->AddLine("  <tr>");
      $hb->AddLine("    <td>border</td>");
      $hb->AddLine("    <td>1</td>");
      $hb->AddLine("  </tr>");
      $hb->AddLine("  <tr>");
      $hb->AddLine("    <td>cellspacing</td>");
      $hb->AddLine("    <td>2</td>");
      $hb->AddLine("  </tr>");
      $hb->AddText("</table>");
      $retText = $hb->ToString();
      return $retText;
    }
  }
?>
