<?
function printKioskEditPropRow($arProp, $arResult, $arParams){?>
	<?if($arProp["IS_REQUIRED"] == "Y"):?>*<?endif?><?=$arProp["NAME"]?>: <?printKioskEditInput($arProp, $arResult, $arParams)?>
<?}

function printKioskEditInput($arProp, $arResult, $arParams){?>
	<span class="property">
		<?
		if (intval($arProp["ID"]) > 0)
		{
			if (
				$arResult["PROPERTY_LIST_FULL"][$arProp["ID"]]["PROPERTY_TYPE"] == "T"
				&&
				$arResult["PROPERTY_LIST_FULL"][$arProp["ID"]]["ROW_COUNT"] == "1"
			)
				$arResult["PROPERTY_LIST_FULL"][$arProp["ID"]]["PROPERTY_TYPE"] = "S";
			elseif (
				(
					$arResult["PROPERTY_LIST_FULL"][$arProp["ID"]]["PROPERTY_TYPE"] == "S"
					||
					$arResult["PROPERTY_LIST_FULL"][$arProp["ID"]]["PROPERTY_TYPE"] == "N"
				)
				&&
				$arResult["PROPERTY_LIST_FULL"][$arProp["ID"]]["ROW_COUNT"] > "1"
			)
				$arResult["PROPERTY_LIST_FULL"][$arProp["ID"]]["PROPERTY_TYPE"] = "T";
		}
		elseif (($arProp["ID"] == "TAGS") && CModule::IncludeModule('search'))
			$arResult["PROPERTY_LIST_FULL"][$arProp["ID"]]["PROPERTY_TYPE"] = "TAGS";

		if ($arResult["PROPERTY_LIST_FULL"][$arProp["ID"]]["MULTIPLE"] == "Y")
		{
			$inputNum = ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0) ? count($arResult["ELEMENT_PROPERTIES"][$arProp["ID"]]) : 0;
			$inputNum += $arResult["PROPERTY_LIST_FULL"][$arProp["ID"]]["MULTIPLE_CNT"];
		}
		else
		{
			$inputNum = 1;
		}

		switch($arProp["PROPERTY_TYPE"]){
			case "S":
				$arValues = array();
				if(is_array($arResult["ELEMENT_PROPERTIES"][$arProp["ID"]]) && count($arResult["ELEMENT_PROPERTIES"][$arProp["ID"]]) > 0)
				{
					foreach($arResult["ELEMENT_PROPERTIES"][$arProp["ID"]] as $key => $arValue)
					{
						$arValues[] = $arValue["VALUE"];
					}
				}
				if(count($arValues) == 0) $arValues[] = "";
				?>
				<?foreach($arValues as $key => $value):?>
					<input type="text" name="PROPERTY[<?=$arProp["ID"]?>][]" value="<?=$value?>"/>
				<?endforeach?>
				<?if($arProp["MULTIPLE"] == "Y"):?>
					<input class="btnMultipleMore" type="button" value="Ещё" data-prop-id="<?=$arProp["ID"]?>" data-prop-type="<?=$arProp["PROPERTY_TYPE"]?>" />
				<?endif?>
				<?break;
			case "L":
				if ($arResult["PROPERTY_LIST_FULL"][$arProp["ID"]]["LIST_TYPE"] == "C")
					$type = $arResult["PROPERTY_LIST_FULL"][$arProp["ID"]]["MULTIPLE"] == "Y" ? "checkbox" : "radio";
				else
					$type = $arResult["PROPERTY_LIST_FULL"][$arProp["ID"]]["MULTIPLE"] == "Y" ? "multiselect" : "dropdown";

				switch ($type):
					case "checkbox":
					case "radio":
						foreach ($arResult["PROPERTY_LIST_FULL"][$arProp["ID"]]["ENUM"] as $key => $arEnum)
						{
							$checked = false;
							if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0)
							{
								if (is_array($arResult["ELEMENT_PROPERTIES"][$arProp["ID"]]))
								{
									foreach ($arResult["ELEMENT_PROPERTIES"][$arProp["ID"]] as $arElEnum)
									{
										if ($arElEnum["VALUE"] == $key)
										{
											$checked = true;
											break;
										}
									}
								}
							}
							else
							{
								if ($arEnum["DEF"] == "Y") $checked = true;
							}

							?>
							<input type="<?=$type?>" name="PROPERTY[<?=$arProp["ID"]?>]<?=$type == "checkbox" ? "[".$key."]" : ""?>" value="<?=$key?>" id="property_<?=$key?>"<?=$checked ? " checked=\"checked\"" : ""?> /><label for="property_<?=$key?>"><?=$arEnum["VALUE"]?></label><br />
						<?
						}
						break;

					case "dropdown":
					case "multiselect":
						?>
						<select name="PROPERTY[<?=$arProp["ID"]?>]<?=$type=="multiselect" ? "[]\" size=\"".$arResult["PROPERTY_LIST_FULL"][$arProp["ID"]]["ROW_COUNT"]."\" multiple=\"multiple" : ""?>">
							<option value="">
								<?//echo GetMessage("CT_BIEAF_PROPERTY_VALUE_NA")?>
								Выберите
							</option>
							<?
							if (intval($arProp["ID"]) > 0) $sKey = "ELEMENT_PROPERTIES";
							else $sKey = "ELEMENT";

							foreach ($arResult["PROPERTY_LIST_FULL"][$arProp["ID"]]["ENUM"] as $key => $arEnum)
							{
								$checked = false;
								if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0)
								{
									foreach ($arResult[$sKey][$arProp["ID"]] as $elKey => $arElEnum)
									{
										if ($key == $arElEnum["VALUE"])
										{
											$checked = true;
											break;
										}
									}
								}
								else
								{
									if ($arEnum["DEF"] == "Y") $checked = true;
								}
								?>
								<option value="<?=$key?>" <?=$checked ? " selected=\"selected\"" : ""?>><?=$arEnum["VALUE"]?></option>
							<?
							}
							?>
						</select>
						<?
						break;

				endswitch;
				break;
			case "F":
				for ($i = 0; $i<$inputNum; $i++)
				{
					$value = intval($arProp["ID"]) > 0 ? $arResult["ELEMENT_PROPERTIES"][$arProp["ID"]][$i]["VALUE"] : $arResult["ELEMENT"][$arProp["ID"]];
					?>
					<input type="hidden" name="PROPERTY[<?=$arProp["ID"]?>][<?=$arResult["ELEMENT_PROPERTIES"][$arProp["ID"]][$i]["VALUE_ID"] ? $arResult["ELEMENT_PROPERTIES"][$arProp["ID"]][$i]["VALUE_ID"] : $i?>]" value="<?=$value?>" />
					<input type="file" size="<?=$arResult["PROPERTY_LIST_FULL"][$arProp["ID"]]["COL_COUNT"]?>"  name="PROPERTY_FILE_<?=$arProp["ID"]?>_<?=$arResult["ELEMENT_PROPERTIES"][$arProp["ID"]][$i]["VALUE_ID"] ? $arResult["ELEMENT_PROPERTIES"][$arProp["ID"]][$i]["VALUE_ID"] : $i?>" /><br />
					<?

					if (!empty($value) && is_array($arResult["ELEMENT_FILES"][$value]))
					{
						?>
						<input type="checkbox" name="DELETE_FILE[<?=$arProp["ID"]?>][<?=$arResult["ELEMENT_PROPERTIES"][$arProp["ID"]][$i]["VALUE_ID"] ? $arResult["ELEMENT_PROPERTIES"][$arProp["ID"]][$i]["VALUE_ID"] : $i?>]" id="file_delete_<?=$arProp["ID"]?>_<?=$i?>" value="Y" /><label for="file_delete_<?=$arProp["ID"]?>_<?=$i?>"><?=GetMessage("IBLOCK_FORM_FILE_DELETE")?></label><br />
						<?

						if ($arResult["ELEMENT_FILES"][$value]["IS_IMAGE"])
						{
							?>
							<img src="<?=$arResult["ELEMENT_FILES"][$value]["SRC"]?>" height="<?=$arResult["ELEMENT_FILES"][$value]["HEIGHT"]?>" width="<?=$arResult["ELEMENT_FILES"][$value]["WIDTH"]?>" border="0" /><br />
						<?
						}
						else
						{
							?>
							<?=GetMessage("IBLOCK_FORM_FILE_NAME")?>: <?=$arResult["ELEMENT_FILES"][$value]["ORIGINAL_NAME"]?><br />
							<?=GetMessage("IBLOCK_FORM_FILE_SIZE")?>: <?=$arResult["ELEMENT_FILES"][$value]["FILE_SIZE"]?> b<br />
							[<a href="<?=$arResult["ELEMENT_FILES"][$value]["SRC"]?>"><?=GetMessage("IBLOCK_FORM_FILE_DOWNLOAD")?></a>]<br />
						<?
						}
					}
				}
				break;
		}?>
	</span>
<?}

function printKioskProp($ID){
	CModule::IncludeModule("iblock");
	$arSelect = array(
		//"PROPERTY_UR_STATUS",
		//"PROPERTY_FORMA_SOBSTVENNOSTI",
		//"PROPERTY_OGRN",
		//"PROPERTY_INN",
		//"PROPERTY_KPP",
		//"PROPERTY_CITY",
		//"PROPERTY_STREET",
		//"PROPERTY_HOUSE",
		//"PROPERTY_STROENIE",
		//"PROPERTY_KORPUS",
		//"PROPERTY_KM_TRASSI",
		//"PROPERTY_VLADENIE",
		//"PROPERTY_POYASNENIE",
		//"PROPERTY_BIK",
		//"PROPERTY_KORESP_SCHET_BANKA",
		//"PROPERTY_RASSCHETNIY_SCHET_ORG",
		//"PROPERTY_BANK_NAME",
		//"PROPERTY_FAMILIYA",
		//"PROPERTY_OTCHESTVO",
		//"PROPERTY_OGRN_IP",
		//"PROPERTY_KVARTIRA",
		"ID",
		"IBLOCK_ID",
		"NAME",
		"ACTIVE",
		"PROPERTY_*"
	);
	$obElement = CIBlockElement::GetList(array(), array("IBLOCK_ID" => KIOSKS_PROPS_IBLOCK_ID, "ID"=>$ID), false, false, $arSelect)->GetNextElement();
	$arElement = $obElement->GetFields();
	$arElement["PROPERTIES"] = $obElement->GetProperties();
	?>

	<div class="kioskElementContainer">
		Название: <?=$arElement["NAME"]?>
		<br/>
		<?
		$arSkeep = array("OWNER");
		foreach($arElement["PROPERTIES"] as $arProp):?>
			<?if(in_array($arProp["CODE"], $arSkeep)) continue;?>
			<?if($arProp["VALUE"]):?>
				<?=$arProp["NAME"]?>: <?=$arProp["VALUE"]?><br/>
			<?endif?>
		<?endforeach?>
		Активен:<input
			type="checkbox"
			class="checkboxKioskElementActive"
			<?if($arElement["ACTIVE"] == "Y"):?>checked="checked"<?endif?>
			data-element-id="<?=$arElement["ID"]?>"
			data-iblock-id="<?=$arElement["IBLOCK_ID"]?>"
			/>
		<!--<a href="prop.php?PROP_TYPE=--><?//=$arElement["PROPERTIES"]["UR_STATUS"]["VALUE_ENUM_ID"]?><!--&CODE=--><?//=$arElement["ID"]?><!--">-->
			<input
				data-href="prop.php?PROP_TYPE=<?=$arElement["PROPERTIES"]["UR_STATUS"]["VALUE_ENUM_ID"]?>&CODE=<?=$arElement["ID"]?>"
				class="submitBeforeRedirect"
				type="button"
				value="Изменить"
				/>
		<!--</a>-->
		<input
			type="button"
			value="Удалить"
			class="btnDeleteKioskElement"
			data-element-id="<?=$arElement["ID"]?>"
			data-iblock-id="<?=$arElement["IBLOCK_ID"]?>"
			/>
		<br/>
	</div>

<?}


function printTimePeriod($TIME_PERIOD_ID){
	$TIME_PERIOD_ID = intval($TIME_PERIOD_ID);
	if(!(intval($TIME_PERIOD_ID) > 0))return false;

	CModule::IncludeModule("hilghloadblock");
	$timePeriodsHLBlockDataClass = CPL::GetHLBlockDataClass(TIME_PERIODS_HLBLOCK_ID);
	$arTimePeriod = $timePeriodsHLBlockDataClass::GetList(array("filter" => array("ID" => $TIME_PERIOD_ID)))->Fetch();

	$arDays = array(
	"Понедельинк",
	"Вторник",
	"Среда",
	"Четверг",
	"Пятница",
	"Суббота",
	"Воскресенье",
	);
	$arHours = array();
	for($i=6; $i <= 24; $i++){
	$hour = strval($i);
	$arHours[$hour] = strlen($hour) == 1 ? "0".$hour : $hour;
	}
	$arMinutes = array();
	for($i=0; $i <= 60; $i+=5){
	$minute = strval($i);
	$arMinutes[$minute] = strlen($minute) == 1 ? "0".$minute : $minute;
	}
	?>
	<span class="timePeriod" data-time-period-id="<?=$TIME_PERIOD_ID?>" >
		<select data-type="dayFrom" >
			<?foreach($arDays as $key => $day):?>
				<option
					value="<?=$key?>"
					<?if($key == $arTimePeriod["UF_DAY_FROM"]):?>selected="selected"<?endif?>
				><?=$day?></option>
			<?endforeach?>
		</select>
		<select data-type="dayTo" >
			<?foreach($arDays as $key => $day):?>
				<option
					value="<?=$key?>"
					<?if($key == $arTimePeriod["UF_DAY_TO"]):?>selected="selected"<?endif?>
				><?=$day?></option>
			<?endforeach?>
		</select>
		<select data-type="hourFrom" >
			<?foreach($arHours as $key => $hour):?>
				<option
					value="<?=$key?>"
					<?if($key == $arTimePeriod["UF_HOUR_FROM"]):?>selected="selected"<?endif?>
				><?=$hour?></option>
			<?endforeach?>
		</select>
		<select data-type="minutesFrom" >
			<?foreach($arMinutes as $key => $minute):?>
				<option
					value="<?=$key?>"
					<?if($key == $arTimePeriod["UF_MINUTE_FROM"]):?>selected="selected"<?endif?>
				><?=$minute?></option>
			<?endforeach?>
		</select>
		<select data-type="hourTo" >
			<?foreach($arHours as $key => $hour):?>
				<option
				value="<?=$key?>"
				<?if($key == $arTimePeriod["UF_HOUR_TO"]):?>selected="selected"<?endif?>
					><?=$hour?></option>
			<?endforeach?>
		</select>
		<select data-type="minutesTo" >
			<?foreach($arMinutes as $key => $minute):?>
				<option
					value="<?=$key?>"
					<?if($key == $arTimePeriod["UF_MINUTE_TO"]):?>selected="selected"<?endif?>
				><?=$minute?></option>
			<?endforeach?>
		</select>
		<input class="btnTimePeriodDelete" type="button" value="Удалить"/>
	</span>
	<br/>
<?}

function timePeriodAdd($LINK_TO_ELEMENT){
	CModule::IncludeModule("highloadblock");
	$dataClass = CPL::getHLBlockDataClass(TIME_PERIODS_HLBLOCK_ID);
	$arFields = array(
		"UF_DAY_FROM" => 0,
		"UF_DAY_TO" => 5,
		"UF_HOUR_FROM" => 9,
		"UF_HOUR_TO" => 18,
		"UF_MINUTE_FROM" => 0,
		"UF_MINUTE_TO" => 0,
		"UF_LINK_TO_ELEMENT" => $LINK_TO_ELEMENT,
	);
	$result = $dataClass::add($arFields);
	if($result->IsSuccess()){
		$arFields["ID"] = $result->GetID();
		return $arFields;
	}else
		return false;
}