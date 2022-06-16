<?php
	// Copyright (c) Lester J. Clark 2022 - All Rights Reserved
	// LJCDocDataLib.php
	declare(strict_types=1);
	$webCommonPath = "c:/inetpub/wwwroot/LJCPHPCommon";
	$devPath = "c:/Users/Les/Documents/Visual Studio 2022/LJCPHPProjects";
	require_once "$devPath/LJCPHPCommon/LJCCommonLib.php";
	require_once "$devPath/LJCPHPCommon/LJCTextLib.php";
	require_once "$devPath/LJCPHPCommon/LJCCollectionLib.php";

	// Contains Classes to represent DocData.
	/// <include path='items/LJCDocDataLib/*' file='Doc/LJCDocDataLib.xml'/>
	/// LibName: LJCDocDataLib

  // Main Object Graph
	// LJCDocDataFile
	//   LJCDocDataClasses
	//     LJCDocDataClass
	//       LJCDocDataMethods
	//         LJCDocDataMethod
	//           LJCDocDataParams
	//             LJCDocDataParam
	//       LJCDocDataProperties
	//         LJCDocDataProperty
	//   LJCDocDataMethods

	// ***************
	/// <summary>Represents a DocData Class.</summary>
	class LJCDocDataClass
	{
		// ---------------
		// Constructors

		// Initializes a class instance.
		/// <include path='items/construct/*' file='Doc/LJCDocDataClass.xml'/>
		public function __construct(string $name, ?string $summary = null)
		{
			$this->Code = null;
			$this->Methods = null;
			$this->Name = $name;
			$this->Properties = null;
			$this->Remarks = null;
			$this->Summary = $summary;
		}

		// ---------------
		// Public Methods

		// Creates a Clone of the current object.
		/// <include path='items/Clone/*' file='Doc/LJCDocDataClass.xml'/>
		public function Clone() : self
		{
			$retValue = new self($this->Name, $this->Summary);
			$retValue->Code = $this->Code;
			$retValue->Methods = $this->Methods;
			$retValue->Properties = $this->Properties;
			$retValue->Remarks = $this->Remarks;
			return $retValue;
		}

		// ---------------
		// Public Properties - LJCDocDataClass

		/// <summary>The Code value.</summary>
		public ?string $Code;

		/// <summary>The Method array.</summary>
		public ?LJCDocDataMethods $Methods;

		/// <summary>The Name value.</summary>
		public ?string $Name;

		/// <summary>The Property array.</summary>
		public ?LJCDocDataProperties $Properties;

		/// <summary>The Remarks value.</summary>
		public ?string $Remarks;

		/// <summary>The Summary value.</summary>
		public ?string $Summary;
	}  // LJCDocDataClass

	// ***************
	/// <summary>Represents a collection of objects.</summary>
	class LJCDocDataClasses extends LJCCollectionBase
	{
		// ---------------
		// Public Methods

		// Adds an object and key value.
		/// <include path='items/AddObject/*' file='../../CommonDoc/PHPCollection.xml'/>
		public function AddObject(LJCDocDataClass $item, $key = null)
			: ?LJCDocDataClass
		{
			if (null == $key)
			{
				$key = $item->ID;
			}
			$retValue = $this->AddItem($item, $key);
			return $retValue;
		}

		/// <summary>Creates an object clone.</summary>
		public function Clone() : self
		{
			$retValue = new self();
			foreach ($this->Items as $key => $item)
			{
				$retValue->AddObject($item);
			}
			unset($item);
			return $retValue;
		}

		// Get the item by Key value.
		/// <include path='items/Get/*' file='../../CommonDoc/PHPCollection.xml'/>
		public function Get($key, bool $throwError = true) : ?LJCDocDataClass
		{
			$retValue = $this->GetItem($key, $throwError);
			return $retValue;
		}
	}  // LJCDocDataClasses

	// ***************
	// Represents a DocData Lib File.
	/// <include path='items/LJCDocDataFile/*' file='Doc/LJCDocDataFile.xml'/>
	class LJCDocDataFile
	{
		// ---------------
		// Static Functions

		// Deserializes the data from an LJCDocDataFile XML file.
		/// <include path='items/Deserialize/*' file='Doc/LJCDocDataFile.xml'/>
		public static function Deserialize(string $xmlFileSpec) : LJCDocDataFile
		{
			$retValue = null;

			$docNode = simplexml_load_file($xmlFileSpec);
			$retValue = self::CreateDocDataFile($docNode);
			return $retValue;
		}

		// Deserializes the data from an LJCDocDataFile XML string.
		/// <include path='items/DeserializeString/*' file='Doc/LJCDocDataFile.xml'/>
		public static function DeserializeString(string $xmlString) : LJCDocDataFile
		{
			$retValue = null;

			$docNode = simplexml_load_string($xmlString);
			$retValue = self::CreateDocDataFile($docNode);
			return $retValue;
		}

		// Creates the LJCDocDataFile object.
		private static function CreateDocDataFile(SimpleXMLElement $xmlNode)
			: ?LJCDocDataFile
		{
			$retValue = null;

			if ($xmlNode != null)
			{
				$name = self::Value($xmlNode->Name);
				$retValue = new LJCDocDataFile($name);
				$retValue->Classes = self::GetClasses($xmlNode);
				$retValue->Remarks = self::Value($xmlNode->Remarks);
				$retValue->Summary = self::Value($xmlNode->Summary);
			}
			return $retValue;
		}

		// Deserialize Classes from the Doc node.
		private static function GetClasses(SimpleXMLElement $docNode)
			: ?LJCDocDataClasses
		{
			$retValue = null;

			$classNodes = self::GetClassNodes($docNode);
			if ($classNodes != null)
			{
				$retValue = new LJCDocDataClasses();
				foreach ($classNodes as $classNode)
				{
					$name = self::Value($classNode->Name);
					$class = new LJCDocDataClass($name);
					$retValue->AddObject($class, $name);
					$class->Summary = self::Value($classNode->Summary);
					$class->Remarks = self::Value($classNode->Remarks);
					$class->Methods = self::GetMethods($classNode);
					$class->Properties = self::GetProperties($classNode);
					$class->Code = self::Value($classNode->Code);
				}
			}
			return $retValue;
		}

		// Deserialize Methods from the Class node.
		private static function GetMethods(SimpleXMLElement $classNode)
			: ?LJCDocDataMethods
		{
			$retValue = null;

			$methodNodes = self::GetMethodNodes($classNode);
			if ($methodNodes != null)
			{
				$retValue = new LJCDocDataMethods();
				foreach ($methodNodes as $methodNode)
				{
					$name = self::Value($methodNode->Name);
					$method = new LJCDocDataMethod($name);
					$retValue->AddObject($method, $name);
					$method->Summary = self::Value($methodNode->Summary);
					$method->Params = self::GetParams($methodNode);
					$method->Returns = self::Value($methodNode->Returns);
					$method->Remarks = self::Value($methodNode->Remarks);
					// *** Next Statement *** Add
					$method->Syntax = self::Value($methodNode->Syntax);
					$method->Code = self::Value($methodNode->Code);
				}
			}
			return $retValue;
		}

		// Deserialize Params from the Method node.
		private static function GetParams(SimpleXMLElement $methodNode)
			: ?LJCDocDataParams
		{
			$retValue = null;

			$paramNodes = self::GetParamNodes($methodNode);
			if ($paramNodes != null)
			{
				$retValue = new LJCDocDataParams();
				foreach ($paramNodes as $paramNode)
				{
					$name = self::Value($paramNode->Name);
					$summary = self::Value($paramNode->Summary);
					$param = new LJCDocDataParam($name, $summary);
					$retValue->AddObject($param, $name);
				}
			}
			return $retValue;
		}

		// Deserialize Properties from the Class node.
		/// <include path='items/GetProperties/*' file='Doc/LJCDocDataFile.xml'/>
		public static function GetProperties(SimpleXMLElement $classNode)
			: ?LJCDocDataProperties
		{
			$retValue = null;

			$propertyNodes = self::GetPropertyNodes($classNode);
			if ($propertyNodes != null)
			{
				$retValue = new LJCDocDataProperties();
				foreach ($propertyNodes as $propertyNode)
				{
					$name = self::Value($propertyNode->Name);
					$property = new LJCDocDataProperty($name);
					$retValue->AddObject($property, $name);
					$property->Summary = self::Value($propertyNode->Summary);
					$property->Returns = self::Value($propertyNode->Returns);
					$property->Remarks = self::Value($propertyNode->Remarks);
					// *** Next Statement *** Add
					$property->Syntax = self::Value($propertyNode->Syntax);
				}
			}
			return $retValue;
		}

		// ---------------
		// Static GetNodes Functions - LJCDocDataFile

		// Retrieves the Class nodes.
		private static function GetClassNodes(SimpleXMLElement $docNode)
			: ?SimpleXMLElement
		{
			$retValue = null;

			$nodes = $docNode->Classes;
			if ($nodes != null)
			{
				$retValue = $nodes->children();
			}
			return $retValue;
		}

		// Retrieves the Method nodes.
		private static function GetMethodNodes(SimpleXMLElement $classNode)
			: ?SimpleXMLElement
		{
			$retValue = null;

			$nodes = $classNode->Methods;
			if ($nodes != null)
			{
				$retValue = $nodes->children();
			}
			return $retValue;
		}

		// Retrieves the Para nodes.
		private static function GetParamNodes(SimpleXMLElement $functionNode)
			: ?SimpleXMLElement
		{
			$retValue = null;

			$nodes = $functionNode->Params;
			if ($nodes != null)
			{
				$retValue = $nodes->children();
			}
			return $retValue;
		}

		// Retrieves the Property nodes.
		private static function GetPropertyNodes(SimpleXMLElement $classNode)
			: ?SimpleXMLElement
		{
			$retValue = null;

			$nodes = $classNode->Properties;
			if ($nodes != null)
			{
				$retValue = $nodes->children();
			}
			return $retValue;
		}

		// Get the value from the XML value.
		private static function Value(SimpleXMLElement $xmlValue) : ?string
		{
			$retValue = null;

			if ($xmlValue != null)
			{
				$retValue = trim((string)$xmlValue);
			}
			return $retValue;
		}

		// ---------------
		// Constructors

		// Initializes a class instance.
		/// <include path='items/construct/*' file='Doc/LJCDocDataFile.xml'/>
		public function __construct(string $name, ?string $summary = null)
		{
			$this->Classes = null;
			$this->Functions = null;
			$this->Name = $name;
			$this->Remarks = null;
			$this->Summary = $summary;
		}

		// ---------------
		// Public Methods - LJCDocDataFile

		// Creates a Clone of the current object.
		/// <include path='items/Clone/*' file='../../CommonDoc/PHPDataClass.xml'/>
		public function Clone() : self
		{
			$retValue = new self($this->Name, $this->Summary);
			$retValue->Classes = $this->Classes;
			$retValue->Functions = $this->Functions;
			return $retValue;
		}

		// Writes the serialized XML.
		/// <include path='items/Serialize/*' file='Doc/LJCDocDataFile.xml'/>
		public function Serialize(string $xmlFileSpec) : void
		{
			$docDataXML = $this->SerializeToString();
			$stream = fopen($xmlFileSpec, "w");
			$this->Writer = new LJCWriter($stream);
			$this->Writer->FWrite($docDataXML);
			fclose($stream);
		}

		// Creates the serialized XML string.
		/// <include path='items/SerializeToString/*' file='Doc/LJCDocDataFile.xml'/>
		public function SerializeToString() : string
		{
			$builder = new LJCStringBuilder();

			$builder->AppendLine("<?xml version=\"1.0\"?>");
      $builder->Append("<!-- Copyright (c) Lester J. Clark 2022 -");
			$builder->AppendLine(" All Rights Reserved -->");
			$builder->Append("<LJCDocDataFile xmlns:xsd=");
			$builder->AppendLine("'http://www.w3.org/2001/XMLSchema'");
			$builder->Append("  xmlns:xsi=");
			$builder->AppendLine("'http://www.w3.org/2001/XMLSchema-instance'>");

			$indent = 1;
			$builder->AppendTags("Name", $this->Name, $indent);
			$builder->AppendTags("Summary", $this->Summary, $indent);
			$builder->AppendTags("Remarks", $this->Remarks, $indent);

			if ($this->Classes != null)
			{
				$builder->AppendLine("<Classes>", $indent);
				foreach ($this->Classes as $class)
				{
					$builder->AppendLine("<Class>", $indent + 1);
					$builder->AppendTags("Name", $class->Name, $indent + 2);
					$builder->AppendTags("Summary", $class->Summary, $indent + 2);
					$builder->AppendTags("Remarks", $class->Remarks, $indent + 2);
					$builder->Append($this->CreateMethods($class, $indent + 2));
					$builder->Append($this->CreateProperties($class, $indent + 2));
					$builder->AppendTags("Code", $class->Code, $indent + 2);
					$builder->AppendLine("</Class>", $indent + 1);
				}
				$builder->AppendLine("</Classes>", $indent);
			}
			$builder->AppendLine("</LJCDocDataFile>");
			return $builder->ToString();
		}
		
		// ---------------
		// Private Methods - LJCDocDataFile

		// Creates the serialized Methods XML.
		private function CreateMethods(LJCDocDataClass $class, int $indent)
			: ?string
		{
			$builder = new LJCStringBuilder();

			if ($class->Methods != null && count($class->Methods) > 0)
			{

				$builder->AppendLine("<Methods>", $indent);
				foreach ($class->Methods as $method)
				{
					$builder->AppendLine("<Method>", $indent + 1);
					$builder->AppendTags("Name", $method->Name, $indent + 2);
					$builder->AppendTags("Summary", $method->Summary, $indent + 2);
					$builder->Append($this->CreateParams($method->Params, $indent + 2));
					$builder->AppendTags("Returns", $method->Returns, $indent + 2);
					$builder->AppendTags("Remarks", $method->Remarks, $indent + 2);
					// *** Next Statement *** Change - Add Syntax
					$builder->AppendTags("Syntax", $method->Syntax, $indent + 2);
					$builder->AppendTags("Code", $method->Code, $indent + 2);
					$builder->AppendLine("</Method>", $indent + 1);
				}
				$builder->AppendLine("</Methods>", $indent);
			}
			return $builder->ToString();
		}

		// Creates the serialized Params XML.
		private function CreateParams(?LJCDocDataParams $params, int $indent)
			: ?string
		{
			$builder = new LJCStringBuilder();

			if ($params != null && count($params) > 0)
			{
				$builder->AppendLine("<Params>", $indent);
				foreach ($params as $param)
				{
					$builder->AppendLine("<Param>", $indent + 1);
					$builder->AppendTags("Name", $param->Name, $indent + 2);
					$builder->AppendTags("Summary", $param->Summary, $indent + 2);
					$builder->AppendLine("</Param>", $indent + 1);
				}
				$builder->AppendLine("</Params>", $indent);
			}
			return $builder->ToString();
		}

		// Appends the serialized Properties XML.
		private function CreateProperties(LJCDocDataClass $class, int $indent)
			: ?string
		{
			$builder = new LJCStringBuilder();

			if ($class->Properties != null && count($class->Properties) > 0)
			{
				$builder->AppendLine("<Properties>", $indent);
				foreach ($class->Properties as $property)
				{
					$builder->AppendLine("<Property>", $indent + 1);
					$builder->AppendTags("Name", $property->Name, $indent + 2);
					$builder->AppendTags("Summary", $property->Summary, $indent + 2);
					$builder->AppendTags("Returns", $property->Returns, $indent + 2);
					$builder->AppendTags("Remarks", $property->Remarks, $indent + 2);
					// *** Next Statement *** Change - Add Syntax
					$builder->AppendTags("Syntax", $property->Syntax, $indent + 2);
					$builder->AppendLine("</Property>", $indent + 1);
				}
				$builder->AppendLine("</Properties>", $indent);
			}
			return $builder->ToString();
		}

		// ---------------
		// Public Properties - LJCDocDataFile

		/// <summary>The Class collection.</summary>
		public ?LJCDocDataClasses $Classes;

		/// <summary>The Function array.</summary>
		public ?LJCDocDataMethods $Functions;

		/// <summary>The Name value.</summary>
		public string $Name;

		/// <summary>The Name value.</summary>
		public ?string $Remarks;

		/// <summary>The Summary value.</summary>
		public ?string $Summary;

		// The Writer object.
		private LJCWriter $Writer;
	}  // LJCDocDataFile

	// ***************
	/// <summary>Represents a DocData Function.</summary>
	class LJCDocDataMethod
	{
		// ---------------
		// Constructors

		// Initializes a class instance.
		/// <include path='items/construct/*' file='Doc/LJCDocDataMethod.xml'/>
		public function __construct(string $name, ?string $summary = null
			, ?string $returns = null)
		{
			$this->Code = null;
			$this->Name = $name;
			$this->Params = null;
			$this->Remarks = null;
			$this->Returns = $returns;
			$this->Summary = $summary;
			$this->Syntax = null;
		}

		// ---------------
		// Public Methods

		// Creates a Clone of the current object.
		/// <include path='items/Clone/*' file='../../CommonDoc/PHPDataClass.xml'/>
		public function Clone() : self
		{
			$retValue = new self($this->Name, $this->Summary, $this->Returns);
			$retValue->Code = $this->Code;
			$retValue->Params = $this->Params;
			$retValue->Remarks = $this->Remarks;
			return $retValue;
		}

		// ---------------
		// Public Properties - LJCDocDataMethod

		/// <summary>The Code value.</summary>
		public ?string $Code;

		/// <summary>The Name value.</summary>
		public string $Name;

		/// <summary>The Param array.</summary>
		public ?LJCDocDataParams $Params;

		/// <summary>The Remarks value.</summary>
		public ?string $Remarks;

		/// <summary>The Returns value.</summary>
		public ?string $Returns;

		/// <summary>The Summary value.</summary>
		public ?string $Summary;

		/// <summary>The Syntax value.</summary>
		public ?string $Syntax;
	}  // LJCDocDataMethod

	// ***************
	/// <summary>Represents a collection of objects.</summary>
	class LJCDocDataMethods extends LJCCollectionBase
	{
		// ----------------------
		// *** Public Methods ***

		// Adds an object and key value.
		/// <include path='items/AddObject/*' file='../../CommonDoc/PHPCollection.xml'/>
		public function AddObject(LJCDocDataMethod $item, $key = null)
			: ?LJCDocDataMethod
		{
			if (null == $key)
			{
				$key = $item->ID;
			}
			$retValue = $this->AddItem($item, $key);
			return $retValue;
		}

		/// <summary>Creates an object clone.</summary>
		public function Clone() : self
		{
			$retValue = new self();
			foreach ($this->Items as $key => $item)
			{
				$retValue->AddObject($item);
			}
			unset($item);
			return $retValue;
		}

		// Get the item by Key value.
		/// <include path='items/Get/*' file='../../CommonDoc/PHPCollection.xml'/>
		public function Get($key, bool $throwError = true) : ?LJCDocDataMethod
		{
			$retValue = $this->GetItem($key, $throwError);
			return $retValue;
		}
	}  // LJCDocDataMethods

	// ***************
	/// <summary>Represents a DocData Parameter.</summary>
	class LJCDocDataParam
	{
		// ---------------
		// Constructors

		// Initializes a class instance.
		/// <include path='items/construct/*' file='Doc/LJCDocDataClass.xml'/>
		public function __construct(string $name, ?string $summary = null)
		{
			$this->Name = $name;
			$this->Summary = $summary;
		}

		// ---------------
		// Public Methods

		// Creates a Clone of the current object.
		/// <include path='items/Clone/*' file='../../CommonDoc/PHPDataClass.xml'/>
		public function Clone() : self
		{
			$retValue = new self($this->Name, $this->Summary);
			return $retValue;
		}

		// ---------------
		// Public Properties

		/// <summary>The Name value.</summary>
		public string $Name;

		/// <summary>The Summary value.</summary>
		public ?string $Summary;
	}  // LJCDocDataParam

	// ***************
	/// <summary>Represents a collection of objects.</summary>
	class LJCDocDataParams extends LJCCollectionBase
	{
		// ----------------------
		// *** Public Methods ***

		// Adds an object and key value.
		/// <include path='items/AddObject/*' file='../../CommonDoc/PHPCollection.xml'/>
		public function AddObject(LJCDocDataParam $item, $key = null)
			: ?LJCDocDataParam
		{
			if (null == $key)
			{
				$key = $item->Name;
			}
			$retValue = $this->AddItem($item, $key);
			return $retValue;
		}

		/// <summary>Creates an object clone.</summary>
		public function Clone() : self
		{
			$retValue = new self();
			foreach ($this->Items as $key => $item)
			{
				$retValue->AddObject($item);
			}
			unset($item);
			return $retValue;
		}

		// Get the item by Key value.
		/// <include path='items/Get/*' file='../../CommonDoc/PHPCollection.xml'/>
		public function Get($key, bool $throwError = true) : ?LJCDocDataParam
		{
			$retValue = $this->GetItem($key, $throwError);
			return $retValue;
		}
	}  // LJCDocDataParams

	// ***************
	/// <summary>Represents a DocData Property.</summary>
	class LJCDocDataProperty
	{
		// ---------------
		// Constructors

		// Initializes a class instance.
		/// <include path='items/construct/*' file='Doc/LJCDocDataProperty.xml'/>
		public function __construct(string $name, ?string $summary = null
			, ?string $returns = null)
		{
			$this->Name = $name;
			$this->Remarks = null;
			$this->Returns = $returns;
			$this->Summary = $summary;
			$this->Syntax = null;
		}

		// ---------------
		// Public Methods

		// Creates a Clone of the current object.
		/// <include path='items/Clone/*' file='../../CommonDoc/PHPDataClass.xml'/>
		public function Clone() : self
		{
			$retValue = new self($this->Name, $this->Summary, $this->Returns);
			$retValue->Remarks = $this->Remarks;
			return $retValue;
		}

		// ---------------
		// Public Properties  - LJCDocDataProperty

		/// <summary>The Name value.</summary>
		public string $Name;

		/// <summary>The Remarks value.</summary>
		public ?string $Remarks;

		/// <summary>The Returns value.</summary>
		public ?string $Returns;

		/// <summary>The Summary value.</summary>
		public ?string $Summary;

		/// <summary>The Syntax value.</summary>
		public ?string $Syntax;
	}  // LJCDocDataProperty

	// ***************
	/// <summary>Represents a collection of objects.</summary>
	class LJCDocDataProperties extends LJCCollectionBase
	{
		// ----------------------
		// *** Public Methods ***

		// Adds an object and key value.
		/// <include path='items/AddObject/*' file='../../CommonDoc/PHPCollection.xml'/>
		public function AddObject(LJCDocDataProperty $item, $key = null)
			: ?LJCDocDataProperty
		{
			if (null == $key)
			{
				$key = $item->ID;
			}
			$retValue = $this->AddItem($item, $key);
			return $retValue;
		}

		// Creates an object clone.
		/// <include path='items/Clone/*' file='../../CommonDoc/PHPCollection.xml'/>
		public function Clone() : self
		{
			$retValue = new self();
			foreach ($this->Items as $key => $item)
			{
				$retValue->AddObject($item);
			}
			unset($item);
			return $retValue;
		}

		// Get the item by Key value.
		/// <include path='items/Get/*' file='../../CommonDoc/PHPCollection.xml'/>
		public function Get($key, bool $throwError = true) : ?LJCDocDataProperty
		{
			$retValue = $this->GetItem($key, $throwError);
			return $retValue;
		}
	}  // LJCDocDataProperties
?>