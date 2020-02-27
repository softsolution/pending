<div class="float_bar">
    <a href="/pending/add{if $category_id}{$category_id}{/if}.html" class="usr_article_add">{$LANG.ADD_ARTICLE}</a>
</div>

<h1 class="con_heading">{$pagetitle}</h1>

<table width="100%">
    <tbody>
        <tr>
            <td class="redactor-left-col" valign="top">
                <div class="categories_box">
                    <div>
                    {if !$only_draft}
                        <a href="/pending/drafts" style="font-weight:bold">{$LANG.DRAFTS}</a>
                    {else}
                        {$LANG.DRAFTS}
                    {/if}
                    </div>
                    <div>
                    {if $category_id || $only_draft}
                        <a href="/pending" style="font-weight:bold">{$LANG.PAGE_ALL}</a>
                    {else}
                        {$LANG.PAGE_ALL}
                    {/if}
                    </div>
                    <div class="cat_link">
                        <div>
                        {if $category_id != 1}
                            <a href="/pending/cat1" style="font-weight:bold">{$LANG.ROOT_CATEGORY}</a>
                        {else}
                            {$LANG.ROOT_CATEGORY}
                        {/if}
                        </div>
                    </div>
                    {if $cats}
                        {foreach $cats as $num=>$cat}
                            <div style="padding-left:{math equation="x*y" x=$cat.NSLevel y=20}px" class="cat_link">
                                <div>
                                    {if $category_id != $cat.id}
                                        <a href="/pending/cat{$cat.id}" style="{if $cat.NSLevel==1}font-weight:bold{/if}">{$cat.title}</a>
                                    {else}
                                        {$cat.title}
                                    {/if}
                                </div>
                            </div>
                        {/foreach}
                    {/if}
                </div>
            </td>
            <td class="redactor-right-col" valign="top">
                <div class="articles_box">
                    
                    {if $articles}
                    <table width="100%" cellpadding="8" cellspacing="0" border="0" class="art_list">
                        <thead>
                            <tr class="thead">
                                <td width="100"><strong>Дата</strong></td>
                                <td width=""><strong>Заголовок</strong></td>
                                <td width="100" align="center"><strong>{$LANG.STATUS}</strong></td>
                                <td width="100"><strong>{$LANG.CAT}</strong></td>
                                <td width="70" align="center"><strong>{$LANG.ACTION}</strong></td>
                            </tr>
                        </thead>
                        <tbody>
                        {foreach key=tid item=article from=$articles}
                            <tr>
                                <td><div style="font-size: 11px">{$article.tpubdate} в {$article.tpubtime}</div></td>
                                <td><a href="/pending/edit{$article.id}.html">{$article.title}</a> <a href="/pending/read{$article.id}.html" style="font-size:11px;color:#666">[предпросмотр]</a></td>
                                <td align="center">
                                {if $article.published}
                                    <span style="color:green;font-size:11px">Готова к публикации</span>
                                {else}
                                    <span style="color:#CC0000;font-size:11px">Черновик</span>
                                 {/if}
                                </td>
                                <td><a href="/pending/cat{$article.category_id}">{$article.cat_title}</a></td>
                                <td align="center">
                                    <a href="/pending/edit{$article.id}.html" title="{$LANG.EDIT}"><img src="/templates/{template}/images/icons/edit.png" border="0"/></a>
                                    <a href="/pending/delete{$article.id}.html" title="{$LANG.DELETE}"><img src="/templates/{template}/images/icons/delete.png" border="0"/></a>
                                </td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>

                    {$pagebar}
                    <script type="text/javascript">
                    $(document).ready(function(){
                        zebra();
                        function zebra() {
                           $('.art_list tr').not('.thead').removeClass('search_row1').removeClass('search_row2');
                           $('.art_list tr:odd').not('.thead').addClass('search_row1');
                           $('.art_list tr:even').not('.thead').addClass('search_row2');
                        }
                    });
                    </script>
                    {else}
                        <p>{$LANG.NO_PENDING_ARTICLES}</p>
                    {/if}
                    
                </div>
            </td>
        </tr>
    </tbody>
</table>