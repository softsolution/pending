<?php
/* ************************************************************************** */
/* created by soft-solution.ru, support@soft-solution.ru                      */
/* component pending content for InstantCMS 1.10.6                            */
/* license: commercialcc                                                      */
/* Незаконное использование преследуется по закону                            */
/* ************************************************************************** */

if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

class cms_model_pending_content{
    
    public $config = array();
    
    public function __call($name, $arguments){
        exit( "вызван несуществующий метод \"".$name."\"" );
    }

    public function __construct(){
        $this->inDB   = cmsDatabase::getInstance();
        $this->config = self::getConfig();
        cmsCore::loadLanguage('components/content');
    }

/* ========================================================================== */
/* ========================================================================== */

    public static function getDefaultConfig() {

        $cfg = array (
            'param1' => 0
        );

        return $cfg;

    }
    
/* ========================================================================== */
/* ========================================================================== */
    
    public static function getConfig() {
        $inCore = cmsCore::getInstance();
        $cfg_content = $inCore->loadComponentConfig("content");
        $cfg_pending = $inCore->loadComponentConfig("pending_content");
        $cfg = array_merge($cfg_content, $cfg_pending);
        return $cfg;
    }
    
/* ========================================================================== */
/* ========================================================================== */
    
    /**
     * Возвращает дерево категорий
     * @return array
     */
    
    public function getCatsTree() {

        $sql = "SELECT  cat.id as id,
                        cat.title as title,
                        cat.NSLeft as NSLeft,
                        cat.NSRight as NSRight,
                        cat.NSLevel as NSLevel,
                        cat.seolink as seolink
                FROM cms_category cat
                WHERE cat.NSLevel>0
                ORDER BY cat.NSLeft";

        $result = $this->inDB->query($sql);

        if (!$this->inDB->num_rows($result)) { return false; }

        while($subcat = $this->inDB->fetch_assoc($result)){

            $subcats[] = $subcat;

        }
        return $subcats;

    }

/* ========================================================================== */
/* ========================================================================== */
    
    /**
     * Условия выборки
     */
    
    public function whereCatIs($category_id) {
        $this->inDB->where("con.category_id = '{$category_id}'");
    }

    public function whereUserIs($user_id) {
        $this->inDB->where("con.user_id = '{$user_id}'");
    }
    public function whereThisAndNestedCats($left_key, $right_key) {
        $this->inDB->where("cat.NSLeft >= '$left_key' AND cat.NSRight <= '$right_key' AND cat.parent_id > 0");
    }

/* ========================================================================== */
/* ========================================================================== */
    
    /**
     * Получаем статьи по заданным параметрам
     * @return array
     */
    
    public function getArticlesList($only_published=true) {

        $today = date("Y-m-d H:i:s");

        if ($only_published){
            $this->inDB->where("con.published = 1 AND con.pubdate <= '$today' AND (con.is_end=0 OR (con.is_end=1 AND con.enddate >= '$today'))");
        }

        $sql = "SELECT con.*,
                       con.pubdate as fpubdate,
                       cat.title as cat_title, cat.seolink as catseolink,
                       cat.showdesc,
                       u.nickname as author,
                       u.login as user_login
                FROM cms_pending_content con
                INNER JOIN cms_category cat ON cat.id = con.category_id
                LEFT JOIN cms_users u ON u.id = con.user_id
                WHERE 1=1 
                {$this->inDB->where}

                {$this->inDB->group_by}

                {$this->inDB->order_by}\n";

        if ($this->inDB->limit){
            $sql .= "LIMIT {$this->inDB->limit}";
        }

        $result = $this->inDB->query($sql);

        $this->inDB->resetConditions();

        if (!$this->inDB->num_rows($result)) { return false; }

        while($article = $this->inDB->fetch_assoc($result)){
            $article['fpubdate'] = cmsCore::dateFormat($article['fpubdate']);
            $articles[] = $article;
        }
        
        return $articles;

    }

/* ========================================================================== */
/* ========================================================================== */
    
    /**
     * Возвращает количество статей по заданным параметрам
     * @return int
     */
    
    public function getArticlesCount($only_published=true) {

        $today = date("Y-m-d H:i:s");

        if ($only_published){
            $this->inDB->where("con.published = 1 AND con.pubdate <= '$today'
                      AND (con.is_end=0 OR (con.is_end=1 AND con.enddate >= '$today'))");
        }

        $sql = "SELECT 1
                FROM cms_pending_content con
                INNER JOIN cms_category cat ON cat.id = con.category_id
                WHERE 1=1 
                {$this->inDB->where}
                {$this->inDB->group_by} ";

        $result = $this->inDB->query($sql);

        return $this->inDB->num_rows($result);

    }

/* ========================================================================== */
/* ========================================================================== */
    
    /**
     * Получает статью
     * @return array
     */
    
    public function getArticle($id) {
        
        $sql = "SELECT  con.*,
                            cat.title cat_title, cat.id cat_id, cat.NSLeft as leftkey, cat.NSRight as rightkey, cat.modgrp_id,
                            cat.showtags as showtags, cat.seolink as catseolink, cat.cost, u.nickname as author, u.login as user_login
                        FROM cms_content con
                        INNER JOIN cms_category cat ON cat.id = con.category_id
                        LEFT JOIN cms_users u ON u.id = con.user_id
                        WHERE con.id = '$id' LIMIT 1";

        $result = $this->inDB->query($sql);

        if (!$this->inDB->num_rows($result)) { return false; }

        $article = $this->inDB->fetch_assoc($result);
        $article['access'] = cmsCore::yamlToArray($article['access']);

        return $article;

    }

/* ========================================================================== */
/* ========================================================================== */
    
    /**
     * Удаляет статью
     * @return bool
     */
    
    public function deleteArticle($id){

        $this->inDB->delete('cms_pending_content', "id='$id'", 1);
        @unlink(PATH.'/images/photos/small/pending_article'.$id.'.jpg');
        @unlink(PATH.'/images/photos/medium/pending_article'.$id.'.jpg');

        return true;

    }
    
/* ========================================================================== */
/* ========================================================================== */
    
    /**
     * Удаляет список статей
     * @param array $id_list
     * @return bool
     */
    
    public function deleteArticles($id_list){
        foreach($id_list as $id){
            $this->deleteArticle($id);
        }
        return true;
    }

/* ========================================================================== */
/* ========================================================================== */
    
    /**
     * Добавляет отложенную статью
     * @param array $article
     * @return int
     */
    
    public function addArticle($article){

        if ($article['url']) { $article['url'] = cmsCore::strToURL($article['url'], $this->config['is_url_cyrillic']); }
        
        $article['id'] = $this->inDB->insert('cms_pending_content', $article);
        return $article['id'] ? $article['id'] : false;
    }

/* ========================================================================== */
/* ========================================================================== */
    
    /**
     * Обновляет статью
     * @return bool
     */
    
    public function updateArticle($id, $article, $not_upd_seo = false){

        $article['id']= $id;

        if(!$not_upd_seo){

            if (@$article['url']){
                $article['url'] = cmsCore::strToURL($article['url'], $this->config['is_url_cyrillic']);
            }

        } else { unset($article['url']); }

        if (!$article['user_id']) { $article['user_id'] = cmsUser::getInstance()->id; }

        $this->inDB->update('cms_pending_content', $article, $id);
        return true;

    }

/* ========================================================================== */
/* ========================================================================== */
    
    public function cronPendingContent(){
        
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
        
        $inCore->loadModel('content');
        $model_content = new cms_model_content;
        
        $date_content = date("Y-m-d H:i");
        $sql =  "SELECT * FROM cms_pending_content WHERE published = 1 AND pubdate <= '".$date_content."'";

        $result = $inDB->query($sql);
        
        if ($inDB->num_rows($result)) {
            while ($article = $inDB->fetch_assoc($result)) {
                
                $pending_id = $article['id'];
                unset($article['id']);

                $article['description']  = $inDB->escape_string($article['description']);
                $article['content']      = $inDB->escape_string($article['content']);
                $article['pubdate']      = $article['pubdate'] ? $article['pubdate'] : date('Y-m-d H:i');
		$article['meta_desc']    = $inDB->escape_string($article['meta_desc']);
		$article['meta_keys']    = $inDB->escape_string($article['meta_keys']);
                $article['title']        = $inDB->escape_string($article['title']);
                $article['access']       = cmsCore::yamlToArray($article['access']);
                
                $article_id              = $model_content->addArticle($article);
                
                if (!$article['is_public']){
                    cmsCore::setAccess($article_id, $article['access'], 'material');
                } else {
                    cmsCore::clearAccess($article_id, 'material');
                }

		if ($article['createmenu']){
                    $this->createMenuItem($article['createmenu'], $article_id, $article['title']);
		}
                
                if ($article_id) {
                    $image_new = PATH . '/images/photos/small/article' . $article_id . '.jpg';
                    $image_old = PATH . '/images/photos/small/pending_article' . $pending_id . '.jpg';
                    if (file_exists($image_old) && !file_exists($image_new)) { rename($image_old, $image_new); }
                    
                    $image_new = PATH . '/images/photos/medium/article' . $article_id . '.jpg';
                    $image_old = PATH . '/images/photos/medium/pending_article' . $pending_id . '.jpg';
                    if (file_exists($image_old) && !file_exists($image_new)) { rename($image_old, $image_new); }

                    $inDB->delete('cms_pending_content', "id=".$pending_id, 1);
                }
            }
        }

        return true;
    }
    
    public function createMenuItem($menu, $id, $title){
        
        $inCore = cmsCore::getInstance();
	$inDB 	= cmsDatabase::getInstance();
	$rootid = $inDB->get_field('cms_menu', 'parent_id=0', 'id');

	$ns     = $inCore->nestedSetsInit('cms_menu');
	$myid   = $ns->AddNode($rootid);

        $link   = $inCore->getMenuLink('content', $id);

        $sql = "UPDATE cms_menu 
                SET menu='$menu',
                        title='$title',
                        link='$link',
                        linktype='content',
                        linkid='$id',
                        target='_self',
                        published='1',
                        template='0',
                        access_list='',
                        iconurl=''
                WHERE id = '$myid'";

        $inDB->query($sql);
        return true;
    }
}

?>