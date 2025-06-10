<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCHTMLObjectTableLib.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/LJCPHPCommon/LJCCommonLib.php";
  include_once "$prefix/LJCPHPCommon/LJCHTMLBuilderLib.php";
  // LJCCommonLib: LJCCommon
  // LJCHTMLBuilderLib: LJCHTMLBuilder

  // Contains methods to create an object HTML Table.
  class LJCHTMLObjectTable
  {
    // ----------
    // Methods

    // Create table headings from a Data Object.
    public static function DataHeadings($dataItems, array $propertyNames
      , LJCTextState $textState): string
    {
      $retValue = null;

      if (LJCCommon::HasItems($dataItems))
      {
        $hb = new LJCHTMLBuilder($textState);

        $hb->Begin("tr", $textState);
        $dataItem = self::GetFirstItem($dataItems);

        foreach ($propertyNames as $propertyName)
        {
          if (is_array($dataItem))
          {
            if (array_key_exists($propertyName, $dataItem))
            {
              $hb->Create("th", $propertyName, $textState);
            }
          }
          else
          {
            if (property_exists($dataItem, $propertyName))
            {
              $hb->Create("th", $propertyName, $textState);
            }
          }
        }
        $hb->End("tr", $textState);
        $retValue = $hb->ToString();
      }
      return $retValue;
    }

    // Create an HTML table from a Data Object.
    public static function DataHTML($dataItems, array $propertyNames
      , LJCTextState $textState, int $maxRows = 0): string
    {
      $retValue = null;

      if (LJCCommon::HasItems($dataItems))
      {
        $hb = new LJCHTMLBuilder($textState);

        $attribs = $hb->TableAttribs();
        $hb->Begin("table", $textState, $attribs);
        $text = self::DataHeadings($dataItems, $propertyNames, $textState);
        $hb->Text($text, false);
        $text = self::DataRows($dataItems, $propertyNames, $textState
          , $maxRows);
        $hb->Text($text, false);
        $hb->End("table", $textState);
        $retValue = $hb->ToString();
      }
      return $retValue;
    }

    // Create table rows from a Data Object.
    public static function DataRows($dataItems, array $propertyNames
      , LJCTextState $textState, int $maxRows = 0): string
    {
      $retValue = null;

      if (LJCCommon::HasItems($dataItems))
      {
        $hb = new LJCHTMLBuilder($textState);

        $count = 0;
        foreach ($dataItems as $dataItem)
        {
          $count++;
          if ($maxRows > 0
            && $count > $maxRows)
          {
            break;
          }
          $hb->Begin("tr", $textState);
          foreach ($propertyNames as $propertyName)
          {
            if (is_array($dataItem))
            {
              if (array_key_exists($propertyName, $dataItem))
              {
                $value = $dataItem[$propertyName];
                $hb->Create("td", $value, $textState);
              }
            }
            else
            {
              if (property_exists($dataItem, $propertyName))
              {
                // Using variable name for object property.
                $value = $dataItem->$propertyName;
                $hb->Create("td", $value, $textState);
              }
            }
          }
          $hb->End("tr", $textState);
        }
        $retValue = $hb->ToString();
      }
      return $retValue;
    }

    // Gets the first array or LJCCollectionLib item.
    private static function GetFirstItem($dataItems)
    {
      $retItem = null;

      if (is_array($dataItems))
      {
        $retItem = $dataItems[0];
      }
      else
      {
        $keys = $dataItems->GetKeys();
        $retItem = $dataItems->Retrieve($keys[0]);
      }
      return $retItem;
    }
  }
?>
