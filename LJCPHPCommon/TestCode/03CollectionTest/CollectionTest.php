<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // CollectionTest.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/LJCPHPCommon/LJCCommonLib.php";
  include_once "$prefix/LJCPHPCommon/LJCCollectionLib.php";
  include_once "$prefix/LJCPHPCommon/LJCTextBuilderLib.php";
  // LJCCommonLib: LJC
  // LJCCollectionLib: LJCCollectionBase

  /// <summary>The Common Test Class Library</summary>
  /// LibName: CommonTest

  class Name
  {
    public string $Name;
  }

  class Names extends LJCCollectionBase
  {
    // Adds a string.
    // This method makes a string collection strongly typed.
    public function Add(string $text)
    {
      $retItem = $this->AddItem($text);
      return $retItem;
    }

    // Adds an object and key value.
    // This method makes a collection strongly typed.
    // The key defaults to a unique object property.
    public function AddObject(Name $item, $key = null): Name
    {
      $retItem = $item;

      if ($item != null)
      {
        if (null == $key
          && property_exists($item, "Name")
          && $item->Name != null
          && strlen(trim($item->Name)) > 0)
        {
          $key = $item->Name;
        }
        $retItem = $this->AddItem($item, $key);
      }
      return $retItem;
    }

    // Clears the collection items.
    public function Clear()
    {
      $this->ClearItems();
    }

    // Remove the item by Key value.
    public function Remove($key, bool $throwError = true)
    {
      $this->DeleteItem($key, $throwError);
    }

    // Retrieves the item by Key value.
    public function Retrieve($key, bool $throwError = true)
    {
      $retItem = $this->RetrieveItem($key, $throwError);
      return $retItem;
    }

    // Retrieves the item by index.
    public function RetrieveAtIndex($index)
    {
      $retItem = $this->RetrieveItemAtIndex($index);
      return $retItem;
    }

    public string $Name;
  }

  $testBuilder = new CollectionTest();
  $testBuilder->Run();

  // ********************
  /// <summary>The Collection Test Class</summary>
  class CollectionTest
  {
    /// <summary>Runs the Common tests.</summary>
    public static function Run()
    {
      // Setup static debug to output.
      $className = "CommonTest";
      $methodName = "Run()";

      echo("\r\n");
      echo("*** LJCCollection ***");

      // Data Methods
      self::AddItem();
      self::AddObject();
      self::ClearItems();
      self::DeleteItem();
      self::RetrieveItem();
      self::RetrieveItemAtIndex();

      // Other Methods
      self::GetKeys();
      self::GetValues();
      self::HasKey();
      self::count();
      self::getIterator();
    }

    // --------------------
    // Data Methods

    // Adds an object and index key value.
    private static function AddItem()
    {
      $names = new Names();
      $name = new Name();
      $names->Add("First");
      $names->Add("Second");
      $names->Add("Third");
      $item = "Fourth";
      $names->Add($item);
      $result = $names->Retrieve(3);

      $compare = "Fourth";
      LJC::OutputLogCompare("AddItem()", $result, $compare);
    }

    // Add a strongly typed object with associative key.
    private static function AddObject()
    {
      $names = new Names();
      $name = new Name();
      $name->Name = "One";
      $names->AddObject($name);
      $item = $names->Retrieve("One");
      $result = $item->Name;

      $compare = "One";
      LJC::OutputLogCompare("AddItem()", $result, $compare);
    }

    // Clears the collection items.
    private static function ClearItems()
    {
      $names = new Names();
      $names->Add("First");
      $names->Add("Second");
      $names->Add("Third");

      $names->Clear();
      $value = $names->count();
      $result = strval($value);

      $compare = "0";
      LJC::OutputLogCompare("ClearItems()", $result, $compare);
    }

    // Remove the item by Key value.
    private static function DeleteItem()
    {
      $names = new Names();
      $names->Add("First");
      $names->Add("Second");
      $names->Add("Third");

      $names->Remove(0);
      $result = $names->RetrieveItemAtIndex(0);

      $compare = "Second";
      LJC::OutputLogCompare("DeleteItem()", $result, $compare);
    }

    // Retrieves the item by Key value.
    private static function RetrieveItem()
    {
      $names = new Names();
      $names->Add("First");
      $names->Add("Second");
      $names->Add("Third");
      $result = $names->Retrieve(0);

      $compare = "First";
      LJC::OutputLogCompare("RetrieveItem()", $result, $compare);
    }

    // Retrieves the item by index.
    private static function RetrieveItemAtIndex()
    {
      $names = new Names();
      $names->Add("First");
      $names->Add("Second");
      $names->Add("Third");
      $result = $names->RetrieveAtIndex(0);

      $compare = "First";
      LJC::OutputLogCompare("RetrieveItemAtIndex()", $result, $compare);
    }

    // --------------------
    // Other Methods

    // Gets an indexed array of keys.
    private static function GetKeys()
    {
      $names = new Names();
      $names->Add("First");
      $names->Add("Second");
      $names->Add("Third");
      $values = $names->GetKeys();
      $result = join(",", $values);

      $compare = "0,1,2";      
      LJC::OutputLogCompare("GetKeys()", $result, $compare);
    }

    // Gets an indexed array of objects.
    private static function GetValues()
    {
      $names = new Names();
      $names->Add("First");
      $names->Add("Second");
      $names->Add("Third");
      $values = $names->GetValues();
      $result = join(",", $values);

      $compare = "First,Second,Third";      
      LJC::OutputLogCompare("GetValues()", $result, $compare);
    }

    // Indicates if a key already exists.
    private static function HasKey()
    {
      $names = new Names();
      $names->Add("First");
      $names->Add("Second");
      $names->Add("Third");
      $value = $names->HasKey(0);
      $result = $value ? "true" : "false";

      $compare = "true";
      LJC::OutputLogCompare("count()", $result, $compare);
    }

    // Allows count(object).
    private static function count()
    {
      $names = new Names();
      $names->Add("First");
      $names->Add("Second");
      $names->Add("Third");
      $value = count($names);
      $result = strval($value);

      $compare = "3";
      LJC::OutputLogCompare("count()", $result, $compare);
    }

    // Allows foreach().
    private static function getIterator()
    {
      $names = new Names();
      $names->Add("First");
      $names->Add("Second");
      $names->Add("Third");
      $result = "";
      foreach ($names as $name)
      {
        if (strlen($result) > 0)
        {
          $result .= ",";
        }
        $result .= $name;
      }

      $compare = "First,Second,Third";
      LJC::OutputLogCompare("getIterator()", $result, $compare);
    }
  }
