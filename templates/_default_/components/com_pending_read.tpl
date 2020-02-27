{$article.plugins_output_before}

{if $article.showtitle}
    <h1 class="con_heading">{$article.title}</h1>
{/if}

{if $article.showdate}
    <div class="con_pubdate">
        {if !$article.published}<span style="color:#CC0000">{$LANG.NO_PUBLISHED}</span>{else}{$article.pubdate}{/if} - <a href="{profile_url login=$article.user_login}">{$article.author}</a>
    </div>
{/if}

<div class="con_text" style="overflow:hidden">
    {if $article.image}
        <div class="con_image" style="float:left;margin-top:10px;margin-right:20px;margin-bottom:20px">
            <img src="/images/photos/medium/{$article.image}" alt="{$article.title|escape:html}"/>
        </div>
    {/if}
    {$article.content}
</div>

{if $is_admin || $is_editor || $is_author}
    <div class="blog_comments">
        {if $is_admin || $is_editor || $is_author_del}
            <a class="blog_moderate_no" href="/pending/delete{$article.id}.html">{$LANG.DELETE}</a> |
        {/if}
        {if $is_admin || $is_editor || $is_author}
            <a href="/pending/edit{$article.id}.html" class="blog_entry_edit">{$LANG.EDIT}</a>
        {/if}
    </div>
{/if}

{if $article.showtags}
    {$tagbar}
{/if}

{$article.plugins_output_after}