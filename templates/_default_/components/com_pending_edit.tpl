{add_js file='includes/jqueryui/jquery-ui.min.js'}
{add_css file='includes/jqueryui/css/smoothness/jquery-ui.min.css'}
{add_js file='includes/jqueryui/i18n/jquery.ui.datepicker-ru.min.js'}

{add_css file='includes/jqueryui/timepicker/jquery.ui.timepicker.css'}
{add_js file='includes/jqueryui/timepicker/jquery.ui.timepicker.js'}

<div class="con_heading">{$pagetitle}</div>

<form id="addform" name="addform" method="post" action="" enctype="multipart/form-data">
    <div class="bar" style="padding:15px 10px">
    <table width="700" cellspacing="5" cellpadding="3" class="proptable">
        <tr>
            <td width="230" valign="top">
                <strong>{$LANG.TITLE}:</strong>
            </td>
            <td valign="top">
                <input name="title" type="text" class="text-input" id="title" style="width:350px" value="{$item.title|escape:'html'}" />
            </td>
        </tr>
        <tr>
            <td valign="top">
                <strong>{$LANG.TAGS}:</strong><br />
                <span class="hinttext">{$LANG.KEYWORDS_TEXT}</span>
            </td>
            <td valign="top">
                <input name="tags" type="text" class="text-input" id="tags" style="width:350px" value="{$item.tags|escape:'html'}" />
                <script type="text/javascript">
                    {$autocomplete_js}
                </script>
            </td>
        </tr>
        <tr>
            <td valign="top">
                <strong>{$LANG.CAT}:</strong>
            </td>
            <td valign="top">
                <select name="category_id" id="category_id" style="width:357px">
                    <option value="">{$LANG.SELECT_CAT}</option>
                    {foreach key=p item=pubcat from=$cats}
                        <option value="{$pubcat.id}" {if $item.category_id==$pubcat.id}selected="selected"{/if}>
                            {'--'|str_repeat:$pubcat.NSLevel} {$pubcat.title}
                         </option>
                    {/foreach}
                </select>
            </td>
        </tr>
        <tr>
            <td valign="top" style="padding-top:8px">
                <strong>{$LANG.IMAGE}:</strong>
            </td>
            <td>
                {if $item.image}
                    <div style="padding-bottom:10px">
                        <img src="/images/photos/small/{$item.image}" border="0" />
                    </div>
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td width="16"><input type="checkbox" id="delete_image" name="delete_image" value="1" /></td>
                            <td><label for="delete_image">{$LANG.DELETE}</label></td>
                        </tr>
                    </table>
                {/if}
                <input type="file" name="picture" style="width:350px" />
            </td>
        </tr>
        <tr>
            <td valign="top">
                <strong>Статус статьи:</strong><br />
                <div><span class="hinttext">Публикуются статьи Готовые к публикации</span></div>
            </td>
            <td valign="top">
                <select name="published" id="published" style="width:357px">
                    <option value="1" {if $item.published}selected="selected"{/if}>Готова к публикации</option>
                    <option value="0" {if !$item.published}selected="selected"{/if}>Черновик</option>
                </select>
            </td>
        </tr>
        <tr>
            <td valign="top">
                <strong>Дата и время публикации:</strong><br />
                <div><span class="hinttext">Если статья готова к публикации она будет опубликована в это время</span></div>
            </td>
            <td valign="top">
                <table>
                    <tr>
                        <td>Дата:<br>
                            <input type="text" name="tpubdate" id="tpubdate" class="text-input" value="{$item.tpubdate}"><br>
                            <span class="hinttext">формат ДД.ММ.ГГГГ</span>
                        </td>
                        <td>Время:<br>
                            <input placeholder="00:00" type="text" name="tpubtime" id="tpubtime" class="text-input" value="{if $item.tpubtime}{$item.tpubtime}{else}00:00{/if}"><br>
                            <span class="hinttext">формат ЧЧ:ММ</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    </div>
                            
    <table width="100%" border="0">
        <tr>
            <td>
            <h3>{$LANG.ARTICLE_ANNOUNCE}</h3>
            <div>{wysiwyg name='description' value=$item.description height=200 width='100%'}</div>
            <h3>{$LANG.ARTICLE_TEXT}</h3>
            <div>{wysiwyg name='content' value=$item.content height=450 width='100%'}</div>
            </td>
        </tr>
    </table>

    <p style="margin-top:15px">
        <input name="add_mod" type="hidden" value="1" />
        <input name="savebtn" type="button" onclick="submitArticle()" id="add_mod" {if $do=='addarticle'} value="{$LANG.ADD_ARTICLE}" {else} value="{$LANG.SAVE_CHANGES}" {/if} />
        <input name="back" type="button" id="back" value="{$LANG.CANCEL}" onclick="window.history.back();"/>
        {if $do=='editarticle'}<input name="id" type="hidden" value="{$item.id}" />{/if}
    </p>
</form>
    
<script type="text/javascript">
var LANG_SELECT_CAT = '{$LANG.SELECT_CAT}';
var LANG_REQ_TITLE  = '{$LANG.REQ_TITLE}';
var LANG_ERROR      = '{$LANG.ERROR}';
function submitArticle(){
    if (!$('input#title').val()){ core.alert(LANG_REQ_TITLE, LANG_ERROR); return false; }
    {if $do=='addarticle'}
            if (!$('select#category_id').val()){ core.alert(LANG_SELECT_CAT, LANG_ERROR); return false; }
    {/if}
    $('form#addform').submit();
}
{literal}
$(function() {
    $("#tpubdate").datepicker({minDate: 0});
    $('#tpubtime').timepicker();
});
{/literal}
</script>
