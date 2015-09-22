<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arParams */
/** @var array $arResult */

$timePeriodsHLBlockDataClass = CPL::GetHLBlockDataClass(TIME_PERIODS_HLBLOCK_ID);
$dbTimePeriods = $timePeriodsHLBlockDataClass::getList(array(
	"filter" => array(
		"UF_LINK_TO_ELEMENT" => $arResult["ELEMENT"]["ID"],
	),
	"order" => array("ID" => "ASC")
));

$arTimePeriods = array();
while($arTimePeriod = $dbTimePeriods->Fetch()){
	$arResult["TIME_PERIODS"][] = $arTimePeriod;
}
if(count($arResult["TIME_PERIODS"]) <= 0){
	$arTimePeriod = timePeriodAdd($arResult["ELEMENT"]["ID"]);
	$arResult["TIME_PERIODS"][] = $arTimePeriod;
}

$dbKioskProps = CIBlockElement::GetList(array(), array("IBLOCK_ID" => KIOSKS_PROPS_IBLOCK_ID, "PROPERTY_OWNER" => $GLOBALS["USER"]->GetID()), false, false, array("ID"));
$arResult["PROPS"] = array();
while($arElement = $dbKioskProps->GetNext(false,false)){
	$arResult["PROPS"][] = $arElement["ID"];
}

// city start
$res_city = CIBlockElement::GetList(array(), array("IBLOCK_ID" => 7, "PROPERTY_OWNER" => $GLOBALS["USER"]->GetID()), false, false, array("ID", "NAME"));
while ($obj = $res_city->GetNext())
{
	$arResult["CITY"][$obj["ID"]] = $obj["NAME"];
}

// delivery in other regions start
$res_region = CIBlockSection::GetList(array("SORT" => "ASC"), array("IBLOCK_ID" => 7, "PROPERTY_OWNER" => $GLOBALS["USER"]->GetID()), false, array("ID", "NAME"), false);
while ($obj_region = $res_region->GetNext())
{
	$arResult["REGIONS"][$obj_region["ID"]] = $obj_region["NAME"];
}

// specialty start
$res = CIBlockElement::GetList(array(), array("IBLOCK_ID" => 3), false, false, array("ID", "NAME"));
while ($obj = $res->GetNext()) {
	$arResult["SPECIALTY"][$obj["ID"]] = $obj["NAME"];
}

// add name in spec
foreach ($arResult["ELEMENT_PROPERTIES"][1] as $key => &$arItem) {
	$res = CIBlockElement::GetByID($arItem["VALUE"]);
	if ($arRes = $res->GetNext()) {
		$arItem["NAME"] = $arRes["NAME"];
	}
}
unset($arItem);

// add shops
$res = CIBlockElement::GetList(array(), array("IBLOCK_ID" => 4, "PROPERTY_OWNER" => $GLOBALS["USER"]->GetID()), false, false, array("ID", "IBLOCK_ID", "NAME",
		"PROPERTY_TYPE", "PROPERTY_SHOP_NAME", "PROPERTY_PHONE", "PROPERTY_CITY", "PROPERTY_STREET", 'PROPERTY_HOUSE', "PROPERTY_BUILD",
		"PROPERTY_BUILDING", "PROPERTY_KM", "PROPERTY_PROP", "PROPERTY_EXPLAIN", "PROPERTY_COAST_SD", "PROPERTY_DURATION", "PROPERTY_CALL"));
while ($obj_shops = $res->GetNext()) {
	if ($obj_shops["PROPERTY_TYPE_ENUM_ID"] == 6) {
		$arResult["SHOPS"][6][] = $obj_shops;		
	} elseif ($obj_shops["PROPERTY_TYPE_ENUM_ID"] == 7) {
		$arResult["SHOPS"][7][] =  $obj_shops;
	}
}

// add courier
$res = CIBlockElement::GetList(array(), array("IBLOCK_ID" => 6, "PROPERTY_OWNER" => $GLOBALS["USER"]->GetID()), false, false, 
	array("ID", "IBLOCK_ID", "NAME", "PROPERTY_REGION", "PROPERTY_CALL", "PROPERTY_INTERVAL", "PROPERTY_WAITING_TIME", 
		"PROPERTY_WHEN", "PROPERTY_COAST", "PROPERTY_NOTE", "PROPERTY_FREE", "PROPERTY_FLOOR", "PROPERTY_DESC"));
while ($obj_courier = $res->GetNext()) {
	$arResult["COURIER"][] = $obj_courier;
}
?>