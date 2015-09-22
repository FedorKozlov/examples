<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/*Экранирование*/
if(isset($arParams['IBLOCK_ID']) && !empty($arParams['IBLOCK_ID']))
{
	$arParams['IBLOCK_ID'] = intval($arParams['IBLOCK_ID']);
}
else
{
	$arParams['IBLOCK_ID'] = 113;
}
if(isset($arParams['IBLOCK_ID']) && !empty($arParams['IBLOCK_ID']))
{
	$arParams["CACHE_TIME"] = $arParams["CACHE_TIME"];
}
else
{
	$arParams["CACHE_TIME"] = 3600;
}
if(isset($_GET['SECTION_ID']))
	$_GET['SECTION_ID'] = intval($_GET['SECTION_ID']);
else
	$_GET['SECTION_ID'] = 0;
if(isset($_GET['element_name']))
	$_GET['element_name'] = htmlspecialchars(addslashes(trim($_GET['element_name'])));
if(isset($_GET['element_status']))
	$_GET['element_status'] = htmlspecialchars(addslashes(trim($_GET['element_status'])));
if(isset($_GET['date_from']))
	$_GET['date_from'] = htmlspecialchars(addslashes(trim($_GET['date_from'])));
if(isset($_GET['date_to']))
	$_GET['date_to'] = htmlspecialchars(addslashes(trim($_GET['date_to'])));
if(isset($_GET['submit_filter']))
	$_GET['submit_filter'] = htmlspecialchars(addslashes(trim($_GET['submit_filter'])));
if(isset($_GET['doc_num']))
	$_GET['doc_num'] = htmlspecialchars(addslashes(trim($_GET['doc_num'])));
/*х_Экранирование_х*/
$iblock_id = $arParams['IBLOCK_ID'];

$cache_id = SITE_ID."|vnd.section|".serialize($arParams).'|'.serialize($_GET);
$cache_dir = "/vnd";
$obCache = new CPHPCache;


if($obCache->InitCache($arParams["CACHE_TIME"], $cache_id, $cache_dir))
{
	$arResult = $obCache->GetVars();
}
elseif(CModule::IncludeModule("iblock") && $obCache->StartDataCache())
{  
	global $CACHE_MANAGER;
	if (defined("BX_COMP_MANAGED_CACHE")) 
	{
		$CACHE_MANAGER->StartTagCache($cache_dir);	
	}
	
	/*breadcrumb*/
	$rsBread = CIBlockSection::GetNavChain($iblock_id,$_GET['SECTION_ID'], array('NAME', 'SECTION_PAGE_URL'));
	while($arBread = $rsBread->GetNext())
	{
		$arTemp['NAME'] = $arBread['NAME'];
		$arTemp['SECTION_PAGE_URL'] = $arBread['SECTION_PAGE_URL'];
		$arTemp['ID'] = $arBread['ID'];
		$arResult['BREADCRUMB'][] = $arTemp;
	}
	$str='';
	foreach($arResult['BREADCRUMB'] as $v){
		
		if($v != end($arResult['BREADCRUMB'])){
			$str .= '<a href="/docs/vnd/'.$v['ID'].'/">'.$v['NAME'].'</a>';
			$str .= "<span class='bread_last'> / </span>";
		}else{
			$str .= '<span class="bread_last">'.$v['NAME'].'</span>';
		}
	}
	$arResult['BREADCRUMB_STRING'] = $str;
	
	$setPageTitle = end($arResult['BREADCRUMB']);
	if(!empty($setPageTitle['NAME'])){
		$APPLICATION->SetTitle($setPageTitle['NAME']);
	}
	/*x_breadcrumb_x*/

	$arOrder = array("SORT"=>"ASC");
	$arFilter = array();
	$arFilter = Array('IBLOCK_ID'=>$iblock_id, 'GLOBAL_ACTIVE'=>'Y', 'SECTION_ID'=>false);
	$arSelect = array("ID", "IBLOCK_ID", "NAME", "LIST_PAGE_URL", "SECTION_PAGE_URL", "DEPTH_LEVEL");

	$sectionList = CIBlockSection::GetList($arOrder, $arFilter, false, $arSelect, false);
	while($arSection = $sectionList->GetNext())
	{
		$arResult['FIRST_LEVEL_MENU'][] = $arSection;
	}

	if(empty($arResult['BREADCRUMB']))
	{
		$arResult['TYPE'] = 1;
		$arResult['SECTIONS'] = $arResult['FIRST_LEVEL_MENU'];
	}
	elseif(count($arResult['BREADCRUMB'])==1)
	{
		$arResult['TYPE'] = 1;
		$arFilter = Array('IBLOCK_ID'=>$iblock_id, 'GLOBAL_ACTIVE'=>'Y', 'SECTION_ID'=>$_GET['SECTION_ID']);
		$arSelect = array("ID", "IBLOCK_ID", "NAME", "LIST_PAGE_URL", "SECTION_PAGE_URL", "DEPTH_LEVEL");
		$sectionList = CIBlockSection::GetList(array("SORT"=>"ASC"), $arFilter, false, $arSelect, false);
		while($arSection = $sectionList->GetNext())
		{
			$arResult['SECTIONS'][] = $arSection;
		}
	}
	elseif(count($arResult['BREADCRUMB'])==2)
	{		
		$arResult['TYPE'] = 2;
	}
		if(empty($_GET['submit_filter']) && $_GET['SECTION_ID'])
			$arFilter = Array('IBLOCK_ID'=>$iblock_id, 'ACTIVE'=>'Y', 'SECTION_ID'=>$_GET['SECTION_ID']);
		else
			$arFilter = Array('IBLOCK_ID'=>$iblock_id, 'ACTIVE'=>'Y');
		
		$arSelect = Array('ID', 'NAME', 'IBLOCK_ID');
		$rsElements = CIBlockElement::GetList(array("id"=>"desc"), $arFilter, false, false, $arSelect);
		while($arElement = $rsElements->GetNextElement())
		{
			//Получаем массив всех элементов, включая поле OLD_VERS, содержащее id записи, которую заменяет текущая.
			$props = $arElement->GetProperties();
			$fields = $arElement->GetFields();
			$arElementTemp['ID'] = $fields['ID'];
			$arElementTemp['NAME'] = $fields['NAME'];
			$arElementTemp['OLD_VERS'] = $props['OLD_VERS']['VALUE'];
			$arElementTemp['COMMENT'] = $props['COMMENT']['VALUE'];
			$arElementTemp['FILES'] = $props['FILES']['VALUE'];
			foreach($arElementTemp['FILES'] as $k=>$v){
				$rsFile = CFile::GetByID($v);
				$arFile = $rsFile->Fetch();
				$arFile['PATH'] = CFile::GetPath($v);
				$file_info = pathinfo($arFile['PATH']);
				$arFile['TYPE_FILE'] = $file_info['extension'];
				$arElementTemp['FILES'][$k] = $arFile;
				$fileNames[$arElementTemp['ID']][] = $arFile['FILE_NAME'];
			}
			if(!empty($props['ACCEPT_DATE']['VALUE']))
			{
				$date = FormatDate('d.m.Y', MakeTimeStamp($props['ACCEPT_DATE']['VALUE'], "DD.MM.YYYY HH:MI:SS"));		
			}
			else
			{
				$date = "";
			}
			$arElementTemp['STATUS'] = $props['STATUS']['VALUE'];
			$arElementTemp['DOCUMENT_NUMBER'] = $props['ACCEPT_NUMBER']['VALUE'];
			$arElementTemp['DOC_DATE'] = $date;
			/* echo '<pre>'; print_r($fileNames); echo '</pre>'; */
			//Получили массив, теперь устаревшие записи запишем как элемент массива новой записи.
			$bool=true;
			foreach($arResult['ELEMENTS'] as $k=>$v){
				if($v['OLD_VERS'] == $fields['ID']){
					$arResult['ELEMENTS'][$k]['OLD_VERS_ARR'][] = $arElementTemp;
					$arResult['ELEMENTS'][$k]['OLD_VERS'] = $arElementTemp['OLD_VERS'];
					$bool=false;
				}
			}
			if($bool)
			{
				if(!empty($_GET['submit_filter']))
				{
					$x = true;
					if(!empty($_GET['element_status']) && $props['STATUS']['VALUE_XML_ID']!=$_GET['element_status'])
					{
						$x = false;
					}
					if(!empty($_GET['element_name']))
					{
						$x = false;
						if(strripos($fields['NAME'], $_GET['element_name'])!==false)
							$x = true;
						else {						
							foreach($fileNames[$arElementTemp['ID']] as $fileName) {
								if(strripos($fileName, $_GET['element_name'])!==false) {
									$x = true;
									break;
								}
							}
						}
					}
					if(!empty($_GET['date_from']) && !empty($_GET['date_to']))
					{
						$from = MakeTimeStamp($_GET['date_from'].' 00:00:00', 'DD.MM.YYYY HH:MI:SS');
						$to = MakeTimeStamp($_GET['date_to'].' 24:59:59', 'DD.MM.YYYY HH:MI:SS');
						$elDate = MakeTimeStamp($props['ACCEPT_DATE']['VALUE'], "DD.MM.YYYY HH:MI:SS");
						if(($to<$elDate) || ($elDate<$from))
						{
							$x = false;
						}
					}
					if(!empty($_GET['date_from']))
					{
						$from = MakeTimeStamp($_GET['date_from'].' 00:00:00', 'DD.MM.YYYY HH:MI:SS');
						$elDate = MakeTimeStamp($props['ACCEPT_DATE']['VALUE'], "DD.MM.YYYY HH:MI:SS");
						if($elDate<$from)
						{
							$x = false;
						}
					}
					if(!empty($_GET['date_to']))
					{
						$to = MakeTimeStamp($_GET['date_to'].' 24:59:59', 'DD.MM.YYYY HH:MI:SS');
						$elDate = MakeTimeStamp($props['ACCEPT_DATE']['VALUE'], "DD.MM.YYYY HH:MI:SS");
						if($to<$elDate)
						{
							$x = false;
						}
					}
					if(!empty($_GET['doc_num']) && $props['ACCEPT_NUMBER']['VALUE'] != $_GET['doc_num']) {
						
						$x = false;
					}
					if($x)
					{
						$arResult['ELEMENTS'][$fields['ID']] = $arElementTemp;
					}
				}
				else
				{
					$arResult['ELEMENTS'][$fields['ID']] = $arElementTemp;
				}
			}
		}
		
		$rsStatus = CIBlockPropertyEnum::GetList(array('id'=>'desc'), array('IBLOCK_ID'=>$iblock_id, 'CODE'=>'STATUS'));
		while($arStatus = $rsStatus->GetNext())
		{
			$tempStatus['VALUE'] = $arStatus['VALUE'];
			$tempStatus['XML_ID'] = $arStatus['XML_ID'];
			$arResult['STATUS_VALUES'][] = $tempStatus;
		}
	
	$CACHE_MANAGER->EndTagCache();	
	$obCache->EndDataCache($arResult);
}
$setPageTitle = end($arResult['BREADCRUMB']);
if(!empty($setPageTitle['NAME'])){
	$APPLICATION->SetTitle($setPageTitle['NAME']);
}
$this->IncludeComponentTemplate();
?>