<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // TestHTMLWriter.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/LJCPHPCommon/LJCTextLib.php";
  // LJCTextLib: LJCHTMLTableColumn, LJCHTMLWriter

  /// <summary>The Common PHP Class Library</summary>
  /// LibName: TestHTMLWriter

  $writer = new LJCHTMLWriter();
  
  echo("<table>\r\n");

  $headerColumns = [];
  $headerColumn = new LJCHTMLTableColumn("First", "One"
    , "background-color: blue", "200");
  $headerColumns[] = $headerColumn;
  $headerColumn = new LJCHTMLTableColumn("Second", "Two", width: "100");
  $headerColumns[] = $headerColumn;
  $writer->WriteHeader($headerColumns);

  $rowColumns = [];
  $rowColumn = new LJCHTMLTableColumn("First");
  $rowColumns[] = $rowColumn;
  $rowColumn = new LJCHTMLTableColumn("Second");
  $rowColumns[] = $rowColumn;

  $testData = new TestData("Les", "Clark");
  $writer->WriteRow($testData, $rowColumns);
  $testData = new TestData("Another", "Tawo");
  $writer->WriteRow($testData, $rowColumns);
  echo("</table>");

  class TestData
  {
    public function __construct(string $first = null, string $second = null)
    {
      $this->First = $first;
      $this->Second = $second;
    }
  }
?>
