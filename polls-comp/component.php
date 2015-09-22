<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)	die();
if (!CModule::IncludeModule("vote")): 
	ShowError(GetMessage("VOTE_MODULE_IS_NOT_INSTALLED"));
	return;
endif;
/********************************************************************
				Input params
********************************************************************/
/************** BASE ***********************************************/
if (is_array($arParams["CHANNEL_SID"]))
{
	$arr = array();
	foreach ($arParams["CHANNEL_SID"] as $v)
	{
		$v = trim(str_replace("-", "", $v));
		$v = (preg_match("~^[A-Za-z0-9_]+$~", $v) ? $v : "");
		if (strlen($v) > 0)
			$arr[] = $v;
	}
	$arParams["CHANNEL_SID"] = "";
	if (!empty($arr))
		$arParams["CHANNEL_SID"] = $arr;
}
else
{
	$arParams["CHANNEL_SID"] = trim(str_replace("-", "", $arParams["CHANNEL_SID"]));
	$arParams["CHANNEL_SID"] = (preg_match("~^[A-Za-z0-9_]+$~", $arParams["CHANNEL_SID"]) ? $arParams["CHANNEL_SID"] : "");
}

$arParams['ELEMENTS_ON_PAGE'] = intval($arParams['ELEMENTS_ON_PAGE']);
if( $arParams['ELEMENTS_ON_PAGE'] == 0 || $arParams['ELEMENTS_ON_PAGE'] == '')
	$arParams['ELEMENTS_ON_PAGE'] = 10;

$arParams["PAGER_TEMPLATE"] = trim($arParams["PAGER_TEMPLATE"]);

/************** URL ************************************************/
	$URL_NAME_DEFAULT = array(
		"vote_form" => "PAGE_NAME=vote_new&VOTE_ID=#VOTE_ID#",
		"vote_result" => "PAGE_NAME=vote_result&VOTE_ID=#VOTE_ID#",
		"vote_export" => "PAGE_NAME=vote_export&VOTE_ID=#VOTE_ID#"
    );
	foreach ($URL_NAME_DEFAULT as $URL => $URL_VALUE)
	{
		if (strLen(trim($arParams[strToUpper($URL)."_TEMPLATE"])) <= 0)
			$arParams[strToUpper($URL)."_TEMPLATE"] = $APPLICATION->GetCurPage()."?".$URL_VALUE;
		$arParams["~".strToUpper($URL)."_TEMPLATE"] = $arParams[strToUpper($URL)."_TEMPLATE"];
		$arParams[strToUpper($URL)."_TEMPLATE"] = htmlspecialcharsbx($arParams["~".strToUpper($URL)."_TEMPLATE"]);
	}
/********************************************************************
				/Input params
********************************************************************/

/********************************************************************
				Data
********************************************************************/
$arResult["VOTES"] = array();
$arResult["NAV_STRING"] = "";

$GROUP_SID = $arParams["CHANNEL_SID"];
if (is_array($GROUP_SID) && !empty($GROUP_SID))
{
	$arr = array();
	foreach ($GROUP_SID as $v)
	{
		if (!empty($v))
			$arr[] = $v;
	}
	if (!empty($arr))
		$arFilter["CHANNEL"] = $arr;
}
elseif (!empty($GROUP_SID))
{
	$arFilter["CHANNEL"] = $GROUP_SID;
}

if( $_GET['show_polls']=='active' )
{
	$arFilter["LAMP"] = 'green';

}
elseif( $_GET['show_polls']=='inactive' )
{
	$arFilter["LAMP"] = 'red';
}
$arResult['LAMP'] = $arFilter["LAMP"];

$strSqlOrder = "ORDER BY V.ACTIVE, V.DATE_START desc";

$params = array(
	"bDescPageNumbering" => false,
	"nPageSize" => $arParams['ELEMENTS_ON_PAGE'],
	"bShowAll" => false
);

$db_res = QVote::GetPublicList($arFilter, $strSqlOrder, $params);
if ($db_res)
{
	$arResult["NAV_STRING"] = $db_res->GetPageNavString(GetMessage("VOTE_PAGES"), $arParams["PAGER_TEMPLATE"]);
	while ($res = $db_res->Fetch())
	{
		$res["USER_ALREADY_VOTE"] = (CVote::UserAlreadyVote($res["ID"], $_SESSION["VOTE_USER_ID"], $res["UNIQUE_TYPE"], $res["KEEP_IP_SEC"], $GLOBALS["USER"]->GetID()) ? "Y" : "N");
		$res["URL"] = array(
				"~VOTE_RESULT" => CComponentEngine::MakePathFromTemplate($arParams["~VOTE_RESULT_TEMPLATE"], array("VOTE_ID" => $res["ID"])),
				"~VOTE_FORM" => CComponentEngine::MakePathFromTemplate($arParams["~VOTE_FORM_TEMPLATE"], array("VOTE_ID" => $res["ID"])),
				"VOTE_RESULT" => CComponentEngine::MakePathFromTemplate($arParams["VOTE_RESULT_TEMPLATE"], array("VOTE_ID" => $res["ID"])),
				"VOTE_FORM" => CComponentEngine::MakePathFromTemplate($arParams["VOTE_FORM_TEMPLATE"], array("VOTE_ID" => $res["ID"])),
                "~VOTE_EXPORT" => CComponentEngine::MakePathFromTemplate($arParams["~VOTE_EXPORT_TEMPLATE"], array("VOTE_ID" => $res["ID"])),
                "VOTE_EXPORT" => CComponentEngine::MakePathFromTemplate($arParams["VOTE_EXPORT_TEMPLATE"], array("VOTE_ID" => $res["ID"])),
        );
		$res["IMAGE"] = CFile::GetFileArray($res["IMAGE_ID"]);
		// For custom 
		foreach ($res["URL"] as $key => $val):
			$res[$key."_URL"] = $val;
		endforeach;
		$res["TITLE"] = htmlspecialcharsEx($res["TITLE"]);
		if ($res['DESCRIPTION_TYPE'] == 'text')
			$res['DESCRIPTION'] = htmlspecialcharsbx($res['DESCRIPTION']);
		$arResult["VOTES"][$res["ID"]] = $res;
	}
}
/********************************************************************
				/Data
********************************************************************/

if($GLOBALS["APPLICATION"]->GetGroupRight("vote") == "W" && CModule::IncludeModule("intranet") && is_object($GLOBALS['INTRANET_TOOLBAR']))
{
	$GLOBALS['INTRANET_TOOLBAR']->AddButton(array(
		'TEXT' => GetMessage("comp_voting_list_add"),
		'TITLE' => GetMessage("comp_voting_list_add_title"),
		'ICON' => 'add',
		'HREF' => '/bitrix/admin/vote_edit.php?lang='.LANGUAGE_ID,
		'SORT' => '100',
	));
	$GLOBALS['INTRANET_TOOLBAR']->AddButton(array(
		'TEXT' => GetMessage("comp_voting_list_list"),
		'TITLE' => GetMessage("comp_voting_list_list_title"),
		'ICON' => 'settings',
		'HREF' => '/bitrix/admin/vote_list.php?lang='.LANGUAGE_ID,
		'SORT' => '200',
	));
}
	
$this->IncludeComponentTemplate();
?>
