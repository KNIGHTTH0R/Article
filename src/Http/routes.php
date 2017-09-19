<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/11
 * Time: 14:49
 */

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', ['namespace' => 'Article\Api\Http\Controllers'], function ($api) {
    // 无需登录即可操作
    $api->get('article/list','ArticleController@article_list');
    $api->post('article/delete','ArticleController@delete');
    $api->post('article/edit','ArticleController@edit');
    $api->post('article/edit_post','ArticleController@edit_post');
    $api->post('article/add','ArticleController@add');
    $api->post('article/drafts','ArticleController@drafts');

});
