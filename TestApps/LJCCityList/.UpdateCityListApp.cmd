set linePath="\Users\Les\Documents\Visual Studio 2022\LJCPHPProjectsDev\TestApps"
set projectPath=%linePath%\LJCCityList

cd %projectPath%
rem 1\2Users\3Les\4Documents\5Visual Studio 2022\6LJCPHPProjectsDev\7TestApps\8LJCCityList
set root=..\..\..\..\..\..\..
set servicePath=%root%\inetpub\wwwroot\ATestForm\CityList
copy LJCCityList.html %servicePath%
copy LJCCityListEvents.js %servicePath%
copy LJCDataConfigs.php %servicePath%
copy LJCTable.js %servicePath%
copy LJCTableMessageLib.js %servicePath%
copy CSS\Table.css %servicePath%\CSS\

cd CityList
set root=..\..\..\..\..\..\..\..
set servicePath=%root%\inetpub\wwwroot\ATestForm\CityList\CityList
copy LJCCityDAL.js %servicePath%
copy LJCCityDataService.php %servicePath%
copy LJCCityDetailEvents.js %servicePath%
copy LJCCityTableEvents.js %servicePath%
copy LJCCityTableEventsArgs.js %servicePath%
copy LJCCityTableService.php %servicePath%
copy LJCRegionDAL.js %servicePath%
copy LJCTableService.php %servicePath%

cd ..\RegionList
set servicePath=%root%\inetpub\wwwroot\ATestForm\CityList\RegionList
copy LJCRegionDataRequest.js %servicePath%
copy LJCRegionDataService.php %servicePath%
copy LJCRegionDetailEvents.js %servicePath%
copy LJCRegionTableEvents.js %servicePath%
copy LJCRegionTableService.php %servicePath%

rem 1\2Users\3Les\4Documents\5Visual Studio 2022\6LJCPHPProjectsDev\7LJCJSCommon
cd ..\..\..\LJCJSCommon
set root=..\..\..\..\..\..
set servicePath=%root%\inetpub\wwwroot\LJCJSCommon
rem LJCDataLib.js %servicePath%
copy LJCDBAccessLib.js %servicePath%
copy LJCCollectionLib.js %servicePath%
copy LJCCommonLib.js %servicePath%

cd ..\LJCPHPCommon
set root=..\..\..\..\..\..
set servicePath=%root%\inetpub\wwwroot\LJCPHPCommon
copy LJCCollectionLib.php %servicePath%
copy LJCCommonLib.php %servicePath%
copy LJCDataManagerLib.php %servicePath%
copy LJCDBAccessLib.php %servicePath%
copy LJCHTMLTableLib.php %servicePath%
copy LJCRoot.php %servicePath%
copy LJCTextBuilderLib.php %servicePath%
copy LJCTextLib.php %servicePath%

rem 1\2Users\3Les\4Documents\5Visual Studio 2022\6LJCPHPProjectsDev\7TestApps\8RegionApp\9City
cd ..\TestApps\RegionApp\City
set root=..\..\..\..\..\..\..\..
set servicePath=%root%\inetpub\wwwroot\RegionApp\City
copy CityDAL.php %servicePath%
copy RegionDAL.php %servicePath%

