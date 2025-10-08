set linePath="\Users\Les\Documents\Visual Studio 2022\wwwrootDev\LJCDataTableTestApp"
set projectPath=%linePath%\LJCCityList
cd %projectPath%
rem 1\2Users\3Les\4Documents\5Visual Studio 2022\6wwwrootDev\7LJCDataTableTestApp\8LJCCityList
set root=..\..\..\..\..\..\..
set servicePath=%root%\inetpub\wwwroot\ATestForm\CityList
copy LJCCityList.html %servicePath%
copy LJCCityListEvents.js %servicePath%
copy LJCDataConfigs.php %servicePath%
copy LJCTable.js %servicePath%

cd CityList
set root=..\..\..\..\..\..\..\..
set servicePath=%root%\inetpub\wwwroot\ATestForm\CityList\CityList
copy LJCCityDAL.js %servicePath%
copy LJCCityDataRequest.js %servicePath%
copy LJCCityDataService.php %servicePath%
copy LJCCityDetailEvents.js %servicePath%
copy LJCCityTableEvents.js %servicePath%
copy LJCCityTableRequest.js %servicePath%
copy LJCCityTableService.php %servicePath%

rem 1\2Users\3Les\4Documents\5Visual Studio 2022\6wwwrootDev\7LJCJSCommon
cd ..\..\..\LJCJSCommon
set root=..\..\..\..\..\..
set servicePath=%root%\inetpub\wwwroot\LJCJSCommon
copy LJCDataLib.js %servicePath%
copy LJCCommonLib.js %servicePath%

cd ..\LJCPHPCommon
set root=..\..\..\..\..\..
set servicePath=%root%\inetpub\wwwroot\LJCPHPCommon
copy LJCCollectionLib.php %servicePath%
copy LJCCommonLib.php %servicePath%
copy LJCDataManagerLib.php %servicePath%
copy LJCDBAccessLib.php %servicePath%
copy LJCHTMLBuilderLib.php %servicePath%
copy LJCHTMLTableLib.php %servicePath%
copy LJCRoot.php %servicePath%
copy LJCTextLib.php %servicePath%

rem 1\2Users\3Les\4Documents\5Visual Studio 2022\6wwwrootDev\7RegionApp\8City
cd ..\RegionApp\City
set root=..\..\..\..\..\..\..
set servicePath=%root%\inetpub\wwwroot\RegionApp\City
copy CityDAL.php %servicePath%
