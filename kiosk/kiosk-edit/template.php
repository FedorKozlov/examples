<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$this->setFrameMode(false);
?>

<pre>
	<?//print_r($arResult)?>
</pre>

<?if (!empty($arResult["ERRORS"])):?>
	<?ShowError(implode("<br />", $arResult["ERRORS"]))?>
<?endif;
if (strlen($arResult["MESSAGE"]) > 0):?>
	<?ShowNote($arResult["MESSAGE"])?>
<?endif?>

<?if($_REQUEST["ajaxSubmitKioskEditForm"] == "Y"):?>
	<?$APPLICATION->RestartBuffer();die(json_encode(array(
		"ERROR_MESSAGE" => $arResult["ERRORS"],
		"success" => false
	)));?>
<?endif?>

<div class="kioskEditContainer">
	<form class="kioskEditFrom" name="iblock_add" action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data">
		<?=bitrix_sessid_post()?>
		<?if ($arParams["MAX_FILE_SIZE"] > 0):?><input type="hidden" name="MAX_FILE_SIZE" value="<?=$arParams["MAX_FILE_SIZE"]?>" /><?endif?>
		Данные о киоске:<br/>
		*Название: <input type="text" name="PROPERTY[NAME][0]" size="25" value="<?=$arResult["ELEMENT"]["NAME"]?>">
		<?printKioskEditPropRow($arResult["PROPERTY_LIST_FULL"][2], $arResult, $arParams);?>
		<br/>
		<div id="kioskYandexSpecialization">
			<!-- специализация start -->
			<div class="specialty-0">
				<select name="specialty-select" class="specialty-select1" style="width: 300px;">
					<?foreach ($arResult["SPECIALTY"] as $key => $arItem):?>
						<option value="<?=$key;?>"><?=$arItem?></option>
					<?endforeach;?>
				</select>
				<input type="text" name="specialty-text" class="specialty-text" value="" placeholder="">
			</div>
			<div class="add-spec">
				
			</div>
			<input type="button" class="btnAddSpec" value="Save and Add"/>
			<div style="margin-top: 10px;">
				<div style="font-weight: bold;">Специализации:</div>
				<ul class="spec">
				<?foreach($arResult["ELEMENT_PROPERTIES"][1] as $arItem):?>
					<li style="margin-top: 3px;" class="<?=$arItem["VALUE"]?>"><?=$arItem["NAME"]?> <a class="<?=$arItem["VALUE"]?>">x</a> </li>
				<?endforeach;?>
				</ul>
			</div>
			<input type="hidden" class="" name="" value="">
			<!-- специализация end -->
		</div>
		<br/>
		<div style="font-weight: bold;">Контакты:</div>
		<?printKioskEditPropRow($arResult["PROPERTY_LIST_FULL"][4], $arResult, $arParams);?>
		<br/>
		<?printKioskEditPropRow($arResult["PROPERTY_LIST_FULL"][5], $arResult, $arParams);?>
		<br/>
		<?printKioskEditPropRow($arResult["PROPERTY_LIST_FULL"][6], $arResult, $arParams);?>
		<br/>
		Время работы:
		<br/>
		<div class="koiskWorkTimeContainer">
			<?foreach($arResult["TIME_PERIODS"] as $key => $arTimePeriod):?>
				<?printTimePeriod($arTimePeriod["ID"]);?>
			<?endforeach?>
		</div>
		<input type="button" class="btnTimePeriodAdd" value="+"/>
		<br/>
		<br/>
		Реквизиты:
		<input data-href="prop.php?LINK_TO_ELEMENT=<?=$arResult["ELEMENT"]["ID"]?>" class="submitBeforeRedirect" type="button" value="+"/>
		<br/>
		<!-- Свой регион start -->
		<div style="margin-top: 10px;">
			<div style="font-weight: bold;">Свой регион</div>
			<div style="margin-top: 10px;">
				Местоположение	киоска:<br>
				<select name="PROPERTY[60][0][VALUE]">
					<? $dev_value_city = $arResult["ELEMENT_PROPERTIES"][60][0]; ?>
					<? $countSelect = 0;?>
					<?foreach($arResult["CITY"] as $key => $arItem):?>
						<?if(!empty($dev_value_city)):?>
							<?if($dev_value_city["VALUE"] == $key):?>
								<? $opt = "selected"; ?>
								<? $countSelect = 1;?>
							<?endif;?>
						<?endif;?>
						<option <?=$opt;?> value="<?=$key;?>"><?=$arItem;?></option>
						<?if($countSelect == 1):?>
							<? $opt = ""; ?>
						<?endif;?>
					<?endforeach;?>
				</select>
			</div>
			<input type="hidden" class="60_0" name="PROPERTY[60][0][VALUE]" value="<?=$dev_value_city["VALUE"]?>">
			<div style="margin-top: 10px;">
				<b>Магазины: </b>
				<input data-href="add_shops.php?LINK_TO_ELEMENT=<?=$arResult["ELEMENT"]["ID"]?>" class="submitBeforeRedirect" type="button" value="+"/>
				<?if(!empty($arResult["SHOPS"][6])):?>
					<?foreach ($arResult["SHOPS"][6] as $key => $arItem):?>
						<div class="<?=$arItem["ID"]?>" style="margin-top: 5px; outline: 2px solid #000; width: 350px;">
							<div class="edit-shops"><a href="add_shops.php?CODE=<?=$arItem["ID"]?>">Edit</a></div>
							<div class="delete"><a class="<?=$arItem["ID"]?>">X</a></div>
							<div><?=$arItem["NAME"]?></div>
							<div><?=$arItem["PROPERTY_PHONE_VALUE"]?></div>
							<div><?=$arItem["PROPERTY_STREET_VALUE"]?>, дом <?=$arItem["PROPERTY_HOUSE_VALUE"]?></div>
							<div>Самовывоз: <?=$arItem["PROPERTY_COAST_SD_VALUE"]?></div>
							<div>Срок доставки: <?=$arItem["PROPERTY_DURATION_VALUE"]?></div>
							<div>Звонок	перед приездом: <?=$arItem["PROPERTY_CALL_VALUE"]?></div>
						</div>
					<?endforeach;?>
				<?endif;?>
			</div>
			<div style="margin-top: 10px;">
				<b>Точки самовывоза: </b>
				<input data-href="self_delivery.php?LINK_TO_ELEMENT=<?=$arResult["ELEMENT"]["ID"]?>" class="submitBeforeRedirect" type="button" value="+"/>
				<?if(!empty($arResult["SHOPS"][7])):?>
					<?foreach ($arResult["SHOPS"][7] as $key => $arItem):?>
						<div class="<?=$arItem["ID"]?>" style="margin-top: 5px;outline: 2px solid #000; width: 350px;">
							<div class="edit-delivery"><a href="self_delivery.php?CODE=<?=$arItem["ID"]?>">Edit</a></div>
							<div class="delete"><a class="<?=$arItem["ID"]?>">X</a></div>
							<div><?=$arItem["NAME"]?></div>
							<div><?=$arItem["PROPERTY_PHONE_VALUE"]?></div>
							<div><?=$arItem["PROPERTY_STREET_VALUE"]?>, дом <?=$arItem["PROPERTY_HOUSE_VALUE"]?></div>
							<div>Самовывоз: <?=$arItem["PROPERTY_COAST_SD_VALUE"]?></div>
							<div>Срок доставки: <?=$arItem["PROPERTY_DURATION_VALUE"]?></div>
							<div>Звонок	перед приездом: <?=$arItem["PROPERTY_CALL_VALUE"]?></div>
						</div>
					<?endforeach;?>
				<?endif;?>
			</div>
			<div style="margin-top: 10px;">
				<b>Свои курьеры: </b>
				<input data-href="courier.php?LINK_TO_ELEMENT=<?=$arResult["ELEMENT"]["ID"]?>" class="submitBeforeRedirect" type="button" value="+"/>
				<?if(!empty($arResult["COURIER"])):?>
					<?foreach ($arResult["COURIER"] as $key => $arItem):?>
						<div class="<?=$arItem["ID"]?>" style="margin-top: 5px;outline: 2px solid #000; width: 350px;">
							<div class="edit-courier"><a href="courier.php?CODE=<?=$arItem["ID"]?>">Edit</a></div>
							<div class="delete"><a class="<?=$arItem["ID"]?>">X</a></div>
							<div>Регион доставки: <?=$arItem["PROPERTY_REGION_VALUE"]?></div>
							<div>Заказы доставляются: <?#=$arItem[""]?></div>
							<div>Курьер позвонит перед приездом: <?=$arItem["PROPERTY_CALL_VALUE"]?></div>
							<div>Интервал времени в который доставляется заказ: <?=$arItem["PROPERTY_INTERVAL_VALUE"]?></div>
							<div>Время ожидания курьером на месте доставки: <?=$arItem["PROPERTY_WAITING_TIME_VALUE"]?></div>
							<div>Заказ будет доставлен после подтверждения: <?=$arItem["PROPERTY_WHEN_VALUE"]?></div>
							<div>Единая стоимость доставки: <?=$arItem["PROPERTY_COAST_VALUE"]?>, бесплатно от <?=$arItem["PROPERTY_FREE_VALUE"]?></div>
							<div>Подъем на этаж: <?=$arItem["PROPERTY_FLOOR_VALUE"]?></div>
							<div>Дополнительное описание: <?=$arItem["PROPERTY_DESC_VALUE"]["TEXT"]?></div>
						</div>
					<?endforeach;?>
				<?endif;?>
			</div>
			<div style="margin-top: 10px;">
				<b>Курьерские службы: </b>
				<input data-href="courier_service.php?LINK_TO_ELEMENT=<?=$arResult["ELEMENT"]["ID"]?>" class="submitBeforeRedirect" type="button" value="+"/>
			</div>
		</div>
		<!-- Свой регион end -->
		<!-- Доставка в другие регионы start -->
			<div style="margin-top: 10px;">
				<div style="font-weight: bold;">Доставка в другие регионы</div>
				<div style="margin-top: 5px;">
					Регионы	доставки:<br>
					<select name="multiple-select-regions" multiple="multiple" style="width: 300px;">
						<? $def_value_regions = $arResult["ELEMENT_PROPERTIES"][61]; ?>
						<? $countSelect = 0; ?>
						<? $opt = ""; ?>
						<?foreach($arResult["REGIONS"] as $key => $arItem):?>
							<?foreach($def_value_regions as $keyReg => $arItemReg):?>
								<? if($arItemReg["VALUE"] == $arItem): ?>
									<? $opt = "selected"; ?>
									<? $countSelect = 1; ?>
								<? endif; ?>
							<?endforeach;?>
								<option <?=$opt; ?> value="<?=$key;?>"><?=$arItem; ?></option>
							<?if($countSelect == 1):?>
								<? $opt = ""; ?>
							<?endif;?>
						<?endforeach;?>
					</select>
				</div>
				<div style="margin-top: 10px;">
					<div style="margin-top: 10px;"><?printKioskEditPropRow($arResult["PROPERTY_LIST_FULL"][62], $arResult, $arParams);?></div>
					<div style="margin-top: 10px;"><?printKioskEditPropRow($arResult["PROPERTY_LIST_FULL"][63], $arResult, $arParams);?></div>
				</div>
			</div>
		<!-- Доставка в другие регионы end -->
		<!-- Способы оплаты start -->
			<div style="margin-top: 10px;">
				<div style="font-weight: bold;">Способы оплаты</div>
				<div style="margin-top: 10px;"><?printKioskEditPropRow($arResult["PROPERTY_LIST_FULL"][64], $arResult, $arParams);?></div>
				<div style="margin-top: 10px;"><?printKioskEditPropRow($arResult["PROPERTY_LIST_FULL"][65], $arResult, $arParams);?></div>
				<div style="margin-top: 10px;"><?printKioskEditPropRow($arResult["PROPERTY_LIST_FULL"][66], $arResult, $arParams);?></div>
				<div style="margin-top: 10px;"><?printKioskEditPropRow($arResult["PROPERTY_LIST_FULL"][67], $arResult, $arParams);?></div>
				<div style="margin-top: 10px;"><?printKioskEditPropRow($arResult["PROPERTY_LIST_FULL"][68], $arResult, $arParams);?></div>
			</div>
		<!-- Способы оплаты end -->
		<br>
		<?foreach($arResult["PROPS"] as $ID):?>
			<?printKioskProp($ID)?>
		<?endforeach?>
		<input type="submit" name="iblock_submit" value="<?=GetMessage("IBLOCK_FORM_SUBMIT")?>" />
	</form>
</div>
<script>
	$(function(){
		// city regions specialty 
		$("select[name*='PROPERTY[60][0][VALUE]']").select2();
		$("select[name*='multiple-select-regions']").select2();
		$("select[name*='specialty-select']").select2();

		// add regions
		$("select[name*='multiple-select-regions']").change(function() {
			/*var regions  = $(".select2-selection__choice"),
				arRegion = {};
			arRegion.val = {};
			arRegion["id"] = <?=$arResult["ELEMENT"]["ID"];?>;
			arRegion["what"] = "regions"
			for (var i = regions.length - 1; i >= 0; i--) {
				arRegion.val[i] = regions[i].title;
			};
			$.ajax({
				url:'/ajax/kiosk-editor/regions.php',
				type:'POST',
				dataType:'html',
				data: arRegion,
				success: function(result) { }
			})*/
		})

		// add city
		$("select[name*='PROPERTY[60][0][VALUE]']").change(function() {
			console.log($("select[name*='PROPERTY[60][0][VALUE]']").val());
			var city_obj = {};
			city_obj["id"] = <?=$arResult["ELEMENT"]["ID"];?>;
			city_obj["type"] = "city";
			city_obj.val = {};
			city_obj.val = $("select[name*='PROPERTY[60][0][VALUE]']").val();
			$.ajax({
				url:'/ajax/kiosk-editor/regions.php',
				type: 'POST',
				dataType: 'html',
				data: city_obj,
				success: function(result) { 
					console.log(result);
					$('.60_0').val(city_obj.val); 
				}
			});
			/*var city = $(".select2-selection__rendered"),
				city_obj = {};
			city_obj["id"] = <?=$arResult["ELEMENT"]["ID"];?>;
			city_obj["what"] = "city";
			city_obj.val = {};
			city_obj.val = city[0].innerText;
			$.ajax({
				url: '/ajax/kiosk-editor/regions.php',
				type: 'POST',
				dataType: 'html',
				data: city_obj,
				success: function(result) { }
			})*/
		})

		// add specialty
		$(".btnAddSpec").click(function() {
			var name       = $('.specialty-text').val(),
				select     = $("select[name*='specialty-select']").val(),
				have       = [],
				spec_class = $('.spec').children();
			for (var i = 0; i < spec_class.length; i++) {
				have.push(spec_class[i].className);
			}
			$.ajax({
				url:'/ajax/kiosk-editor/specialty.php',
				method:'POST',
				dataType:'json',
				data:{
					ID:'<?=$arResult["ELEMENT"]["ID"]?>',
					name: name,
					select: select,
					have: have,
					what: "add"
				},
				success:function(result){
					$('.specialty-text').val('');
					$('.spec').append('<li style="margin-top: 3px;" class="' + result.el_id + '">' + name + ' <a class="' + result.el_id + '">x</a></li>');
				}
			});
		})

		// delete specialty
		$('.spec > li > a').click(function() {
			var id_spec = $(this)[0].className;
			$.ajax({
				url: '/ajax/kiosk-editor/specialty.php',
				method: 'POST',
				dataType: 'json',
				data:{
					ID: id_spec,
					what: "delete"
				},
				success: function(result){
					$(".spec > ." + id_spec).remove();
				}
			})
		})

		// save specialty city regions



		// delete shops
		// delete delivery
		// delete courier
		$('.delete > a').click(function() {
			var ID = $(this)[0].className;
			$.ajax({
				url: '/ajax/kiosk-editor/delete.php',
				method: 'POST',
				dataType: 'html',
				data:{
					ID: ID
				},
				success: function(result){
					$("div." + ID).remove();
				}
			})
		})

		//------------------------------------------------------------------------------------------

		var $kioskEditForm = $(".kioskEditFrom");
		var onAfterKioskEditMultipleStringInputAddFunctionsList = [];
		var onAfterKioskEditMultipleStringInputAdd = function($newInput){
			for(i in onAfterKioskEditMultipleStringInputAddFunctionsList){
				onAfterKioskEditMultipleStringInputAddFunctionsList[i]($newInput);
			}
		}
		$(".btnMultipleMore").click(function(){
			var $this = $(this);
			var $newInput = $("input:first", $this.parent()).clone().val("");
			$newInput.insertBefore($this);

			if(typeof onAfterKioskEditMultipleStringInputAdd == "function"){
				onAfterKioskEditMultipleStringInputAdd($newInput);
			}
		});
		var $koiskWorkTimeContainer = $(".koiskWorkTimeContainer");
		$(".btnTimePeriodAdd").click(function(){
			$.ajax({
				url:'/ajax/time-period/add.php',
				method:'POST',
				dataType:'html',
				data:{
					LINK_TO_ELEMENT:'<?=$arResult["ELEMENT"]["ID"]?>'
				},
				success:function(result){
					var $tmp = $("<div/>").html(result);
					var newTimePeriod = $tmp.find(".timePeriod");
					timePeriod(newTimePeriod);
					$koiskWorkTimeContainer.append(newTimePeriod).append($("<br/>"));
					delete $tmp;
				}
			});
		});
		$(".kioskEditContainer").on("click", ".btnDeleteKioskElement", function(){
			var $this = $(this);
			var $elementContainer = $this.parent(".kioskElementContainer");
			var ID = $this.data("element-id");
			var IBLOCK_ID = $this.data("iblock-id");
			$.ajax({
				url: '/ajax/kiosk-editor/delete-element.php',
				method:"POST",
				dataType:"json",
				data:{
					ID:ID,
					IBLOCK_ID:IBLOCK_ID
				},
				success:function(result){
					if(result.success){
						$elementContainer.remove();
					}
				}
			});
		});
		var $kioskYandexSpecialization = $("#kioskYandexSpecialization");
		var autocomplateSpecialisation = {
			source:function (request, response) {
				$.ajax({
					url:"/ajax/kiosk-editor/specialisation.php",
					method:"POST",
					dataType:"json",
					data: {
						query:request.term,
						maxItems: 10
					},
					success:function(result){
						if(result.success)
							response(result.data);
					}
				});
			},
			minLength: 3
		};
		$("input" , $kioskYandexSpecialization).autocomplete(autocomplateSpecialisation);

		console.log(onAfterKioskEditMultipleStringInputAddFunctionsList.length);
		onAfterKioskEditMultipleStringInputAddFunctionsList[onAfterKioskEditMultipleStringInputAddFunctionsList.length + 1] = function($newInput){
			$newInput.autocomplete(autocomplateSpecialisation);
		}
		$.fn.serializeObject = function()
		{
			var o = {};
			var a = this.serializeArray();
			$.each(a, function() {
				if (o[this.name] !== undefined) {
					if (!o[this.name].push) {
						o[this.name] = [o[this.name]];
					}
					o[this.name].push(this.value || '');
				} else {
					o[this.name] = this.value || '';
				}
			});
			return o;
		};
		$(".submitBeforeRedirect").click(function(){
			var $this = $(this);
			var data = $kioskEditForm.serializeObject();
			data["ajaxSubmitKioskEditForm"] = "Y";
			data["iblock_submit"] = "Y";
			$.ajax({
				url:"",
				method:"POST",
				dataType:"json",
				data:data,
				success:function(result){
					if(result.success) {
						window.location.href = $this.data("href");
						console.log(result);
					}
					else {
						alert(result.ERROR_MESSAGE);
					}
				}
			});
			return false;
		});
		$('.kioskEditContainer').on("change", ".checkboxKioskElementActive", function(){
			var $this = $(this);
			var data = {};
			data["ACTIVE"] = $this.prop("checked") ? "Y" : "N";
			data["ID"] = $this.data("element-id");
			data["IBLOCK_ID"] = $this.data("iblock-id");

			$.ajax({
				url:"/ajax/kiosk-editor/set-active.php",
				method:"POST",
				dataType:"json",
				data:data
			});
		});
	});
</script>
