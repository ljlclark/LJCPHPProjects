<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCHTMLTableLib.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/LJCPHPCommon/LJCCommonLib.php";
  include_once "$prefix/LJCPHPCommon/LJCTextBuilderLib.php";
  // LJCCommonLib: LJCCommon
  // LJCTextBuilderLib: LJCAttribute, LJCAttributes, LJCTextBuilder

  /// <summary>The HTML Object Table Class Library</summary>
  /// LibName: LJCHTMLTableLib
  //  Classes: LJCHTMLTable

  // ********************
  /// <summary>Provides methods to create an object HTML Table.</summary>
  /// <group name="Collection">Collection</group>
  //    CollectionHeadings(), CollectionHTML(), CollectionRows()
  /// <group name="Objects">Array of Objects</group>
  //    ObjectArrayHeadings(), ObjectArrayHTML(), ObjectArrayRows()
  /// <group name="Rows">Array of Rows</group>
  //    ResultHeadings(), ResultHTML(), ResultRows()
  class LJCHTMLTable
  {
    // Initializes a class instance with the provided values.
    /// <include path='items/construct/*' file='Doc/LJCDataManager.xml'/>
    public function __construct()
    {
      $this->ClassName = "LJCHTMLTable";
      $this->DebugText = "";

      $this->MaxRows = 0;
      $this->ColumnNames = [];
      $this->DataAttribs = new LJCAttributes();
      $this->HeadingAttribs = new LJCAttributes();
      $this->TableAttribs = new LJCAttributes();
    } // __construct()

    // Standard debug method for each class.
    private function AddLogText($methodName, $valueName, $value = null)
    {
      $location = LJC::Location($this->ClassName, $methodName
        , $valueName);
      $this->DebugText .= LJC::DebugObject($location, $value);
    } // AddLogText()

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
        $dataItem = $dataItems->RetrieveWithIndex(0);
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
        $tb = new LJCTextBuilder($textState);
        $tb->Begin("table", $textState, $this->TableAttribs);
        $text = $this->CollectionHeadings($dataItems, $textState);
        $tb->Text($text, false);
        $text = $this->CollectionRows($dataItems, $textState);
        $tb->Text($text, false);
        $tb->End("table", $textState);
        $retValue = $tb->ToString();
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
        $tb = new LJCTextBuilder($textState);

        $count = 0;
        foreach ($dataItems as $dataItem)
        {
          if (self::EndRetrieve($count))
          {
            break;
          }
          $text = $this->ObjectRow($dataItem, $textState);
          $tb->Text($text, false);
        }
        $retValue = $tb->ToString();
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
        $tb = new LJCTextBuilder($textState);
        $tb->Begin("table", $textState, $this->TableAttribs);
        $text = $this->ObjectArrayHeadings($dataItems, $textState);
        $tb->Text($text, false);
        $text = $this->ObjectArrayRows($dataItems, $textState);
        $tb->Text($text, false);
        $tb->End("table", $textState);
        $retValue = $tb->ToString();
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
        $tb = new LJCTextBuilder($textState);

        $count = 0;
        foreach ($dataItems as $dataItem)
        {
          if (self::EndRetrieve($count))
          {
            break;
          }
          $text = $this->ObjectRow($dataItem, $textState);
          $tb->Text($text, false);
        }
        $retValue = $tb->ToString();
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
      $methodName = "ResultHTML()";
      $retValue = null;

      if (LJC::HasElements($rows))
      {
        $tb = new LJCTextBuilder($textState);
        $tb->Begin("table", $textState, $this->TableAttribs);
        $text = $this->ResultHeadings($rows, $textState);
        $tb->Text($text, false);
        $text = $this->ResultRows($rows, $textState);
        $tb->Text($text, false);
        $tb->End("table", $textState);
        $this->DebugText .= $tb->LogText;
        $retValue = $tb->ToString();
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
        $tb = new LJCTextBuilder($textState);

        $count = 0;
        foreach ($rows as $row)
        {
          if (self::EndRetrieve($count))
          {
            break;
          }
          $text = $this->ArrayRow($row, $textState);
          $tb->Text($text, false);
        }
        $retValue = $tb->ToString();
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
        $tb = new LJCTextBuilder($textState);
        $indentCount = strval($textState->getIndentCount());
        $indentLength = strval($tb->IndentLength());
        $tb->Begin("tr", $textState);
        foreach ($this->ColumnNames as $propertyName)
        {
          if (array_key_exists($propertyName, $dataItem))
          {
            $tb->Create("th", $textState, $propertyName, $this->HeadingAttribs);
          }
        }
        $tb->End("tr", $textState);
        $retValue = $tb->ToString();
      }
      return $retValue;
    }

    // Create the Array data table rows.
    private function ArrayRow(array $dataItem, LJCTextState $textState)
    {
      $retValue = null;

      if (LJC::HasElements($dataItem))
      {
        $tb = new LJCTextBuilder($textState);
        $tb->Begin("tr", $textState);
        foreach ($this->ColumnNames as $propertyName)
        {
          if (array_key_exists($propertyName, $dataItem))
          {
            $value = $dataItem[$propertyName];
            $tb->Create("td", $textState, strval($value), $this->DataAttribs);
          }
        }
        $tb->End("tr", $textState);
        $retValue = $tb->ToString();
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
        $tb = new LJCTextBuilder($textState);
        $tb->Begin("tr", $textState);
        foreach ($this->ColumnNames as $propertyName)
        {
          if (property_exists($dataItem, $propertyName))
          {
            $tb->Create("th", $textState, $propertyName, $this->HeadingAttribs);
          }
        }
        $tb->End("tr", $textState);
        $retValue = $tb->ToString();
      }
      return $retValue;
    }

    // Create the Object data table rows.
    private function ObjectRow($dataItem, LJCTextState $textState)
    {
      if ($dataItem != null)
      {
        $tb = new LJCTextBuilder($textState);
        $tb->Begin("tr", $textState);
        foreach ($this->ColumnNames as $propertyName)
        {
          if (property_exists($dataItem, $propertyName))
          {
            // Using variable name for object property.
            $value = strval($dataItem->$propertyName);
            $tb->Create("td", $textState, $value, $this->DataAttribs);
          }
        }
        $tb->End("tr", $textState);
        $retValue = $tb->ToString();
      }
      return $retValue;
    }

    // --------------------
    // Properties
    
    /// <summary>The class name for debugging.</summary>
    public string $ClassName;

    // The table column property names.
    public array $ColumnNames;

    /// <summary>The debug text.</summary>
    public string $DebugText;

    // The heading attributes.
    public LJCAttributes $HeadingAttribs;

    // The maximum display rows.
    public int $MaxRows;

    // The row attributes.
    public LJCAttributes $DataAttribs;

    // The table attributes.
    public LJCAttributes $TableAttribs;
  }
