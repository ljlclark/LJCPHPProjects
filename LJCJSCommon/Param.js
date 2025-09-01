// Copyright(c) Lester J. Clark 2022 - All Rights Reserved
// Param.js

// ***************
// Represents a Parameter.
class Param
{
  Name = null;
  Value = null;

  // The Constructor function.
  constructor(name, value)
  {
    this.Name = name;
    this.Value = value;
  }

  // Clones this object.
  Clone()
  {
    return new Param(this.Name, this.Value);
  }
}
