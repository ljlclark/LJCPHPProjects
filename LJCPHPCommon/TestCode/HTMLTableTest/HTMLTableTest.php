<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // HTMLTableTest.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/LJCPHPCommon/LJCCommonLib.php";
  include_once "$prefix/LJCPHPCommon/LJCDataManagerLib.php";
  include_once "$prefix/LJCPHPCommon/LJCTextBuilderLib.php";
  include_once "$prefix/LJCPHPCommon/LJCHTMLTableLib.php";
  // LJCCommonLib: LJCCommon
  // LJCDataManagerLib: LJCConnectionValues, LJCDataManager
  // LJCTextBuilderLib: LJCAttribute, LJCAttributes, LJCTextBuilder 
  //   , LJCTextState
  // LJCHTMLTableLib: LJCHTMLTable

  /// <summary>The HTML Table test class library</summary>
  /// LibName: HTMLTableTest
  //  Classes: TestHTMLTable

  $testBuilder = new TestHTMLTable();
  $testBuilder->Run();

  // ********************
  // Methods: Run(), GetArraysTable(), GetCollectionTable(), GetObjectsTable()
  //   , GetResultsTable()
  //
  /// <summary>Represents a built string value.</summary>
  /// <include path='items/LJCTextBuilder/*' file='Doc/LJCTextBuilder.xml'/>
  class TestHTMLTable
  {
    public static function Run()
    {
      echo("\r\n");
      echo("*** LJCHTMLTable ***");

      $textState = new LJCTextState();
      self::GetArraysTable($textState);
      self::GetCollectionTable($textState);
      self::GetObjectsTable($textState);
      self::GetResultsTable($textState);
    }

    // --------------------
    // Static Methods

    // Get Array data table.
    private static function GetArraysTable(LJCTextState $textState)
    {
      $dataItems = [
        [ "Name" => "border", "Value" => "1" ],
        [ "Name" => "cellspacing", "Value" => "2" ],
      ];
      $propertyNames = self::GetPropertyNames();

      $ht = new LJCHTMLTable();
      $ht->ColumnNames = $propertyNames;
      $tb = new LJCTextBuilder();
      $ht->TableAttribs = $tb->TableAttribs();
      //$result = $ht->ArrayArrayHtml($dataItems, $textState);
      $result = $ht->ResultHtml($dataItems, $textState);
      $compare = self::GetTableCompare();
      LJC::OutputDebugCompare("GetArraysTable()", $result, $compare);
    }

    // Get Collection data table.
    private static function GetCollectionTable(LJCTextState $textState)
    {
      $dataItems = new LJCAttributes();
      $dataItems->Add("border", "1");
      $dataItems->Add("cellspacing", "2");
      $propertyNames = self::GetPropertyNames();

      $ht = new LJCHTMLTable();
      $ht->ColumnNames = $propertyNames;
      $tb = new LJCTextBuilder();
      $ht->TableAttribs = $tb->TableAttribs();
      $result = $ht->CollectionHTML($dataItems, $textState
        , $propertyNames);
      $compare = self::GetTableCompare();
      LJC::OutputDebugCompare("GetCollectionTable()", $result, $compare);
    }

    // Get Object Array data table.
    private static function GetObjectsTable(LJCTextState $textState)
    {
      $dataItems = [];
      $dataItems[] = new LJCAttribute("border", "1");
      $dataItems[] = new LJCAttribute("cellspacing", "2");
      $propertyNames = self::GetPropertyNames();

      $ht = new LJCHTMLTable();
      $ht->ColumnNames = $propertyNames;
      $tb = new LJCTextBuilder();
      $ht->TableAttribs = $tb->TableAttribs();
      $result = $ht->ObjectArrayHTML($dataItems, $textState
        , $propertyNames);
      $compare = self::GetTableCompare();
      LJC::OutputDebugCompare("GetObjectsTable()", $result, $compare);
    }

    // Get Result table.
    private static function GetResultsTable(LJCTextState $textState)
    {
	    $database = "TestData";
	    $userID = "root";
	    $password = "Unifies1";
      $connectionValues = new LJCConnectionValues("localhost", $database, $userID
        , $password);
      //$tableName = "Region";
      $tableName = "Province";
      //$tableName = "City";
      //$tableName = "CitySection";
      $manager = new LJCDataManager($connectionValues, $tableName);

      // Retrieves all column values.
      $rows = $manager->Load();

      $propertyNames = $manager->PropertyNames();
      $ht = new LJCHTMLTable();
      $ht->ColumnNames = $propertyNames;
      $tb = new LJCTextBuilder();
      $ht->TableAttribs = $tb->TableAttribs();
      $result = $ht->ResultHTML($rows, $textState);
      //$compare = self::GetTableCompare();
      //LJC::OutputDebugCompare("GetResultTable()", $result, $compare);
    }

    // --------------------
    // Static Private Methods

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
      $tb = new LJCTextBuilder();

      $tb->AddLine("<table border=\"1\" cellspacing=\"0\" cellpadding=\"2\">");
      $tb->AddLine("  <tr>");
      $tb->AddLine("    <th>Name</th>");
      $tb->AddLine("    <th>Value</th>");
      $tb->AddLine("  </tr>");
      $tb->AddLine("  <tr>");
      $tb->AddLine("    <td>border</td>");
      $tb->AddLine("    <td>1</td>");
      $tb->AddLine("  </tr>");
      $tb->AddLine("  <tr>");
      $tb->AddLine("    <td>cellspacing</td>");
      $tb->AddLine("    <td>2</td>");
      $tb->AddLine("  </tr>");
      $tb->AddText("</table>");
      $retText = $tb->ToString();
      return $retText;
    }
  }
?>
