<?php
/* ************************************************************************** */
/* created by soft-solution.ru, support@soft-solution.ru                      */
/* component pending content for InstantCMS 1.10.6                            */
/* license: commercialcc                                                      */
/* Незаконное использование преследуется по закону                            */
/* ************************************************************************** */
function pending(){

    $inCore = cmsCore::getInstance();
    $inPage = cmsPage::getInstance();
    $inDB   = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();
    $inConf = cmsConfig::getInstance();

    $model = new cms_model_pending();
    
    if (!$inUser->id){ cmsUser::goToLogin(); }
    if (!cmsUser::isUserCan('pending/access')){ cmsCore::error404(); }

    global $_LANG;

    $id = cmsCore::request('id', 'int', 0);
    $do = $inCore->do;

    $page = cmsCore::request('page', 'int', 1);
    
    $target  = cmsCore::request('target', 'str', 'all');

    $pagetitle = $inCore->menuTitle();
    $pagetitle = $pagetitle ? $pagetitle : $_LANG['PENDING'];

    $inPage->setTitle($pagetitle);
    $inPage->addPathway($pagetitle, '/pending');
    $inPage->addHeadCSS('templates/'.$inConf->template.'/css/pending.css');
    
/* ========================================================================== */
/* = LIST ARTICLES + VIEW CATEGORIES ======================================== */
/* ========================================================================== */
    
    if ($do=='view'){
        
        $category_id = cmsCore::request('category_id', 'int', 0);
        $only_draft  = cmsCore::request('only_draft', 'int', 0);

        $pagetitle = $_LANG['PENDING'];
        $perpage = 15;
        
        if($category_id){
            $model->whereCatIs($category_id);
            $page_url = '/pending/cat'.$category_id.'/page-%page%';
        }
        
        if ($only_draft){
            $inDB->where('con.published = 0');
            $page_url = '/pending/drafts/page-%page%';
        }

        $total = $model->getArticlesCount(false);
        $inDB->orderBy('con.pubdate', 'DESC');
        $inDB->limitPage($page, $perpage);

        $content_list = $total ? $model->getArticlesList(false) : array();
        $inDB->resetConditions();
        
        $cats = $model->getCatsTree();
        
        $page_url = $page_url ? $page_url : '/pending/page-%page%';

        cmsPage::initTemplate('components', 'com_pending')->
            assign('articles', $content_list)->
            assign('total', $total)->
            assign('pagebar', cmsPage::getPagebar($total, $page, $perpage, $page_url))->
            assign('pagetitle', $pagetitle)->
            assign('cats', $cats)->
            assign('category_id', $category_id)->
            assign('only_draft', $only_draft)->
            display('com_pending.tpl');

}

/* ========================================================================== */
/* = PREVIEW ARTICLE ======================================================== */
/* ========================================================================== */

    if ($do=='read'){

        $article = $model->getArticle($id);
        if (!$article) { cmsCore::error404(); }

        $is_admin      = $inUser->is_admin;
        $is_author     = $inUser->id == $article['user_id'];
        $is_author_del = cmsUser::isUserCan('content/delete');
        $is_editor     = ($article['modgrp_id'] == $inUser->group_id && cmsUser::isUserCan('content/autoadd'));

        // Картинка статьи
        $article['image'] = (file_exists(PATH.'/images/photos/medium/pending'.$article['id'].'.jpg') ? 'pending'.$article['id'].'.jpg' : '');

        // Заголовок страницы
        $article['pagetitle'] = $article['pagetitle'] ? $article['pagetitle'] : $article['title'];

        // Тело статьи в зависимости от настроек
        $article['content'] = $model->config['readdesc'] ? $article['description'].$article['content'] : $article['content'];

        // Дата публикации
        $article['pubdate'] = cmsCore::dateFormat($article['pubdate']);

        // Шаблон статьи
        $article['tpl'] = $article['tpl'] ? $article['tpl'] : 'com_content_read.tpl';

        $inPage->setTitle($article['pagetitle']);

        $inPage->addPathway($article['title']);

        // Мета теги KEYWORDS и DESCRIPTION
        if ($article['meta_keys']){
            $inPage->setKeywords($article['meta_keys']);
        } else {
            if (mb_strlen($article['content'])>30){
                $inPage->setKeywords(cmsCore::getKeywords(cmsCore::strClear($article['content'])));
            }
        }
        if (mb_strlen($article['meta_desc'])){
            $inPage->setDescription($article['meta_desc']);
        }

        // Выполняем фильтры
        $article['content'] = cmsCore::processFilters($article['content']);
        
        $article['tpl'] = $article['tpl'] == 'com_content_read.tpl' ? 'com_pending_read.tpl' : $article['tpl'];

        cmsPage::initTemplate('components', $article['tpl'])->
            assign('article', $article)->
            assign('cfg', $model->config)->
            assign('is_admin', $is_admin)->
            assign('is_editor', $is_editor)->
            assign('is_author', $is_author)->
            assign('is_author_del', $is_author_del)->
            assign('tagbar', $article['tags'])->
            display($article['tpl']);

    }

/* ========================================================================== */
/* = ADD ARTICLE + EDIT ARTICLE ============================================= */
/* ========================================================================== */
    
if ($do=='addarticle' || $do=='editarticle'){

    if ($do=='editarticle'){

        $item = $model->getArticle($id);
        if (!$item) { cmsCore::error404(); }

        $pubcats = array();

        // доступ к редактированию админам, авторам и редакторам 
        if(!$inUser->is_admin && ($item['user_id'] != $inUser->id) && !($item['modgrp_id'] == $inUser->group_id && cmsUser::isUserCan('content/autoadd'))){
            cmsCore::error404();
        }
    }

    // Для добавления проверяем не вводили ли мы данные ранее
    if ($do=='addarticle'){
        
        $item = cmsUser::sessionGet('article');
        if ($item) { cmsUser::sessionDel('article'); }
        
        if(!$item['tpubdate']){
            $item['tpubdate'] = date("d.m.Y", strtotime("+1 day"));
        }
        
        if(!$item['tpubtime']){
            $item['tpubtime'] = '00:00';
        }
        
        if(!isset($item['published'])){
            $item['published'] = 1;
        }
        
        if(!isset($item['category_id'])){
            $item['category_id'] = cmsCore::request('category_id', 'int', 0);
        }
        
    }

    // не было запроса на сохранение, показываем форму
    if (!cmsCore::inRequest('add_mod')){

        // Если добавляем статью
        if ($do=='addarticle'){
            $pagetitle = $_LANG['ADD_PENDING_ARTICLE'];
            $inPage->setTitle($pagetitle);
            $inPage->addPathway($pagetitle);
        }

        // Если редактируем статью
        if ($do=='editarticle'){

            $pagetitle = $_LANG['EDIT_ARTICLE'];

            $inPage->setTitle($pagetitle);
            $inPage->addPathway($pagetitle);

            $item['image'] = (file_exists(PATH.'/images/photos/small/pending'.$item['id'].'.jpg') ? 'pending'.$item['id'].'.jpg' : '');
        }

        $inPage->initAutocomplete();
        $autocomplete_js = $inPage->getAutocompleteJS('tagsearch', 'tags');

        $item = @$item ? $item : array();
        
        $cats = $model->getCatsTree();

        cmsPage::initTemplate('components', 'com_pending_edit')->
            assign('item', $item)->
            assign('do', $do)->
            assign('cfg', $model->config)->
            assign('cats', $cats)->
            assign('pagetitle', $pagetitle)->
            assign('is_admin', $inUser->is_admin)->
            assign('autocomplete_js', $autocomplete_js)->
            display('com_pending_edit.tpl');

    }

	// Пришел запрос на сохранение статьи
    if (cmsCore::inRequest('add_mod')){

        $errors = false;

        $article['category_id']  = cmsCore::request('category_id', 'int', 1);
        $article['user_id']      = $item['user_id'] ? $item['user_id'] : $inUser->id;
        $article['title']        = cmsCore::request('title', 'str', '');
        $article['tags']         = cmsCore::request('tags', 'str', '');

        $article['description']  = cmsCore::request('description', 'html', '');
        $article['content']      = cmsCore::request('content', 'html', '');
        $article['description']  = cmsCore::badTagClear($article['description']);
        $article['content']      = cmsCore::badTagClear($article['content']);

        $article['published']    = cmsCore::request('published', 'int');
        
        $tpubdate                = cmsCore::request('tpubdate', 'str', date("d.m.Y", strtotime("+1 day")));
        $tpubtime                = cmsCore::request('tpubtime', 'str', '00:00');
        
        if(!strstr($tpubtime, ':')){ $tpubtime = '00:00'; }
        
        //приводим дату публикации к правильному формату
        $pubdate_arr = explode('.', $tpubdate);
        $article['pubdate'] = $pubdate_arr[2] . '-' . $pubdate_arr[1] . '-' . $pubdate_arr[0] . ' ' .$tpubtime;
        
        $article['enddate']      = $do=='editarticle' ? $item['enddate'] : $article['pubdate'];
        $article['is_end']       = $do=='editarticle' ? $item['is_end'] : 0;
        $article['showtitle']    = $do=='editarticle' ? $item['showtitle'] : 1;

        $article['meta_desc']    = $do=='addarticle' ? mb_strtolower($article['title']) : $inDB->escape_string($item['meta_desc']);
        $article['meta_keys']    = $do=='addarticle' ? $inCore->getKeywords($article['content']) : $inDB->escape_string($item['meta_keys']);

        $article['showdate']     = $do=='editarticle' ? $item['showdate'] : 1;
        $article['showlatest']   = $do=='editarticle' ? $item['showlatest'] : 1;
        $article['showpath']     = $do=='editarticle' ? $item['showpath'] : 1;
        $article['comments']     = $do=='editarticle' ? $item['comments'] : 1;
        $article['canrate']      = $do=='editarticle' ? $item['canrate'] : 1;
        $article['pagetitle']    = '';
        
        if ($do=='editarticle'){
           $article['tpl']       = $item['tpl'];
        }

        if (mb_strlen($article['title'])<2){ cmsCore::addSessionMessage($_LANG['REQ_TITLE'], 'error'); $errors = true; }
        if (mb_strlen($article['content'])<10){ cmsCore::addSessionMessage($_LANG['REQ_CONTENT'], 'error'); $errors = true; }

        if($errors) {
            // При добавлении статьи при ошибках сохраняем введенные поля
            if ($do=='addarticle'){ cmsUser::sessionPut('article', $article); }
            cmsCore::redirectBack();
        }

        $article['description']  = $inDB->escape_string($article['description']);
        $article['content']      = $inDB->escape_string($article['content']);

        // добавление статьи
        if ($do=='addarticle'){
            $article_id = $model->addArticle($article);
        }

        // загрузка фото
        $file = 'pending'.(@$article_id ? $article_id : $item['id']).'.jpg';

        if (cmsCore::request('delete_image', 'int', 0)){
            @unlink(PATH."/images/photos/small/$file");
            @unlink(PATH."/images/photos/medium/$file");
        }

        // Загружаем класс загрузки фото
        cmsCore::loadClass('upload_photo');
        $inUploadPhoto = cmsUploadPhoto::getInstance();
        // Выставляем конфигурационные параметры
        $inUploadPhoto->upload_dir    = PATH.'/images/photos/';
        $inUploadPhoto->small_size_w  = $model->config['img_small_w'];
        $inUploadPhoto->medium_size_w = $model->config['img_big_w'];
        $inUploadPhoto->thumbsqr      = $model->config['img_sqr'];
        $inUploadPhoto->is_watermark  = $model->config['watermark'];
        $inUploadPhoto->input_name    = 'picture';
        $inUploadPhoto->filename      = $file;
        // Процесс загрузки фото
        $inUploadPhoto->uploadPhoto();

        // операции после добавления/редактирования статьи
        // добавление статьи
        if ($do=='addarticle'){
            cmsCore::addSessionMessage($_LANG['ARTICLE_SAVE'], 'info');
            cmsCore::redirect('/pending');
        }

        // Редактирование статьи
        if ($do=='editarticle'){

            $model->updateArticle($item['id'], $article, true);
            cmsCore::addSessionMessage($_LANG['ARTICLE_SAVE'], 'info');
            cmsCore::redirect('/pending');

        }
    }
}

/* ========================================================================== */
/* = DELETE ARTICLE ========================================================= */
/* ========================================================================== */

    if ($do=='deletearticle'){

        $article = $model->getArticle($id);
        if (!$article) { cmsCore::error404(); }

        // права доступа
        $is_author = cmsUser::isUserCan('content/delete') && ($article['user_id'] == $inUser->id);
        $is_editor = ($article['modgrp_id'] == $inUser->group_id) && cmsUser::isUserCan('content/autoadd');

        if (!$is_author && !$is_editor && !$inUser->is_admin) { cmsCore::error404(); }

        if (!cmsCore::inRequest('goadd')){

            $inPage->setTitle($_LANG['ARTICLE_REMOVAL']);
            $inPage->addPathway($_LANG['ARTICLE_REMOVAL']);

            $confirm['title']              = $_LANG['ARTICLE_REMOVAL'];
            $confirm['text']               = $_LANG['ARTICLE_REMOVAL_TEXT'].' <a href="/pending/read'.$article['id'].'.html">'.$article['title'].'</a>?';
            $confirm['action']             = $_SERVER['REQUEST_URI'];
            $confirm['yes_button']         = array();
            $confirm['yes_button']['type'] = 'submit';
            $confirm['yes_button']['name'] = 'goadd';

            cmsPage::initTemplate('components', 'action_confirm')->
                assign('confirm', $confirm)->
                display('action_confirm.tpl');

        } else {

            $model->deleteArticle($article['id']);
            cmsCore::addSessionMessage($_LANG['ARTICLE_DELETED'], 'info');
            cmsCore::redirect('/pending');

        }

    }
    
}