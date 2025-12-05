<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCCollectionLib.php
  declare(strict_types=1);

  /// <summary>Contains Classes to represent a Collection.</summary>
  /// LibName: LJCCollectionLib
  // Classes: LJCCollectionBase

  // ***************
  // Represents a Collection of objects.
  /// <group name="Data">Data Methods</group>
  //    AddItem(), ClearItems(), DeleteItem(), RetrieveIndex(), RetrieveItem()
  /// <group name="Other">Other Methods</group>
  //    GetKeys(), GetValues(), HasKey(), count()
  /// <include path='items/LJCCollectionBase/*' file='Doc/LJCCollectionBase.xml'/>
  class LJCCollectionBase implements IteratorAggregate, Countable
  {
    // ---------------
    // Data Methods

    // Adds an object and key value.
    /// <include path='items/AddItem/*' file='Doc/LJCCollectionBase.xml'/>
    /// <ParentGroup>Data</ParentGroup>
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

    /// <summary>Clears the collection items.</summary>
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
    /// <include path='items/RetrieveIndex/*' file='Doc/LJCCollectionBase.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    public function RetrieveWithIndex($index)
    {
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
    // Other Methods

    /// <summary>Gets an indexed array of keys.</summary>
    /// <returns>The indexed keys array.</returns>
    /// <ParentGroup>Other</ParentGroup>
    public function GetKeys(): array
    {
      return array_keys($this->Items);
    } // GetKeys()

    /// <summary>Gets an indexed array of objects.</summary>
    /// <returns>The indexed values array.</returns>
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

    /// <summary>Allows Count(object).</summary>
    /// <returns>The element count.</returns>
    /// <ParentGroup>Other</ParentGroup>
    public function count(): int
    {
      return count($this->Items);
    } // count()

    // ----------------------
    // IteratorAggregate Implementation Methods - LJCCollectionBase

    /// <summary>Allows foreach()</summary>
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
