<?php

namespace Article\Api\Http\Model;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\CityRepository;
class ArticlePosts extends Model
{

    public $table='article_posts';

    public $timestamps = true;

    function terms_posts($id){
        $where['city_id'] = CityRepository::http_id();
        $where['post_type'] =1;
        return $this->where($where)->whereRaw("FIND_IN_SET($id,term_id)")->orderBy('id','desc')->paginate(5);
    }

    function one_posts($id){
        $where['city_id'] = CityRepository::http_id();
        $where['id'] =$id;
        $this->where($where)->increment('post_hits', 1);
        return $this->where($where)->first();
    }

    function _lists($where=array(),$order=array('id','desc')){
        $where['city_id']= ! empty(auth()->user()->prior_citiy->id)?auth()->user()->prior_citiy->id:0;

        if ($where['city_id'] == 0){
            unset($where['city_id']);
        }

        $users = $this->where($where)->orderBy($order[0],$order[1])->join('cities', 'cities.id', '=', 'article_posts.city_id')
            ->select(['cities.name','article_posts.*'])->paginate(15);
        return $users;
    }

}
