<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // CityListTable.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/LJCPHPCommon/LJCDBAccessLib.php";
  include_once "$prefix/LJCPHPCommon/LJCTextLib.php";
  include_once "$prefix/RegionApp/City/CityDAL.php";
  // LJCDBAccessLib: LJCConnectionValues
  // LJCTextLib: LJCHTMLTableColumn, LJCHTMLWriter, LJCWriter
  // CityDAL: Cities, City, CityManager

  // ***************
  /// <summary>Contains methods to create the CityList HTML table.</summary>
  class CityListTable
  {
    // The Constructor function.
    /// <include path='items/construct/*' file='Doc/CityTable/CityTable.xml'/>
    public function __construct($connectionValues, string $tableName)
    {
      $this->ConnectionValues = $connectionValues;
      $this->TableName = $tableName;
    }

    // ---------------
    // Public Methods

    /// <summary>Creates the HTML table.</summary>
    public function CreateHTMLTable(): void
    {
      // CityDAL.php
      $cityManager = new CityManager($this->ConnectionValues, $this->TableName);
      $cityManager->OrderByNames = array("ProvinceID", "Name");
      $cities = $cityManager->Load(null);
      if ($cities != null)
      {
        $columns = self::CityColumns();
        // LJCTextLib.php
        LJCWriter::WriteLine("<table id='cityTable'",1);
        LJCWriter::WriteLine(" style='margin: auto'>", 1);

        // LJCTextLib.php
        LJCHTMLWriter::WriteHeader($columns);
        $columns[1]->Width = null;

        LJCWriter::WriteLine("<tbody>", 1);
        foreach ($cities as $city)
        {
          LJCHTMLWriter::WriteRow($city, $columns);
        }
        LJCWriter::WriteLine("</tbody>", 1);
        LJCWriter::WriteLine("</table>", 1);
      }
    }

    // ---------------
    // Private Functions

    // Define the HTML Table columns.
    // *** Change ***
    private static function CityColumns(): array
    {
      $retValue = array(); // []
      // LJCTextLib.php
      $retValue[] = new LJCHTMLTableColumn("CityID", style: "display: none");
      $retValue[] = new LJCHTMLTableColumn("Name", width: "30%");
      $retValue[] = new LJCHTMLTableColumn("Description", width: "50%");
      return $retValue;
    }

    // ---------------
    // Public Properties

    /// <summary>The connection values.</summary>
    public LJCConnectionValues $ConnectionValues;

    /// <summary>The table name value.</summary>
    public string $tableName;
  }
?>