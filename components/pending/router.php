<?php
/* ************************************************************************** */
/* created by soft-solution.ru, support@soft-solution.ru                      */
/* component pending content for InstantCMS 1.10.6                            */
/* license: commercialcc                                                      */
/* Незаконное использование преследуется по закону                            */
/* ************************************************************************** */

    function routes_pending(){
        
        $routes[] = array(
                            '_uri'  => '/^pending\/add([0-9]+).html$/i',
                            'do'    => 'addarticle',
                            1       => 'category_id'
                         );

        $routes[] = array(
                            '_uri'  => '/^pending\/add.html$/i',
                            'do'    => 'addarticle'
                         );

        $routes[] = array(
                            '_uri'  => '/^pending\/edit([0-9]+).html$/i',
                            'do'    => 'editarticle',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^pending\/delete([0-9]+).html$/i',
                            'do'    => 'deletearticle',
                            1       => 'id'
                         );

        $routes[] = array(
                            '_uri'  => '/^pending\/read([0-9]+).html$/i',
                            'do'    => 'read',
                            1       => 'id'
                         );
        
        $routes[] = array(
                            '_uri'  => '/^pending\/drafts\/page\-([0-9]+)$/i',
                            'do'    => 'view',
                            'only_draft' => 1,
                            1       => 'page'
                         );
        
        $routes[] = array(
                            '_uri'  => '/^pending\/drafts$/i',
                            'do'    => 'view',
                            'only_draft' => 1
                         );

        $routes[] = array(
                            '_uri'  => '/^pending\/cat([0-9]+)\/page\-([0-9]+)$/i',
                            'do'    => 'view',
                            1       => 'category_id',
                            2       => 'page'
                         );
        
        $routes[] = array(
                            '_uri'  => '/^pending\/cat([0-9]+)$/i',
                            'do'    => 'view',
                            1       => 'category_id'
                         );
        
        $routes[] = array(
                            '_uri'  => '/^pending\/page\-([0-9]+)$/i',
                            'do'    => 'view',
                            1       => 'page'
                         );

        $routes[] = array(
                            '_uri'  => '/^pending$/i',
                            'do'    => 'view'
                         );

        return $routes;

    }

?>