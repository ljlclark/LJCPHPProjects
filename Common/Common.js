// Copyright(c) Lester J. Clark 2022 - All Rights Reserved
// Common.js

// ***************
// Common Functions
class Common
{
	// The Constructor function.
	constructor()
	{
	}

	// ---------------
	// Get Elements

	// Gets the HTMLElement.
	static Element(elementID)
	{
		return document.getElementById(elementID);
	}

	// Gets HTMLElements by Tag.
	static TagElements(parentElement, tag)
	{
		return parentElement.getElementsByTagName(tag);
	}

	// ---------------
	// Binary Search

	// Returns the index of a search item in the array.
	static BinarySearch(array, sortFunction, compareFunction, showAlerts = false)
	{
		let retValue = -1;

		if (array && Array.isArray(array))
		{
			array.sort(sortFunction);

			// Start with middle index.
			let length = array.length;
			let index = Common.MiddleCount(length) - 1;

			let nextCount = 0;
			let lowerBound = 0;
			let upperBound = length - 1;
			retValue = -2;
			while (-2 == retValue)
			{
				if (showAlerts)
				{
					let text = `${lowerBound} to ${upperBound}, (${nextCount}), ${index}`;
					alert(text);
				}

				let result = compareFunction(array[index]);
				switch (result)
				{
					// Item was found.
					case 0:
						retValue = index;
						if (showAlerts)
						{
							alert(`Found: index: ${index}`);
						}
						break;

					// Set previous index.
					case 1:
						// There are no items left.
						if (1 == nextCount)
						{
							retValue = -1;
							break;
						}

						// Get middle index of previous items.
						upperBound = index;
						nextCount = upperBound - lowerBound;
						index = upperBound - Common.MiddleCount(nextCount);
						break;

					// Set next index.
					case -1:
						// There are no items left.
						if (1 == nextCount)
						{
							retValue = -1;
							break;
						}

						// Get middle index of next items.
						lowerBound = index;
						nextCount = upperBound - lowerBound;
						index = lowerBound + Common.MiddleCount(nextCount);
						break;
				}
			}
		}
		return retValue;
	}

	// Returns the middle position of the count value.
	static MiddleCount(count)
	{
		let retValue = 0;
		if (0 == count % 2)
		{
			// Even length.
			retValue = count / 2;
		}
		else
		{
			// Odd length.
			let remainder = count % 2;
			retValue = (count - remainder) / 2 + 1;
		}
		return retValue;
	}

	// ---------------
	// Helper Methods
	
	// Gets the element text.
	static GetText(elementID)
	{
		let retValue = null;

		let element = Common.Element(elementID);
		if (element != null)
		{
			retValue = element.innerText;
		}
		return retValue;
	}

	// Gets the element value.
	static GetValue(elementID)
	{
		let retValue = null;

		let element = Common.Element(elementID);
		if (element != null)
		{
			retValue = element.value;
		}
		return retValue;
	}

	// Check if an element has a value.
	static HasValue(element)
	{
		let retValue = false;

		if (element && element != null)
		{
			retValue = true;
		}
		return retValue;
	}

	// Sets the element text.
	static SetText(elementID, text)
	{
		let element = Common.Element(elementID);
		if (element != null)
		{
			element.innerText = text;
		}
	}

	// Sets the element value.
	static SetValue(elementID, value)
	{
		let element = Common.Element(elementID);
		if (element != null)
		{
			element.value = value;
		}
	}

	// ---------------
	// Show Property Methods

	// 
	static AddPropertyOutput(item, propertyName)
	{
		let retValue = "";

		if (propertyName in item)
		{
			retValue = `${propertyName}=${item[propertyName]}\r\n`;
		}
		return retValue;
	}

	// Gets the default property names.
	static GetPropertyNames(typeName)
	{
		let retValue = null;

		switch (typeName.toLowerCase().trim())
		{
			case "window":
				retValue = [
					"parent", "document", "location", "frames", "length",
					"addEventListener",	"removeEventListener"
				];
				break;
			case "document":
				retValue = [
					"documentElement", "location", "baseURI", "body", "head",
					"nodeType", "hasChildNodes", "childNodes", "firstChild",
					"getElementByID", "getElementsByName",
					"getElementsByClassName", "getElementsByTagName",
					"addEventListener",	"removeEventListener"
				];
				break;
			case "element":
				retValue = [
					"localName", "tagName",
					"innerHTML", "outerHTML",
					"nodeType", "parentNode", "hasChildNodes", "childNodes", "firstChild",
					"getElementsByClassName", "getElementsByTagName",
				];
				break;
			default:
				retValue = [
					"addEventListener",	"removeEventListener"
				];
				break;
		}
		return retValue;
	}

	// Get the property list start text.
	static GetStartText(typeName, startText)
	{
		let retValue = null;

		if (null == startText)
		{
			retValue = `${typeName.toLowerCase().trim()}: `;
		}
		else
		{
			retValue = `${starText.trim()}: `;
		}
		return retValue;
	}

	// Show the properties of an object that are not null or "" and
	// do not start with "on".
	static ShowProperties(location, item)
	{
		if (item)
		{
			let startText = `${location}: `;

			let results = startText;
			let page = 1;
			let count = 1;
			for (let propertyName in item)
			{
				if (false == propertyName.startsWith("on")
					&& item[propertyName] != null
					&& item[propertyName] != "")
				{
					if (count % 12 == 0)
					{
						alert(`${page} ${results}`);
						results = startText;
						page++;
					}
					count++;
					results += Common.AddPropertyOutput(item, propertyName);
				}
			}
			if (results != startText)
			{
				alert(`${page} ${results}`);
			}
		}
	}

	// Show selected properties of an object.
	static ShowSelectProperties(item, typeName, startText, propertyNames)
	{
		if (item)
		{
			if (null == propertyNames)
			{
				propertyNames = Common.GetPropertyNames(typeName);
			}
			startText = Common.GetStartText(typeName, startText);

			let results = startText;
			let page = 1;
			let count = 1;
			let length = propertyNames.length;
			for (let index = 0; index < length; index++)
			{
				let propertyName = propertyNames[index];
				if (count % 12 == 0)
				{
					alert(`${page} ${results}`);
					results = startText;
					page++;
				}
				count++;
				results += Common.AddPropertyOutput(item, propertyName);
			}
			if (results != startText)
			{
				alert(`${page} ${results}`);
			}
		}
	}
}