<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContentModel extends Model
{
    //指定表名
    protected $table='content';

    //指定主键
    protected $primaryKey='content_id';

    //设置允许批量赋值的字段
    //protected  $fillable=[Admin_name'];
    
    //指定不允许批量赋值的字段
     //protected $guarded=[];

    //是否时间自动维护
     public $timestamps=true;

     public function visitors(){
         
         //子表为laravel_Visitor_Registry
        return $this->hasMany('App\VisitorRegistry');
     }



}
