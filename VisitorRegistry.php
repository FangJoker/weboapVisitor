<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VisitorRegistry extends Model
{
      //指定表名
    protected $table='visitor_registry';

    //指定主键
    protected $primaryKey='id';

    //设置允许批量赋值的字段
    protected  $fillable=['clicks'];
    
    //指定不允许批量赋值的字段
     //protected $guarded=[];

    //是否时间自动维护
     public $timestamps=true;

     public function content(){
        //父表为laravel_content
        return $this->belongsTo('App\ContentModel');
    }
}
