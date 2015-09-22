<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="b-grid b-magic b-template" style="background-color: #EEE;">
	<div class="b-personal-files_inner">
		<div class="list_title">
			<a href="/docs/vnd/"><span class="left_arrow">&nbsp;</span></a>
			<h2><?=GetMessage('SECTION_TITLE')?></h2>
		</div>
		<div class="current_section <?if(empty($arResult['BREADCRUMB_STRING'])) echo 'inact'?>">
			<span class='first_menu_button'></span><div class="breadcrumb_string"><?=$arResult['BREADCRUMB_STRING']?></div>
			<div class="first_level_menu">
				<?foreach($arResult['FIRST_LEVEL_MENU'] as $menu):?>
					<p><a href="<?=$menu['LIST_PAGE_URL'].$menu['ID'].'/'?>"><?=$menu['NAME']?></a></p>
				<?endforeach;?>
			</div>
		</div>		
			<div class="search_block">
				<div class="search_wrapper">
					<form action="<?=POST_FORM_ACTION_URI?>" name="search_form" method="GET">
						<input name="element_name" class="search_field" type="text" value="<?=$_GET['element_name']?>" placeholder="<?=GetMessage('SEARCH_FOR_NAME_OR_FILE_CONTENT')?>">
						<select name="element_status" id="search_status" class="search_status">
							<option value="0" <?if(empty($_GET['element_status'])) echo 'selected';?>><?=GetMessage('ANY_STATUS')?></option>
							<?foreach($arResult['STATUS_VALUES'] as $value):?>
								<option value="<?=$value['XML_ID']?>" <?if($_GET['element_status'] == $value['XML_ID']) echo 'selected';?>><?=$value['VALUE']?></option>
							<?endforeach;?>
						</select>
						<input name="doc_num" class="doc_num_filter" type="text" value="<?=htmlspecialchars($_GET['doc_num'])?>" placeholder="<?=GetMessage('SEARCH_FOR_DOC_NUM')?>">
						<?=GetMessage('ACCEPT_FROM')?>
						<?$APPLICATION->IncludeComponent("bitrix:main.calendar","vnd",Array(
							 "SHOW_INPUT" => "Y",
							 "FORM_NAME" => "search_form",
							 "INPUT_NAME" => "date_from",
							 "INPUT_NAME_FINISH" => "date_to",
							 "INPUT_VALUE" => $_GET['date_from'],
							 "INPUT_VALUE_FINISH" => $_GET['date_to'], 
							 "SHOW_TIME" => "N",
							 "HIDE_TIMEBAR" => "Y"
							)
						);?>
						
						<input type="submit" name="submit_filter" class="search_button" value="<?=GetMessage('ACCEPT')?>">
					</form>
				</div>
			</div>
			<?if(!empty($_GET['submit_filter'])):?>
				<div class="cancel_filter">
					<a href="<?=$APPLICATION->GetCurPage(false)?>"><?=GetMessage('CANCEL_FILTER')?></a>
				</div>
			<?endif;?>
			<?if($arResult['TYPE']==2 || !empty($_GET['submit_filter'])):?>
			
			<div class="files_sections">
				<?if(!empty($arResult['ELEMENTS'])):?>
					<div class="main_header">
						<div class="header position">Положения</div>
						<div class="header status">Статус</div>
						<div class="header approved">
							<div>Утверждено</div>
							<div class="doc_number">№</div>
							<div class="doc_date">Дата</div>
						</div>
						<div class="header doc_comment">Комментарий</div>
					</div>
					<div class="clear_both"></div>
						<?foreach($arResult['ELEMENTS'] as $element):?>
							<div class="files_each">
								<div class="files_name">
									<span class="element_name"><?=$element['NAME']?></span>
								</div>
								<div class="files_status">
									<?=$element['STATUS']?>
								</div>
								<div class="files_number">
									<?=$element['DOCUMENT_NUMBER']?>
								</div>
								<div class="files_date">
									<?=$element['DOC_DATE']?>
								</div>
								<div class="files_comment">
									<?=$element['COMMENT']?>
								</div>
								<div class="files_more_less">
									<span><?=GetMessage('MORE_LESS')?></span>
									<span class="icon_more_less"></span>
								</div>
								<div style="clear:both;"></div>
								<div class="files_list">
									<?if(!empty($element['FILES'])):?>
										<?foreach($element['FILES'] as $v):?>
											<p class="file_type_<?=$v['TYPE_FILE']?>"><span class="file-type-pic"></span><a download target="_blank" href="<?=$v['PATH']?>"><?=$v['FILE_NAME']?></a></p>
										<?endforeach;?>
									<?endif;?>
								</div>
								<div class="old_files">
									<div class="old_files_title"><span><?=GetMessage('PREV_VERS')?></span></div>
									<?foreach($element['OLD_VERS_ARR'] as $oldVer):?>
									<div class="files_each_old">
										<div class="old_files_name">
											<span class="old_element_name"><?=$oldVer['NAME']?></span>
											<div class="old_files_list">
												<?if(!empty($oldVer['FILES'])):?>
													<?foreach($oldVer['FILES'] as $val):?>
														<p class="file_type_<?=$val['TYPE_FILE']?>"><span class="file-type-pic"></span><a download target="_blank" href="<?=$val['PATH']?>"><?=$val['FILE_NAME']?></a></p>
													<?endforeach;?>
												<?endif;?>
											</div>
										</div>
										<div class="old_files_status">
											<?=$oldVer['STATUS']?>
										</div>
										<div class="old_files_comment">
											<?=$oldVer['COMMENT']?>
										</div>
										<div class="old_files_more_less">
										</div>
									</div>
									<div style="clear:both;"></div>
									<?endforeach;?>
								</div>
							</div>
						<?endforeach;?>					
				<?else:?>
					<div class="no_results">
						<p><?=GetMessage('NO_RESULTS')?></p>
					</div>
				<?endif?>
			</div>
		<?elseif($arResult['TYPE']==1):?>
			<?if(!empty($arResult['SECTIONS'])):?>
				<div class="sub_sections">
					<?foreach($arResult['SECTIONS'] as $section):?>
						<a class="list_link" href="/docs/vnd/<?=$section['ID']?>/" ><?=$section['NAME']?></a>
						<div style="clear:both"></div>
					<?endforeach;?>
				</div>
			<?else:?>
				<div class="no_results">
					<p><?=GetMessage('NO_RESULTS')?></p>
				</div>
			<?endif?>
		<?endif;?>
	</div>
</div>