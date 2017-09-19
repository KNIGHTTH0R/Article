<?php

namespace Article\Api\Http\Controllers;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/13
 * Time: 14:44
 */

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Article\Api\Http\Model\ArticlePosts;
use DB;

class ArticleController extends Controller
{

    protected $posts;
    public function __construct(ArticlePosts $posts)
    {
        $this->posts = $posts;
    }

    /**
     *文章列表（分页）
     *
     * @Get("article/list")
     *
     * @Response(200, body={
     *     "status": "ok|error",
     *     "message": "...",
     *     "data": "{
     *       {
     *      "id": 文章id,
     *      "term_id": 分类id,
     *       "post_author": 发表者名称,
     *      "post_keywords": "关键字",
     *      "post_content": post内容,
     *      "post_title": post标题,
     **     "post_excerpt": post摘要,
     *      "visible_status": 登录可见，1允许，0不允许,
     *      "post_type": "post类型，1页面,2博客",
     *      "attachment_address": "附件地址",
     *      "image_address": "图片地址",
     *      "post_hits": "post点击数，查看数",
     *      "city_id": "地市ID",
     *      },
     *     {
     *      "id": 文章id,
     *      "term_id": 分类id,
     *       "post_author": 发表者名称,
     *      "post_keywords": "关键字",
     *      "post_content": post内容,
     *      "post_title": post标题,
     **     "post_excerpt": post摘要,
     *      "visible_status": 登录可见，1允许，0不允许,
     *      "post_type": "post类型，1页面,2博客",
     *      "attachment_address": "附件地址",
     *      "image_address": "图片地址",
     *      "post_hits": "post点击数，查看数",
     *      "city_id": "地市ID",
     *      }
     *     }",
     *     "errors": null,
     *     "code": 0
     * })
     */
    public function article_list(Request $request){

        $type = $request->input('post_type');
        $where=array(
            ['post_type','=',1]
        );

        $order=array('id','desc');
        if ( $time = $request->input('time')){
            $order=array('article_posts.created_at','desc');
        }

        if ( $created_at = $request->input('created_at')){
            $where[] =   ['article_posts.created_at', '>', "$created_at"];
        }

        if ( $created_by = $request->input('created_by')){
            $where[] =   ['article_posts.created_at', '<', "$created_by"];
        }

        if (!empty($type)){
            $where['post_type'] = 3;
        }
        return $this->ajax('ok','success', $this->posts->_lists($where,$order));
    }

    /**
     *文章添加
     *
     * @Get("article/add")
     *
     * @Response(200, body={
     *     "status": "ok|error",
     *     "message": "...",
     *     "data": "{
     *       {
     *          1:添加成功
     *          0:添加失败
     *          3:数据库字段不对
     *          4:没有填写分类
     *      }
     *     }",
     *     "errors": null,
     *     "code": 0
     * })
     */
    public function add(Request $request){
        $input = json_decode($request->getContent(),1);

        if (empty($input['term_id'])&&!is_array($input['term_id'])){
            return $this->ajax('no','error', "4");
        }

        $input['term_id'] = implode(',',$input['term_id']);
        try {
            $input['post_content']=htmlspecialchars_decode($input['post_content']);
            $input['city_id'] = ! empty(auth()->user()->prior_citiy->id)?auth()->user()->prior_citiy->id:$input['city_id'];
            $input['created_at'] = date('y-m-d H:i:s',time());
            if ($this->posts->insert($input)){
                return $this->ajax('ok','success', "1");
            }else{
                return $this->ajax('no','error', "0");
            }
        } catch(\Illuminate\Database\QueryException $ex) {
            return $this->ajax('no','error', "3");
        }
    }

    /**
     *文章编辑
     *
     * @Post("api/article/edit")
     * @Request({
     *     "id": "id"
     * })
     * @Response(200, body={
     *     "status": "ok|no",
     *     "message": "...",
     *     "data": "{
     *      "id": 文章id,
     *      "term_id": 分类id,
     *       "post_author": 发表者名称,
     *      "post_keywords": "关键字",
     *      "post_content": post内容,
     *      "post_title": post标题,
     **     "post_excerpt": post摘要,
     *      "visible_status": 登录可见，1允许，0不允许,
     *      "post_type": "post类型，1页面,2博客",
     *      "attachment_address": "附件地址",
     *      "image_address": "图片地址",
     *      "post_hits": "post点击数，查看数",
     *      "city_id": "地市ID",
     *     |
     *
     *      0:
     *
     *     }",
     *     "errors": null,
     *     "code": 0
     * })
     */
    public function edit(Request $request){
        $id = $request->input('id');
        $data = $this->posts->where('id',$id)->first();
        if ($data){
            return $this->ajax('ok','success', $data);
        }else{
            return $this->ajax('no','error', "0");
        }
    }

    /**
     *文章编辑提交
     *
     * @Post("api/article/edit_post")
     * @Request({
     *     "id": "id"
     * })
     * @Response(200, body={
     *     "status": "ok|no",
     *     "message": "...",
     *     "data": "{
     *      "term_id": 分类id,
     *       "post_author": 发表者名称,
     *      "post_keywords": "关键字",
     *      "post_content": post内容,
     *      "post_title": post标题,
     **     "post_excerpt": post摘要,
     *      "visible_status": 登录可见，1允许，0不允许,
     *      "attachment_address": "附件地址",
     *      "image_address": "图片地址",
     *     |
     *
     *      0:
     *
     *     }",
     *     "errors": null,
     *     "code": 0
     * })
     */
    public function edit_post(Request $request){
        $input = json_decode($request->getContent(),1);
        $id = $input['id'];

        if (empty($input['term_id'])&&!is_array($input['term_id'])){
            return $this->ajax('no','error', "4");
        }
        $input['term_id'] = implode(',',$input['term_id']);
        unset($input['id']);
        try {
            $input['post_content']=htmlspecialchars_decode($input['post_content']);
            $input['city_id'] = ! empty(auth()->user()->prior_citiy->id)?:$input['city_id'];
            if ($this->posts ->where('id',$id)->update($input)){
                return $this->ajax('ok','success', "1");
            }else{
                return $this->ajax('no','error', "0");
            }
        } catch(\Illuminate\Database\QueryException $ex) {
            return $this->ajax('no','error', "3");
        }
    }

    /**
     *文章删除
     *
     * @Post("api/article/add_post")
     * @Request({
     *     "id": "id"
     * })
     * @Response(200, body={
     *     "status": "ok|error",
     *     "message": "...",
     *     "data": "{
     *      1:删除成功
     *      0:删除失败
     *
     *     }",
     *     "errors": null,
     *     "code": 0
     * })
     *
     */
    public function delete(Request $request){
        $id = $request->input('id');
        if($this->posts->where('id',$id)->delete()){
            return $this->ajax('ok','success', "1");
        }else{
            return $this->ajax('no','error', "0");
        }
    }

    /**
     *文章草稿箱
     *
     * @Post("api/article/add_post")
     * @Request({
     *     "id": "id"
     *     "type": "1|3"
     * })
     * @Response(200, body={
     *     "status": "ok|error",
     *     "message": "...",
     *     "data": "{
     *      1:修改成功
     *      0:修改失败
     *
     *     }",
     *     "errors": null,
     *     "code": 0
     * })
     *
     */
    public function drafts(Request $request){
        $type = $request->input('post_type');
        if ($type==1||$type==3){
            $id = $request->input('id');
            if($this->posts->where('id',$id)->update(['post_type'=>$type])){
                return $this->ajax('ok','success', "1");
            }
        }

        return $this->ajax('no','error', "0");
    }

}