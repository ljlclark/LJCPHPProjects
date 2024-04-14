<?php
  // Copyright(c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCCollectionLib.php
  declare(strict_types=1);

  /// <summary>Contains Classes to represent a Collection.</summary>
  /// LibName: LJCCollectionLib

  // ***************
  // Represents a Collection of objects.
  /// <include path='items/LJCCollectionBase/*' file='Doc/LJCCollectionBase.xml'/>
  class LJCCollectionBase implements IteratorAggregate, Countable
  {
    // ---------------
    // Public Methods

    // Adds an object and key value.
    /// <include path='items/AddItem/*' file='Doc/LJCCollectionBase.xml'/>
    protected function Add($item, $key = null)
    {
      $retValue = $item;

      if (null === $key)
      {
        $this->Items[] = $item;
      }
      else
      {
        if ($this->HasKey($key))
        {
          throw new Exception("Key: {$key} already in use.");
        }
        $this->Items[$key] = $item;
      }
      return $retValue;
    }

    // Adds an object and key value.
    // Obsolete: Use Add().
    protected function AddItem($item, $key = null)
    {
      return Add($item, $key);
    }

    // Remove the item by Key value.
    /// <include path='items/Remove/*' file='Doc/LJCCollectionBase.xml'/>
    public function Delete($key, bool $throwError = true) : void
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
    }

    // Get the item by Key value.
    /// <include path='items/GetItem/*' file='Doc/LJCCollectionBase.xml'/>
    protected function GetItem($key, bool $throwError = true)
    {
      return Retrieve($key, $throwError);
    }

    /// <summary>Gets an indexed array of keys.</summary>
    /// <returns>The indexed keys array.</returns>
    public function GetKeys() : array
    {
      return array_keys($this->Items);
    }

    /// <summary>Gets an indexed array of objects.</summary>
    /// <returns>The indexed values array.</returns>
    public function GetValues() : array
    {
      return array_values($this->Items);
    }

    // Indicates if a key already exists.
    /// <include path='items/HasKey/*' file='Doc/LJCCollectionBase.xml'/>
    public function HasKey($key) : bool
    {
      //return array_key_exists($key, $this->Items);
      return isset($this->Items[$key]);
    }

    // Remove the item by Key value.
    // Obsolete: Use Delete().
    public function Remove($key, bool $throwError = true) : void
    {
      Delete($key, $throwError);
    }

    // Get the item by Key value.
    /// <include path='items/GetItem/*' file='Doc/LJCCollectionBase.xml'/>
    protected function Retrieve($key, bool $throwError = true)
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
    }

    // ----------------------
    // Implementation Methods

    /// <summary>Allows Count(object).</summary>
    /// <returns>The element count.</returns>
    public function count() : int
    {
      return count($this->Items);
    }

    /// <summary>Allows foreach()</summary>
    public function getIterator() : Traversable
    {
      return new ArrayIterator($this->Items);
    }

    // ------------------
    // Class Data

    /// <summary>The elements array.</summary>
    protected array $Items = [];
  }
?>