set linePath="\Users\Les\Documents\Visual Studio 2022\wwwrootDev\LJCDataTableTestApp"
set projectPath=%linePath%\LJCCityList
cd %projectPath%
set root=..\..\..
set servicePath=%root%\LJCPHPProjects\TestApps\LJCCityList
copy .PromoteLJCCityList.cmd %servicePath%
copy .UpdateCityListService.cmd %servicePath%
copy LJCCityList.html %servicePath%
copy LJCCityListEvents.js %servicePath%
copy LJCDataConfigs.php %servicePath%
copy LJCTable.js %servicePath%

cd CityList
set root=..\..\..\..
set servicePath=%root%\LJCPHPProjects\TestApps\LJCCityList\CityList
copy LJCCityDAL.js %servicePath%
copy LJCCityDataService.php %servicePath%
copy LJCCityDetailEvents.js %servicePath%
copy LJCCityTableEvents.js %servicePath%
copy LJCCityTableService.php %servicePath%

cd ..\RegionList
set servicePath=%root%\LJCPHPProjects\TestApps\LJCCityList\RegionList
copy LJCRegionTableEvents.js %servicePath%
copy LJCRegionTableService.php %servicePath%

cd ..\..\..\LJCJSCommon
set root=..\..
set servicePath=%root%\LJCPHPProjects\LJCJSCommon
copy LJCDataLib.js %servicePath%
copy LJCJSCommonLib.js %servicePath%
copy Templates\*.* %servicePath%\Templates\

cd ..\LJCPHPCommon
set root=..\..
set servicePath=%root%\LJCPHPProjects\LJCPHPCommon
copy LJCCollectionLib.php %servicePath%
copy LJCCommonLib.php %servicePath%
copy LJCDataManagerLib.php %servicePath%
copy LJCDBAccessLib.php %servicePath%
copy LJCHTMLBuilderLib.php %servicePath%
copy LJCHTMLTableLib.php %servicePath%
copy LJCRoot.php %servicePath%
copy LJCTextLib.php %servicePath%
copy Templates\*.* %servicePath%\Templates\
