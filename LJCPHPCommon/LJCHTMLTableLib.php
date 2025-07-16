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
  // LJCHTMLBuilderLib: LJCAttribute, LJCAttributes, LJCHTMLBuilder

  /// <summary>The HTML Object Table Class Library</summary>
  /// LibName: LJCHTMLTableLib
  //  Classes: LJCHTMLTable

  // ********************
  // Public:
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
    // Initializes a class instance with the provided values.
    /// <include path='items/construct/*' file='Doc/LJCDataManager.xml'/>
    public function __construct()
    {
      $this->MaxRows = 0;
      $this->ColumnNames = [];
      $this->DataAttribs = new LJCAttributes();
      $this->HeadingAttribs = new LJCAttributes();
      $this->TableAttribs = new LJCAttributes();
    } // __construct()

    // ----------
    // ArrayArray Functions

    // Create table headings from an ArrayArray Data Object.
    /// <include path='items/ArrayArrayHeadings/*' file='Doc/LJCHTMLTable.xml'/>
    /// <ParentGroup>Arrays</ParentGroup>
    public function ArrayArrayHeadings(array $dataItems
      , LJCTextState $textState): string
    {
      $retValue = null;

      if (LJC::HasElements($dataItems))
      {
        $dataItem = $dataItems[0];
        $retValue = $this->ArrayHeadings($dataItem, $textState);
      }
      return $retValue;
    }

    // Create an HTML table from an ArrayArray Data Object.
    /// <include path='items/ArrayArrayHeadings/*' file='Doc/LJCHTMLTable.xml'/>
    /// <ParentGroup>Arrays</ParentGroup>
    public function ArrayArrayHTML(array $dataItems, LJCTextState $textState)
      : string
    {
      $retValue = null;

      if (LJC::HasItems($dataItems))
      {
        $hb = new LJCHTMLBuilder($textState);
        $hb->Begin("table", $textState, $this->TableAttribs);
        $text = $this->ArrayArrayHeadings($dataItems, $textState);
        $hb->Text($text, false);
        $text = $this->ArrayArrayRows($dataItems, $textState);
        $hb->Text($text, false);
        $hb->End("table", $textState);
        $retValue = $hb->ToString();
      }
      return $retValue;
    }

    /// <summary>Create table rows from an ArrayArray Data Object.</summary>
    /// <include path='items/ArrayArrayRows/*' file='Doc/LJCHTMLTable.xml'/>
    /// <ParentGroup>Arrays</ParentGroup>
    public function ArrayArrayRows(array $dataItems, LJCTextState $textState)
      : string
    {
      $retValue = null;

      if (LJC::HasElements($dataItems))
      {
        $hb = new LJCHTMLBuilder($textState);

        $count = 0;
        foreach ($dataItems as $dataItem)
        {
          if (self::EndRetrieve($count))
          {
            break;
          }
          $text = $this->ArrayRow($dataItem, $textState);
          $hb->Text($text, false);
        }
        $retValue = $hb->ToString();
      }
      return $retValue;
    }

    // ----------
    // Collection Functions

    // Create table headings from a Collection Object.
    /// <include path='items/CollectionHeadings/*' file='Doc/LJCHTMLTable.xml'/>
    /// <ParentGroup>Collection</ParentGroup>
    public function CollectionHeadings(LJCCollectionBase $dataItems
      , LJCTextState $textState): string
    {
      $retValue = null;

      if (LJC::HasItems($dataItems))
      {
        $dataItem = $dataItems->Item(0);
        $retValue = $this->ObjectHeadings($dataItem, $textState);
      }
      return $retValue;
    }

    // Create an HTML table from an Collection Object.
    /// <include path='items/CollectionHTML/*' file='Doc/LJCHTMLTable.xml'/>
    /// <ParentGroup>Collection</ParentGroup>
    public function CollectionHTML(LJCCollectionBase $dataItems
      , LJCTextState $textState): string
    {
      $retValue = null;

      if (LJC::HasItems($dataItems))
      {
        $hb = new LJCHTMLBuilder($textState);
        $hb->Begin("table", $textState, $this->TableAttribs);
        $text = $this->CollectionHeadings($dataItems, $textState);
        $hb->Text($text, false);
        $text = $this->CollectionRows($dataItems, $textState);
        $hb->Text($text, false);
        $hb->End("table", $textState);
        $retValue = $hb->ToString();
      }
      return $retValue;
    }

    // Create table rows from a Collection Object.
    /// <include path='items/CollectionRows/*' file='Doc/LJCHTMLTable.xml'/>
    /// <ParentGroup>Collection</ParentGroup>
    public function CollectionRows(LJCCollectionBase $dataItems
      , LJCTextState $textState): string
    {
      $retValue = null;

      if (LJC::HasItems($dataItems))
      {
        $hb = new LJCHTMLBuilder($textState);

        $count = 0;
        foreach ($dataItems as $dataItem)
        {
          if (self::EndRetrieve($count))
          {
            break;
          }
          $text = $this->ObjectRow($dataItem, $textState);
          $hb->Text($text, false);
        }
        $retValue = $hb->ToString();
      }
      return $retValue;
    }

    // ----------
    // ObjectArray Functions

    // Create table headings from an ObjectArray Data Object.
    /// <include path='items/ObjectArrayHeadings/*' file='Doc/LJCHTMLTable.xml'/>
    /// <ParentGroup>Objects</ParentGroup>
    public function ObjectArrayHeadings(array $dataItems
      , LJCTextState $textState): string
    {
      $retValue = null;

      if (LJC::HasElements($dataItems))
      {
        $dataItem = $dataItems[0];
        if ($dataItem != null)
        {
          $retValue = $this->ObjectHeadings($dataItem, $textState);
        }
      }
      return $retValue;
    }

    /// <summary>Create an HTML table from an ObjectArray Data Object.</summary>
    /// <include path='items/ObjectArrayHTML/*' file='Doc/LJCHTMLTable.xml'/>
    /// <ParentGroup>Objects</ParentGroup>
    public function ObjectArrayHTML(array $dataItems
      , LJCTextState $textState): string
    {
      $retValue = null;

      if (LJC::HasItems($dataItems))
      {
        $hb = new LJCHTMLBuilder($textState);
        $hb->Begin("table", $textState, $this->TableAttribs);
        $text = $this->ObjectArrayHeadings($dataItems, $textState);
        $hb->Text($text, false);
        $text = $this->ObjectArrayRows($dataItems, $textState);
        $hb->Text($text, false);
        $hb->End("table", $textState);
        $retValue = $hb->ToString();
      }
      return $retValue;
    }

    // Create table rows from an ObjectArray Data Object.
    /// <include path='items/ObjectArrayHTML/*' file='Doc/LJCHTMLTable.xml'/>
    /// <ParentGroup>Objects</ParentGroup>
    public function ObjectArrayRows(array $dataItems
      , LJCTextState $textState): string
    {
      $retValue = null;

      if (LJC::HasElements($dataItems))
      {
        $hb = new LJCHTMLBuilder($textState);

        $count = 0;
        foreach ($dataItems as $dataItem)
        {
          if (self::EndRetrieve($count))
          {
            break;
          }
          $text = $this->ObjectRow($dataItem, $textState);
          $hb->Text($text, false);
        }
        $retValue = $hb->ToString();
      }
      return $retValue;
    }

    // ----------
    // Data Table Functions

    // Create table headings from result rows.
    /// <include path='items/ResultHeadings/*' file='Doc/LJCHTMLTable.xml'/>
    /// <ParentGroup>Rows</ParentGroup>
    public function ResultHeadings(array $rows, LJCTextState $textState): string
    {
      $retValue = null;

      if (LJC::HasElements($rows))
      {
        $row = $rows[0];
        $retValue = $this->ArrayHeadings($row, $textState);
      }
      return $retValue;
    }

    // Create an HTML table from result rows.
    /// <include path='items/ResultHTML/*' file='Doc/LJCHTMLTable.xml'/>
    /// <ParentGroup>Rows</ParentGroup>
    public function ResultHTML(array $rows, LJCTextState $textState): string
    {
      $retValue = null;

      if (LJC::HasElements($rows))
      {
        $hb = new LJCHTMLBuilder($textState);
        $hb->Begin("table", $textState, $this->TableAttribs);
        $text = $this->ResultHeadings($rows, $textState);
        $hb->Text($text, false);
        $text = $this->ResultRows($rows, $textState);
        $hb->Text($text, false);
        $hb->End("table", $textState);
        $retValue = $hb->ToString();
      }
      return $retValue;
    }

    // Create table rows from result rows.
    /// <include path='items/ResultRows/*' file='Doc/LJCHTMLTable.xml'/>
    /// <ParentGroup>Rows</ParentGroup>
    public function ResultRows(array $rows, LJCTextState $textState): string
    {
      $retValue = null;

      if (LJC::HasElements($rows))
      {
        $hb = new LJCHTMLBuilder($textState);

        $count = 0;
        foreach ($rows as $row)
        {
          if (self::EndRetrieve($count))
          {
            break;
          }
          $text = $this->ArrayRow($row, $textState);
          $hb->Text($text, false);
        }
        $retValue = $hb->ToString();
      }
      return $retValue;
    }

    // --------------------
    // Support Methods

    // Create the Array heading table rows.
    private function ArrayHeadings(array $dataItem
      , LJCTextState $textState): string
    {
      $retValue = null;

      if (LJC::HasElements($dataItem))
      {
        $hb = new LJCHTMLBuilder($textState);
        $indentCount = strval($textState->IndentCount);
        $indentLength = strval($hb->IndentLength());
        $hb->Begin("tr", $textState);
        foreach ($this->ColumnNames as $propertyName)
        {
          if (array_key_exists($propertyName, $dataItem))
          {
            $hb->Create("th", $textState, $propertyName, $this->HeadingAttribs);
          }
        }
        $hb->End("tr", $textState);
        $retValue = $hb->ToString();
      }
      return $retValue;
    }

    // Create the Array data table rows.
    private function ArrayRow(array $dataItem, LJCTextState $textState)
    {
      $retValue = null;

      if (LJC::HasElements($dataItem))
      {
        $hb = new LJCHTMLBuilder($textState);
        $hb->Begin("tr", $textState);
        foreach ($this->ColumnNames as $propertyName)
        {
          if (array_key_exists($propertyName, $dataItem))
          {
            $value = $dataItem[$propertyName];
            $hb->Create("td", $textState, strval($value), $this->DataAttribs);
          }
        }
        $hb->End("tr", $textState);
        $retValue = $hb->ToString();
      }
      return $retValue;
    }

    // Check if maximum rows has been retrieved.
    private function EndRetrieve(int &$count)
    {
      $retValue = false;

      $count++;
      if ($this->MaxRows > 0
        && $count > $this->MaxRows)
      {
        $retValue = true;
      }
      return $retValue;
    }

    // Create the Object heading table rows.
    private function ObjectHeadings($dataItem, LJCTextState $textState): string
    {
      $retValue = null;

      if ($dataItem != null)
      {
        $hb = new LJCHTMLBuilder($textState);
        $hb->Begin("tr", $textState);
        foreach ($this->ColumnNames as $propertyName)
        {
          if (property_exists($dataItem, $propertyName))
          {
            $hb->Create("th", $textState, $propertyName, $this->HeadingAttribs);
          }
        }
        $hb->End("tr", $textState);
        $retValue = $hb->ToString();
      }
      return $retValue;
    }

    // Create the Object data table rows.
    private function ObjectRow($dataItem, LJCTextState $textState)
    {
      if ($dataItem != null)
      {
        $hb = new LJCHTMLBuilder($textState);
        $hb->Begin("tr", $textState);
        foreach ($this->ColumnNames as $propertyName)
        {
          if (property_exists($dataItem, $propertyName))
          {
            // Using variable name for object property.
            $value = strval($dataItem->$propertyName);
            $hb->Create("td", $textState, $value, $this->DataAttribs);
          }
        }
        $hb->End("tr", $textState);
        $retValue = $hb->ToString();
      }
      return $retValue;
    }

    // --------------------
    // Properties

    // The table column property names.
    public array $ColumnNames;

    // The heading attributes.
    public LJCAttributes $HeadingAttribs;

    // The maximum display rows.
    public int $MaxRows;

    // The row attributes.
    public LJCAttributes $DataAttribs;

    // The table attributes.
    public LJCAttributes $TableAttribs;
  }
?>
