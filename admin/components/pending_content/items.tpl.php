<?php if(!defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); } ?>

<table cellpadding="0" cellspacing="0" border="0" width="100%" height="100%" style="margin-top:2px">
    <tr>
        <td valign="top" width="240" style="<?php if ($hide_cats){ ?>display:none;<?php } ?>" id="cats_cell">

            <div class="cat_add_link">
                <div>
                    <a href="?view=cats&do=add" style="color:#09C">Добавить раздел</a>
                </div>
            </div>
            <div class="cat_link">
                <div>
                <?php if (!$only_hidden) { ?>
                    <a href="<?php echo $base_uri.'&orderby=pubdate&orderto=desc&only_hidden=1'; ?>" style="font-weight:bold">На модерации</a>
                <?php } else { $current_cat = 'На модерации'; ?>
                    На модерации
                <?php } ?>
                </div>
                <div>
                <?php if ($category_id || $only_hidden) { ?>
                    <a href="<?php echo $base_uri; ?>" style="font-weight:bold">Все страницы</a>
                <?php } else { $current_cat = 'Все страницы'; ?>
                    Все страницы
                <?php } ?>
                </div>
            </div>
            <div class="cat_link">
                <div>
                <?php if ($category_id != 1) { ?>
                    <a href="<?php echo $base_uri.'&cat_id=1'; ?>" style="font-weight:bold">Корневой раздел</a>
                <?php } else { $current_cat = 'Корневой раздел'; ?>
                    Корневой раздел
                <?php } ?>
                </div>
            </div>
            <?php if (is_array($cats)){ ?>
                <?php foreach($cats as $num=>$cat) { ?>
                    <div style="padding-left:<?php echo ($cat['NSLevel'])*20; ?>px" class="cat_link">
                        <div>
                            <?php if ($category_id != $cat['id']) { ?>
                                <a href="<?php echo $base_uri.'&cat_id='.$cat['id']; ?>" style="<?php if ($cat['NSLevel']==1){ echo 'font-weight:bold'; } ?>"><?php echo $cat['title']; ?></a>
                            <?php } else { ?>
                                <?php echo $cat['title']; $current_cat = $cat['title']; ?>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
        </td>

        <td valign="top" id="slide_cell" class="<?php if ($hide_cats){ ?>unslided<?php } ?>" onclick="$('#cats_cell').toggle();$(this).toggleClass('unslided');$('#filter_form input[name=hide_cats]').val(1-$('#cats_cell:visible').length)">&nbsp;

        </td>

        <td valign="top" style="padding-left:2px">

            <form action="<?php echo $base_uri; ?>" method="GET" id="filter_form">
                <input type="hidden" name="view" value="components" />
                <input type="hidden" name="do" value="config" />
                <input type="hidden" name="id" value="<?php echo $component_id; ?>" />
                <input type="hidden" name="opt" value="list_items" />
                <input type="hidden" name="cat_id" value="<?php echo $category_id; ?>" />
                <input type="hidden" name="hide_cats" value="<?php echo $hide_cats; ?>" />
                <table class="toolmenu" cellpadding="5" border="0" width="100%" style="margin-bottom: 2px;">
                    <tr>
                        <td width="">
                            <span style="font-size:16px;color:#0099CC;font-weight:bold;">
                                <?php echo $current_cat; ?> <?php if($category_id){ ?>[id=<?php echo $category_id; ?>]<?php } ?>
                            </span>
                            <span style="padding-left: 15px;">
                                <a title="Добавить статью" href="?view=components&do=config&id=<?php echo $component_id; ?>&opt=add<?php if($category_id){ ?>&to=<?php echo $category_id; } ?>">
                                    <img border="0" hspace="2" alt="Добавить статью" src="images/actions/add.gif"/>
                                </a>
                                <?php if($category_id>1){ ?>
                                    <a title="Редактировать раздел" href="?view=cats&do=edit&id=<?php echo $category_id; ?>">
                                        <img border="0" hspace="2" alt="Редактировать раздел" src="images/actions/edit.gif"/>
                                    </a>
                                <?php } ?>
                            </span>
                        </td>
                    </tr>
                </table>
                <table class="toolmenu" cellpadding="5" border="0" width="100%" style="margin-bottom: 2px;" id="filterpanel">
                    <tr>
                        <td width="130">
                            <select name="orderby" style="width:130px" onchange="$('#filter_form').submit()">
                                <option value="title" <?php if($orderby=='title'){ ?>selected="selected"<?php } ?>>по названию</option>
                                <option value="pubdate" <?php if($orderby=='pubdate'){ ?>selected="selected"<?php } ?>>по дате</option>
                            </select>
                        </td>
                        <td width="150">
                            <select name="orderto" style="width:150px" onchange="$('#filter_form').submit()">
                                <option value="asc" <?php if($orderto=='asc'){ ?>selected="selected"<?php } ?>>по возрастанию</option>
                                <option value="desc" <?php if($orderto=='desc'){ ?>selected="selected"<?php } ?>>по убыванию</option>
                            </select>
                        </td>
                        <td width="60">Название:</td>
                        <td width="">
                            <input type="text" name="title" value="<?php echo $title_part; ?>" style="width:99%"/>
                        </td>
                        <td width="30">
                            <input type="submit" name="filter" value="&raquo;" style="width:30px"/>
                        </td>
                    </tr>
                </table>
            </form>

            <form name="selform" action="index.php?view=components" method="post">
                <table id="listTable" class="tablesorter" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-top:0px">
                    <thead>
                        <tr>
                            <th class="lt_header" align="center" width="20">
                                <a class="lt_header_link" title="Инвертировать выделение" href="javascript:" onclick="javascript:invert()">#</a>
                            </th>
                            <th class="lt_header" width="25">id</th>
                            <th class="lt_header" width="" colspan="2">Название</th>
                            <th class="lt_header" width="80" align="center">Дата публикации</th>
                            <th class="lt_header" width="50">Показ</th>
                            <th class="lt_header" align="center" width="90">Действия</th>
                        </tr>
                    </thead>
                    <?php if ($items){ ?>
                        <tbody>
                            <?php foreach($items as $num=>$item){ ?>
                                <tr id="<?php echo $item['id']; ?>" class="item_tr">
                                    <td><input type="checkbox" name="item[]" value="<?php echo $item['id']; ?>" /></td>
                                    <td><?php echo $item['id']; ?></td>
                                    <td width="16">
                                        <img src="/templates/_default_/images/icons/article.png" border="0"/>
                                    </td>
                                    <td>
                                        <a href="index.php?view=components&do=config&id=<?php echo $component_id; ?>&opt=edit&item_id=<?php echo $item['id']; ?>">
                                            <?php echo $item['title']; ?>
                                        </a>
                                    </td>
                                    <td><?php echo $item['fpubdate']; ?></td>
                                    <td>
                                        <?php if ($item['published']) { ?>
                                            <a id="publink<?php echo $item['id']; ?>" href="javascript:pub(<?php echo $item['id']; ?>, 'view=components&do=config&id=<?php echo $component_id; ?>&opt=hide&item_id=<?php echo $item['id']; ?>', 'view=components&do=config&id=<?php echo $component_id; ?>&opt=show&item_id=<?php echo $item['id']; ?>', 'off', 'on');" title="Скрыть">
                                                <img id="pub<?php echo $item['id']; ?>" border="0" src="images/actions/on.gif"/>
                                            </a>
                                        <?php } else { ?>
                                            <a id="publink<?php echo $item['id']; ?>" href="javascript:pub(<?php echo $item['id']; ?>, 'view=components&do=config&id=<?php echo $component_id; ?>&opt=show&item_id=<?php echo $item['id']; ?>', 'view=components&do=config&id=<?php echo $component_id; ?>&opt=hide&item_id=<?php echo $item['id']; ?>', 'on', 'off');" title="Показать">
                                                <img id="pub<?php echo $item['id']; ?>" border="0" src="images/actions/off.gif"/>
                                            </a>
                                        <?php } ?>
                                    </td>
                                    <td align="right">
                                        <div style="padding-right: 8px;">
                                            <a title="Редактировать" href="?view=components&do=config&id=<?php echo $component_id; ?>&opt=edit&item_id=<?php echo $item['id']; ?>">
                                                <img border="0" hspace="2" alt="Редактировать" src="images/actions/edit.gif"/>
                                            </a>
                                            <a title="Удалить" onclick="jsmsg('Удалить <?php echo $item['title']; ?>?', '?view=components&do=config&id=<?php echo $component_id; ?>&opt=delete&item_id=<?php echo $item['id']; ?>')" href="#">
                                                <img border="0" hspace="2" alt="Удалить" src="images/actions/delete.gif"/>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    <?php } else { ?>
                        <tbody>
                            <td colspan="6" style="padding-left:5px"><div style="padding:15px;padding-left:0px">Статьи не найдены</div></td>
                        </tbody>
                    <?php } ?>
                </table>
                <?php if ($items){ ?>

                    <div style="margin-top:4px;padding-top:4px;">
                        <table class="" cellpadding="5" border="0" height="40">
                            <tr>
                                <td width="">
                                   <strong style="color:#09C">Отмеченные:</strong>
                                </td>
                                <td width="" class="sel_pub">
                                    <input type="button" name="" value="Редактировать" onclick="sendContentForm(<?php echo $component_id; ?>, 'edit');" />
                                    <input type="button" name="" value="Перенести" onclick="$('.sel_move').toggle();$('.sel_pub').toggle();" />
                                </td>
                                <td class="sel_move" style="display:none">
                                    Перенести в раздел
                                </td>
                                <td class="sel_move" style="display:none">
                                    <select id="move_cat_id" style="width:250px">
                                        <option value="1">Корневой раздел</option>
                                        <?php
                                           echo $inCore->getListItemsNS('cms_category', $category_id);
                                        ?>
                                    </select>
                                </td>
                                <td class="sel_move" style="display:none">
                                    <input type="button" name="" value="ОК" onclick="sendContentForm(<?php echo $component_id; ?>, 'move_to_cat', $('select#move_cat_id').val(), <?php echo $category_id; ?>);" />
                                    <input type="button" name="" value="Отмена" onclick="$('td.sel_move').toggle();$('td.sel_pub').toggle();" /> Внимание! URL переносимых статей изменится согласно категории.
                                </td>
                                <td class="sel_pub">
                                    <input type="button" name="" value="Показать" onclick="sendContentForm(<?php echo $component_id; ?>, 'show');" />
                                    <input type="button" name="" value="Скрыть" onclick="sendContentForm(<?php echo $component_id; ?>, 'hide');" />
                                </td>
                                <td class="sel_pub">
                                    <input type="button" name="" value="Удалить" onclick="sendContentForm(<?php echo $component_id; ?>, 'delete');" />
                                </td>
                            </tr>
                        </table>
                    </div>
                <?php } ?>
                <script type="text/javascript">highlightTableRows("listTable","hoverRow","clickedRow");</script>
            </form>

            <?php
                if ($pages>1){
                    echo cmsPage::getPagebar($total, $page, $perpage, $base_uri.'&hide_cats='.$hide_cats.'&title='.$title_part.'&orderby='.$orderby.'&orderto='.$orderto.'&cat_id='.$category_id.'&page=%page%');
                }
            ?>
        </td>
    </tr>
</table>