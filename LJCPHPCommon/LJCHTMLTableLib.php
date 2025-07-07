<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCHTMLTableLib.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/LJCPHPCommon/LJCCommonLib.php";
  include_once "$prefix/LJCPHPCommon/LJCHTMLBuilderLib.php";
  // LJCCommonLib: LJCCommon
  // LJCHTMLBuilderLib: LJCHTMLBuilder

  /// <summary>The HTML Object Table Class Library</summary>
  /// LibName: LJCHTMLTableLib
  //  Classes: LJCHTMLTable

  // ********************
  // Methods:
  // ArrayArrayHeadings(), ArrayArrayHTML(), ArrayArrayRows()
  // CollectionHeadings(), CollectionHTML(), CollectionRows()
  // ObjectArrayHeadings(), ObjectArrayHTML(), ObjectArrayRows()
  // ResultHeadings(), ResultHTML(), ResultRows()
  //
  /// <summary>Provides methods to create an object HTML Table.</summary>
  /// <group name="Arrays">Array of Arrays</group>
  /// <group name="Collection">Collection</group>
  /// <group name="Objects">Array of Objects</group>
  /// <group name="Rows">Array of Rows</group>
  class LJCHTMLTable
  {
    // ----------
    // Static ArrayArray Functions

    // Create table headings from an ArrayArray Data Object.
    /// <include path='items/ArrayArrayHeadings/*' file='Doc/LJCHTMLTable.xml'/>
    /// <ParentGroup>Arrays</ParentGroup>
    public static function ArrayArrayHeadings(array $dataItems
      , array $propertyNames, LJCTextState $textState): string
    {
      $retValue = null;

      if (LJCCommon::HasElements($dataItems))
      {
        $dataItem = $dataItems[0];
        $retValue = self::ArrayHeadings($dataItem, $textState, $propertyNames);
      }
      return $retValue;
    }

    // Create an HTML table from an ArrayArray Data Object.
    /// <include path='items/ArrayArrayHeadings/*' file='Doc/LJCHTMLTable.xml'/>
    /// <ParentGroup>Arrays</ParentGroup>
    public static function ArrayArrayHTML(array $dataItems, array $propertyNames
      , LJCTextState $textState, int $maxRows = 0): string
    {
      $retValue = null;

      if (LJCCommon::HasItems($dataItems))
      {
        $hb = new LJCHTMLBuilder($textState);
        $attribs = $hb->TableAttribs();
        $hb->Begin("table", $textState, $attribs);
        // *** Add ***
        $textState = $hb->GetTextState();
        $text = self::ArrayArrayHeadings($dataItems, $propertyNames
         , $textState);
        $hb->Text($text, false);
        // *** Add ***
        $textState = $hb->GetTextState();
        $text = self::ArrayArrayRows($dataItems, $propertyNames, $textState
          , $maxRows);
        $hb->Text($text, false);
        $hb->End("table", $textState);
        $retValue = $hb->ToString();
      }
      return $retValue;
    }

    /// <summary>Create table rows from an ArrayArray Data Object.</summary>
    /// <include path='items/ArrayArrayRows/*' file='Doc/LJCHTMLTable.xml'/>
    /// <ParentGroup>Arrays</ParentGroup>
    public static function ArrayArrayRows(array $dataItems, array $propertyNames
      , LJCTextState $textState, int $maxRows = 0): string
    {
      $retValue = null;

      if (LJCCommon::HasElements($dataItems))
      {
        $hb = new LJCHTMLBuilder($textState);

        $count = 0;
        foreach ($dataItems as $dataItem)
        {
          if (self::EndRetrieve($maxRows, $count))
          {
            break;
          }
          $text = self::ArrayRow($dataItem, $textState, $propertyNames);
          $hb->Text($text, false);
        }
        $retValue = $hb->ToString();
      }
      return $retValue;
    }

    // ----------
    // Static Collection Functions

    // Create table headings from a Collection Object.
    /// <include path='items/CollectionHeadings/*' file='Doc/LJCHTMLTable.xml'/>
    /// <ParentGroup>Collection</ParentGroup>
    public static function CollectionHeadings(LJCCollectionBase $dataItems
      , array $propertyNames, LJCTextState $textState): string
    {
      $retValue = null;

      if (LJCCommon::HasItems($dataItems))
      {
        $dataItem = $dataItems->Item(0);
        $retValue = self::ObjectHeadings($dataItem, $propertyNames
          , $textState);
      }
      return $retValue;
    }

    // Create an HTML table from an Collection Object.
    /// <include path='items/CollectionHTML/*' file='Doc/LJCHTMLTable.xml'/>
    /// <ParentGroup>Collection</ParentGroup>
    public static function CollectionHTML(LJCCollectionBase $dataItems
      , array $propertyNames, LJCTextState $textState, int $maxRows = 0): string
    {
      $retValue = null;

      if (LJCCommon::HasItems($dataItems))
      {
        $hb = new LJCHTMLBuilder($textState);
        $attribs = $hb->TableAttribs();
        $hb->Begin("table", $textState, $attribs);
        // *** Add ***
        $textState = $hb->GetTextState();
        $text = self::CollectionHeadings($dataItems, $propertyNames
         , $textState);
        $hb->Text($text, false);
        // *** Add ***
        $textState = $hb->GetTextState();
        $text = self::CollectionRows($dataItems, $propertyNames, $textState
          , $maxRows);
        $hb->Text($text, false);
        $hb->End("table", $textState);
        $retValue = $hb->ToString();
      }
      return $retValue;
    }

    // Create table rows from a Collection Object.
    /// <include path='items/CollectionRows/*' file='Doc/LJCHTMLTable.xml'/>
    /// <ParentGroup>Collection</ParentGroup>
    public static function CollectionRows(LJCCollectionBase $dataItems
      , array $propertyNames, LJCTextState $textState, int $maxRows = 0): string
    {
      $retValue = null;

      if (LJCCommon::HasItems($dataItems))
      {
        $hb = new LJCHTMLBuilder($textState);

        $count = 0;
        foreach ($dataItems as $dataItem)
        {
          if (self::EndRetrieve($maxRows, $count))
          {
            break;
          }
          $hb->Begin("tr", $textState);
          foreach ($propertyNames as $propertyName)
          {
            if (property_exists($dataItem, $propertyName))
            {
              // Using variable name for object property.
              $value = $dataItem->$propertyName;
              $hb->Create("td", $textState, $value);
            }
          }
          $hb->End("tr", $textState);
        }
        $retValue = $hb->ToString();
      }
      return $retValue;
    }

    // ----------
    // Static ObjectArray Functions

    // Create table headings from an ObjectArray Data Object.
    /// <include path='items/ObjectArrayHeadings/*' file='Doc/LJCHTMLTable.xml'/>
    /// <ParentGroup>Objects</ParentGroup>
    public static function ObjectArrayHeadings(array $dataItems, array $propertyNames
      , LJCTextState $textState): string
    {
      $retValue = null;

      if (LJCCommon::HasElements($dataItems))
      {
        $dataItem = $dataItems[0];
        if ($dataItem != null)
        {
          $retValue = self::ObjectHeadings($dataItem, $propertyNames
            , $textState);
        }
      }
      return $retValue;
    }

    /// <summary>Create an HTML table from an ObjectArray Data Object.</summary>
    /// <include path='items/ObjectArrayHTML/*' file='Doc/LJCHTMLTable.xml'/>
    /// <ParentGroup>Objects</ParentGroup>
    public static function ObjectArrayHTML(array $dataItems, array $propertyNames
      , LJCTextState $textState, int $maxRows = 0): string
    {
      $retValue = null;

      if (LJCCommon::HasItems($dataItems))
      {
        $hb = new LJCHTMLBuilder($textState);
        $attribs = $hb->TableAttribs();
        $hb->Begin("table", $textState, $attribs);
        // *** Add ***
        $textState = $hb->GetTextState();
        $text = self::ObjectArrayHeadings($dataItems, $propertyNames
         , $textState);
        $hb->Text($text, false);
        // *** Add ***
        $textState = $hb->GetTextState();
        $text = self::ObjectArrayRows($dataItems, $propertyNames, $textState
          , $maxRows);
        $hb->Text($text, false);
        $hb->End("table", $textState);
        $retValue = $hb->ToString();
      }
      return $retValue;
    }

    // Create table rows from an ObjectArray Data Object.
    /// <include path='items/ObjectArrayHTML/*' file='Doc/LJCHTMLTable.xml'/>
    /// <ParentGroup>Objects</ParentGroup>
    public static function ObjectArrayRows(array $dataItems, array $propertyNames
      , LJCTextState $textState, int $maxRows = 0): string
    {
      $retValue = null;

      if (LJCCommon::HasElements($dataItems))
      {
        $hb = new LJCHTMLBuilder($textState);

        $count = 0;
        foreach ($dataItems as $dataItem)
        {
          if (self::EndRetrieve($maxRows, $count))
          {
            break;
          }
          $text = self::ObjectRow($dataItem, $propertyNames, $textState);
          $hb->Text($text, false);
        }
        $retValue = $hb->ToString();
      }
      return $retValue;
    }

    // ----------
    // Static Data Table Functions

    // Create table headings from result rows.
    /// <include path='items/ResultHeadings/*' file='Doc/LJCHTMLTable.xml'/>
    /// <ParentGroup>Rows</ParentGroup>
    public static function ResultHeadings(array $rows, LJCTextState $textState
      , array $propertyNames): string
    {
      $retValue = null;

      if (LJCCommon::HasElements($rows))
      {
        $row = $rows[0];
        $retValue = self::ArrayHeadings($row, $textState, $propertyNames);
      }
      return $retValue;
    }

    // Create an HTML table from result rows.
    /// <include path='items/ResultHTML/*' file='Doc/LJCHTMLTable.xml'/>
    /// <ParentGroup>Rows</ParentGroup>
    public static function ResultHTML(array $rows, LJCTextState $textState
      , array $propertyNames, int $maxRows = 0): string
    {
      $retValue = null;

      if (LJCCommon::HasElements($rows))
      {
        $hb = new LJCHTMLBuilder($textState);

        $attribs = $hb->TableAttribs();
        $hb->Begin("table", $textState, $attribs);
        // *** Add ***
        $textState = $hb->GetTextState();
        $text = self::ResultHeadings($rows, $textState, $propertyNames);
        $hb->Text($text, false);
        // *** Add ***
        $textState = $hb->GetTextState();
        $text = self::ResultRows($rows, $textState, $propertyNames
          , $maxRows);
        $hb->Text($text, false);
        $hb->End("table", $textState);
        $retValue = $hb->ToString();
      }
      return $retValue;
    }

    // Create table rows from result rows.
    /// <include path='items/ResultRows/*' file='Doc/LJCHTMLTable.xml'/>
    /// <ParentGroup>Rows</ParentGroup>
    public static function ResultRows(array $rows, LJCTextState $textState
      , array $propertyNames, int $maxRows = 0): string
    {
      $retValue = null;

      if (LJCCommon::HasElements($rows))
      {
        $hb = new LJCHTMLBuilder($textState);

        $count = 0;
        foreach ($rows as $row)
        {
          if (self::EndRetrieve($maxRows, $count))
          {
            break;
          }
          $text = self::ArrayRow($row, $textState, $propertyNames);
          $hb->Text($text, false);
        }
        $retValue = $hb->ToString();
      }
      return $retValue;
    }

    // --------------------
    // Static Support Methods

    // Create the Array heading table rows.
    private static function ArrayHeadings(array $dataItem
      , LJCTextState $textState, array $propertyNames): string
    {
      $retValue = null;

      if (LJCCommon::HasElements($dataItem))
      {
        $hb = new LJCHTMLBuilder($textState);
        // *****
        $indentCount = strval($textState->IndentCount);
        echo("\r\n".__line__." textState->IndentCount = {$indentCount}");
        $indentLength = strval($hb->IndentLength());
        echo("\r\n".__line__." hb->IndentLength = {$indentLength}");
        // *****
        $hb->Begin("tr", $textState);
        foreach ($propertyNames as $propertyName)
        {
          if (array_key_exists($propertyName, $dataItem))
          {
            $hb->Create("th", $textState, $propertyName);
          }
        }
        $hb->End("tr", $textState);
        $retValue = $hb->ToString();
      }
      return $retValue;
    }

    // Create the Array data table rows.
    private static function ArrayRow(array $dataItem, LJCTextState $textState
      , array $propertyNames)
    {
      $retValue = null;

      if (LJCCommon::HasElements($dataItem))
      {
        $hb = new LJCHTMLBuilder($textState);
        $hb->Begin("tr", $textState);
        foreach ($propertyNames as $propertyName)
        {
          if (array_key_exists($propertyName, $dataItem))
          {
            $value = $dataItem[$propertyName];
            $hb->Create("td", $textState, strval($value));
          }
        }
        $hb->End("tr", $textState);
        $retValue = $hb->ToString();
      }
      return $retValue;
    }

    // Check if maximum rows has been retrieved.
    private static function EndRetrieve(int $maxRows, int &$count)
    {
      $retValue = false;

      $count++;
      if ($maxRows > 0
        && $count > $maxRows)
      {
        $retValue = true;
      }
      return $retValue;
    }

    // Create the Object heading table rows.
    private static function ObjectHeadings($dataItem
      , array $propertyNames, LJCTextState $textState): string
    {
      $retValue = null;

      if ($dataItem != null)
      {
        $hb = new LJCHTMLBuilder($textState);
        $hb->Begin("tr", $textState);
        foreach ($propertyNames as $propertyName)
        {
          if (property_exists($dataItem, $propertyName))
          {
            $hb->Create("th", $textState, $propertyName);
          }
        }
        $hb->End("tr", $textState);
        $retValue = $hb->ToString();
      }
      return $retValue;
    }

    // Create the Object data table rows.
    private static function ObjectRow($dataItem, array $propertyNames
      , LJCTextState $textState)
    {
      if ($dataItem != null)
      {
        $hb = new LJCHTMLBuilder($textState);
        $hb->Begin("tr", $textState);
        foreach ($propertyNames as $propertyName)
        {
          if (property_exists($dataItem, $propertyName))
          {
            // Using variable name for object property.
            $value = $dataItem->$propertyName;
            $hb->Create("td", $textState, strval($value));
          }
        }
        $hb->End("tr", $textState);
        $retValue = $hb->ToString();
      }
      return $retValue;
    }
  }
?>
