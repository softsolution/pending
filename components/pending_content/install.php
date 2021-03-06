<?php
/* ************************************************************************** */
/* created by soft-solution.ru, support@soft-solution.ru                      */
/* component pending content for InstantCMS 1.10.6                            */
/* license: commercialcc                                                      */
/* Незаконное использование преследуется по закону                            */
/* ************************************************************************** */
    function info_component_pending_content(){
        
        $_component['title']        = 'Отложенный контент';
        $_component['description']  = 'Компонент Отложенный контент для InstantCMS';
        $_component['link']         = 'pending_content';
        $_component['author']       = '<a href="http://soft-solution.ru">soft-solution.ru</a>';
        $_component['internal']     = '1';
        $_component['version']      = '1.10.6';
		
        $inCore = cmsCore::getInstance();
        $inCore->loadModel('pending_content');
        
        $_component['config'] = cms_model_pending_content::getDefaultConfig();
        
        return $_component;

    }

    function install_component_pending_content(){
        
        $inCore     = cmsCore::getInstance();
        $inDB       = cmsDatabase::getInstance();
        
        $inDB->importFromFile($_SERVER['DOCUMENT_ROOT'].'/components/pending_content/install.sql');
        
        cmsCore::loadClass('cron');
        
        if(!$inDB->get_field('cms_cron_jobs', "job_name='cronPendingContent'", 'id')){
            cmsCron::registerJob('cronPendingContent', array(
                'interval' => 24,
                'component' => 'pending_content',
                'model_method' => 'cronPendingContent',
                'comment' => 'Публикация отложенных статей',
                'custom_file' => '',
                'enabled' => 1,
                'class_name' => '',
                'class_method' => ''
            ));
        }

        return true;

    }


    function upgrade_component_pending_content(){
        
        return true;
        
    }
    
    function remove_component_pending_content(){
	
        $inCore     = cmsCore::getInstance();
        $inDB       = cmsDatabase::getInstance();
        
        cmsCore::loadClass('cron');
        cmsCron::removeJob('cronPendingContent');
        
        $inDB->query("DROP TABLE IF EXISTS cms_pending_content");
        
        //TODO добавить удаление неопубликованных статей через модель компонента - 
        //для правильного удаления картинок
		
    }
?>