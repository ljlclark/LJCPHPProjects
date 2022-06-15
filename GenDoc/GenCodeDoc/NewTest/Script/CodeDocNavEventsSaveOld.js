"use strict";
// Copyright(c) Lester J. Clark 2022 - All Rights Reserved
// CodeDocNavEvents.js

window.PrevNavItem = null;
let NavItems = new CodeDocNavItems();

NavItems.Add("Projects", "LJCPHPCodeDoc.html");

// Common Libraries
NavItems.Add("CommonCode", "HTML/LJCCommonLib.html");
NavItems.Add("LJCCommon", "HTML/LJCCommon/LJCCommon.html");

NavItems.Add("TextOutput", "HTML/LJCTextLib.html");
NavItems.Add("TableColumn", "HTML/LJCHTMLTableColumn/LJCHTMLTableColumn.html");
NavItems.Add("HTMLWriter", "HTML/LJCHTMLWriter/LJCHTMLWriter.html");
NavItems.Add("StringBuilder", "HTML/LJCStringBuilder/LJCStringBuilder.html");
NavItems.Add("Writer", "HTML/LJCWriter/LJCWriter.html");

NavItems.Add("Collections", "HTML/LJCCollectionLib.html");
NavItems.Add("Collection", "HTML/LJCCollectionBase/LJCCollectionBase.html");

// Data Libraries
NavItems.Add("DataAccessLib", "HTML/LJCDBAccessLib.html");
NavItems.Add("Connection", "HTML/LJCConnectionValues/LJCConnectionValues.html");
NavItems.Add("DataAccess", "HTML/LJCDbAccess/LJCDbAccess.html");
NavItems.Add("Column", "HTML/LJCDbColumn/LJCDbColumn.html");
NavItems.Add("Columns", "HTML/LJCDbColumns/LJCDbColumns.html");
NavItems.Add("Join", "HTML/LJCJoin/LJCJoin.html");
NavItems.Add("Joins", "HTML/LJCJoins/LJCJoins.html");
NavItems.Add("JoinOn", "HTML/LJCJoinOn/LJCJoinOn.html");
NavItems.Add("JoinOns", "HTML/LJCJoinOns/LJCJoinOns.html");

NavItems.Add("DataManagerLib", "HTML/LJCDataManagerLib.html");
NavItems.Add("DataManager", "HTML/LJCDataManager/LJCDataManager.html");
NavItems.Add("SQLBuilder", "HTML/LJCSQLBuilder/LJCSQLBuilder.html");

// Text Generator Utility
NavItems.Add("GenSections", "HTML/LJCGenTextSectionLib.html");
NavItems.Add("Directive", "HTML/LJCDirective/LJCDirective.html");
NavItems.Add("Section", "HTML/LJCSection/LJCSection.html");
NavItems.Add("Sections", "HTML/LJCSections/LJCSections.html");
NavItems.Add("Replacement", "HTML/LJCReplacement/LJCReplacement.html");

NavItems.Add("TextGenerator", "HTML/LJCGenTextLib.html");
NavItems.Add("GenText", "HTML/LJCGenText/LJCGenText.html");

// CodeDoc Generator Utility
NavItems.Add("XMLComments", "HTML/LJCCommentsLib.html");
NavItems.Add("LJCComments", "HTML/LJCComments/LJCComments.html");

NavItems.Add("DocDataXML", "HTML/LJCDocDataGenLib.html");
NavItems.Add("DocDataGen", "HTML/LJCDocDataGen/LJCDocDataGen.html");

NavItems.Add("DocData", "HTML/LJCDocDataLib.html");
NavItems.Add("DocDataClass", "HTML/LJCDocDataClass/LJCDocDataClass.html");
NavItems.Add("DocDataClasses", "HTML/LJCDocDataClasses/LJCDocDataClasses.html");
NavItems.Add("DocDataFile", "HTML/LJCDocDataFile/LJCDocDataFile.html");
NavItems.Add("DocDataMethod", "HTML/LJCDocDataMethod/LJCDocDataMethod.html");
NavItems.Add("DocDataMethods", "HTML/LJCDocDataMethods/LJCDocDataMethods.html");
NavItems.Add("DocDataParam", "HTML/LJCDocDataParam/LJCDocDataParam.html");
NavItems.Add("DocDataParams", "HTML/LJCDocDataParams/LJCDocDataParams.html");
NavItems.Add("DocDataProperty", "HTML/LJCDocDataProperty/LJCDocDataProperty.html");
NavItems.Add("DocDataProperties", "HTML/LJCDocDataProperties/LJCDocDataProperties.html");

NavItems.Add("GenDataXML", "HTML/LJCGenDataGenLib.html");
NavItems.Add("GenDataGen", "HTML/LJCGenDataGen/LJCGenDataGen.html");

NavItems.Add("GenXMLSections", "HTML/LJCGenDataXMLLib.html");
NavItems.Add("GenSectionXML", "HTML/LJCGenDataXML/LJCGenDataXML.html");

NavItems.Add("FileXMLComments", "HTML/LJCIncludeLib.html");
NavItems.Add("Include", "HTML/LJCInclude/LJCInclude.html");

NavItems.Add("ParamXMLComments", "HTML/LJCParamCommentLib.html");
NavItems.Add("ParamComment", "HTML/LJCParamComment/LJCParamComment.html");

//
function DocumentClick(event)
{
	let srcElement = event.target;
	if ("navGroup" == srcElement.className
		|| "navItem" == srcElement.className)
	{
		let navItem = NavItems.SearchName(srcElement.id);
		if (navItem != null)
		{
			if (window.contentFrame != null)
			{
				window.contentFrame.src = navItem.URL;
			}
		}

		if (window.PrevNavItem != null)
		{
			window.PrevNavItem.style.backgroundColor = "";
		}
		window.PrevNavItem = srcElement;
		srcElement.style.backgroundColor = "lightgray";
	}
}
