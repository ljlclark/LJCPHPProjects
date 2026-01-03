<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCCollectionLib.php
  declare(strict_types=1);

  // The Collection Class Library
  /// <include path='items/LJCCollectionLib/*' file='Doc/LJCCollectionBase.xml'/>
  
  // The "LibName:" XML comment triggers the file (library) HTML page
  // generation.
  // It generates a page with the same name as the library.
  // LJCCollectionLib.html
  /// LibName: LJCCollectionLib
  // Classes: LJCCollectionBase

  // ***************
  // Represents a Collection of objects.
  /// <include path='items/LJCCollectionBase/*' file='Doc/LJCCollectionBase.xml'/>
  /// <group name="Data">Data Methods</group>
  //    AddItem(), ClearItems(), DeleteItem(), RetrieveIndex(), RetrieveItem()
  /// <group name="Other">Other Methods</group>
  //    GetKeys(), GetValues(), HasKey(), count()

  // A class triggers the class HTML page generation.
  // It generates a page with the same name as the class.
  // LJCCollectionBase/LJCCollectionBase.html
  class LJCCollectionBase implements IteratorAggregate, Countable
  {
    // ---------------
    // Data Methods - LJCCollecionBase

    // Adds an object and key value.
    /// <include path='items/AddItem/*' file='Doc/LJCCollectionBase.xml'/>
    /// <ParentGroup>Data</ParentGroup>

    // A method triggers the method HTML page generation.
    // It generates a page with the name: class plus method.
    // LJCCollectionBase/LJCCollectionBaseAddItem.html
    protected function AddItem($item, $key = null)
    {
      $retValue = $item;

      if (null === $key)
      {
        $this->Items[] = $item;
      }
      else
      {
        //if ($this->HasKey($key))
        if (!$this->HasKey($key))
        {
          //throw new Exception("Key: {$key} already in use.");
          $this->Items[$key] = $item;
        }
        //$this->Items[$key] = $item;
      }
      return $retValue;
    } // AddItem()

    // Clears the collection items.
    /// <include path='items/AddItem/*' file='Doc/LJCCollectionBase.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    protected function ClearItems()
    {
      $this->Items = [];
    } // ClearItems()

    // Remove the item by Key value.
    /// <include path='items/DeleteItem/*' file='Doc/LJCCollectionBase.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    protected function DeleteItem($key, bool $throwError = true): void
    {
      $success = true;
      if (false == $this->HasKey($key))
      {
        $success = false;
        if ($throwError)
        {
          throw new Exception("Key: {$key} was not found.");
        }
      }
      if ($success)
      {
        unset($this->Items[$key]);
      }
    } // DeleteItem()

    // Retrieves the item by Key value.
    /// <include path='items/RetrieveItem/*' file='Doc/LJCCollectionBase.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    protected function RetrieveItem($key, bool $throwError = true)
    {
      $retValue = null;

      $success = true;
      if (false == $this->HasKey($key))
      {
        $success = false;
        if ($throwError)
        {
          throw new Exception("Key: '$key' was not found.");
        }
      }
      if ($success)
      {
        $retValue = $this->Items[$key];
      }
      return $retValue;
    } // RetrieveItem()

    // Retrieves the item by index.
    /// <include path='items/RetrieveItemAtIndex/*' file='Doc/LJCCollectionBase.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    public function RetrieveItemAtIndex($index)
    {
      //$output = new Output("LJCCollectionBase", "RetrieveItemAtIndex");
      $retItem = null;

      $keys = self::GetKeys();
      if (count($keys) > $index)
      {
        $key = $keys[$index];
        $retItem = $this->Items[$key];
      }
      return $retItem;
    } // Item()

    // ---------------
    // Other Methods - LJCCollecionBase

    // Gets an indexed array of keys.
    /// <include path='items/GetKeys/*' file='Doc/LJCCollectionBase.xml'/>
    /// <ParentGroup>Other</ParentGroup>
    public function GetKeys(): array
    {
      return array_keys($this->Items);
    } // GetKeys()

    // Gets an indexed array of objects.
    /// <include path='items/GetValues/*' file='Doc/LJCCollectionBase.xml'/>
    /// <ParentGroup>Other</ParentGroup>
    public function GetValues(): array
    {
      return array_values($this->Items);
    } // GetValues()

    // Indicates if a key already exists.
    /// <include path='items/HasKey/*' file='Doc/LJCCollectionBase.xml'/>
    /// <ParentGroup>Other</ParentGroup>
    public function HasKey($key): bool
    {
      //return array_key_exists($key, $this->Items);
      return isset($this->Items[$key]);
    } // HasKey()

    // ----------------------
    // Countable Implementation Methods - LJCCollectionBase

    // Enables count(object).
    /// <include path='items/count/*' file='Doc/LJCCollectionBase.xml'/>
    /// <ParentGroup>Other</ParentGroup>
    public function count(): int
    {
      return count($this->Items);
    } // count()

    // ----------------------
    // IteratorAggregate Implementation Methods - LJCCollectionBase

    // Enables foreach().
    /// <include path='items/getIterator/*' file='Doc/LJCCollectionBase.xml'/>
    /// <ParentGroup>Other</ParentGroup>
    public function getIterator(): Traversable
    {
      return new ArrayIterator($this->Items);
    } // getIterator()

    // ----------------------
    // ArrayAccess Implementation Methods - LJCCollectionBase

    //public function offsetSet($offset, $value): void
    //{
    // if (is_null($offset))
    //  {
    //    $this->Items[] = $value;
    //  } else
    //  {
    //    $this->Items[$offset] = $value;
    //  }
    //}

    //public function offsetExists($offset): bool
    //{
    //  return isset($this->Items[$offset]);
    //}

    //public function offsetUnset($offset): void
    //{
    //  unset($this->Items[$offset]);
    //}

    //public function offsetGet($offset): mixed
    //{
    //  return isset($this->Items[$offset]) ? $this->Items[$offset] : null;
    //}

    // ------------------
    // Class Data - LJCCollectionBase

    /// <summary>The elements array.</summary>
    protected array $Items = [];
  } // LJCCollectionBase
?>
