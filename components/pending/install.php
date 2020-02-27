<?php
/* ************************************************************************** */
/* created by soft-solution.ru, support@soft-solution.ru                      */
/* component pending content for InstantCMS 1.10.6                            */
/* license: commercialcc                                                      */
/* Незаконное использование преследуется по закону                            */
/* ************************************************************************** */
    function info_component_pending(){
        
        $_component['title']        = 'Отложенный контент';
        $_component['description']  = 'Компонент Отложенный контент для InstantCMS';
        $_component['link']         = 'pending';
        $_component['author']       = '<a href="http://soft-solution.ru">soft-solution.ru</a>';
        $_component['internal']     = '0';
        $_component['version']      = '1.10.6';
		
        $inCore = cmsCore::getInstance();
        $inCore->loadModel('pending');
        
        $_component['config'] = cms_model_pending::getDefaultConfig();
        
        return $_component;

    }

    function install_component_pending(){
        
        $inCore     = cmsCore::getInstance();
        $inDB       = cmsDatabase::getInstance();
        
        $inDB->importFromFile($_SERVER['DOCUMENT_ROOT'].'/components/pending/install.sql');
        
        cmsCore::loadClass('cron');
        
        if(!$inDB->get_field('cms_cron_jobs', "job_name='cronPendingContent'", 'id')){
            cmsCron::registerJob('cronPendingContent', array(
                'interval' => 0,
                'component' => 'pending',
                'model_method' => 'cronPendingContent',
                'comment' => 'Публикация отложенных статей',
                'custom_file' => '',
                'enabled' => 1,
                'class_name' => '',
                'class_method' => ''
            ));
        }
        
        cmsUser::registerGroupAccessType('pending/access', 'Добавление отложенных статей');

        return true;

    }


    function upgrade_component_pending(){
        
        cmsUser::registerGroupAccessType('pending/access', 'Добавление отложенных статей');
        
        return true;
        
    }
    
    function remove_component_pending(){
	
        $inCore     = cmsCore::getInstance();
        $inDB       = cmsDatabase::getInstance();
        
        cmsCore::loadClass('cron');
        cmsCron::removeJob('cronPendingContent');
        
        cmsUser::deleteGroupAccessType('pending/access');
        
        $inDB->query("DROP TABLE IF EXISTS cms_pending");
        
        //TODO добавить удаление неопубликованных статей через модель компонента - 
        //для правильного удаления картинок
		
    }
?>