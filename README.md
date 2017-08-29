# 1 .装插件 “weboap/visitor” 及其环境部署#

##Weboap/visitors##

它是能够根据 用户的不同ip 来进行访问量统计。

## 使用composer 安装 ##
    "require": {
        "weboap/visitor": "dev-master"
    }

然后使用php artisan 执行 composer update

##环境部署及其相关配置##
插件就安装完成了，之后再注册一下插件，在config/app.php中：

    'providers' => [

       
        Weboap\Visitor\VisitorServiceProvider::class,


    ],

然后 php artisan 执行 下面两个命令

php artisan vendor:publish

php artisan migrate


到http://dev.maxmind.com/geoip/geoip2/geolite2/下载geoip包（GeoLite2-City.mmdb），
将它解压之后放到 目录storage/geo/， 其中geo目录需要自己创建.

#2. 配置migration #


**上面的步骤之后你将会得到一个表 visitor_registry，**

![](http://i.imgur.com/L2BTfX4.png)

然后我们表添加一个字段 ‘article_id’

接下来使用 Migration 来进行数据表之间的关联 

使用php artisan执行命令

php artisan make:migration add_article_id_to_visitor_registry --table='visitor_registry'.

就会在databases/migration 生成文件  2014_02_09_225721_create_visitor_registry.php


重写里面的up方法

    public function up()
    {
        Schema::table(''visitor_registry'', function (Blueprint $table) {
            //设定字段'article_id'字段类型
            $table->integer('article_id')->unsigned()->index();
            
           //可设定字段'article_id'为外来键，与content表中的content_id关联,联级删除
            //$table->foreign('article_id')->references('content_id')->on('content')->onDelete('cascade');

        });
    }


#数据表之间的关联#

这一步个人认为可以将文章表和 _visitor_registry表关联，也可不关联，看业务需求吧。

在_visitor_registry表的model里面添加方法：

    public function content()
    {
     return $this->belongsTo('App\ContentModel');
    }

对应的，在文章表 Content 也加入方法：

     public function visitors(){
         
         //子表为laravel_Visitor_Registry
        return $this->hasMany('App\VisitorRegistry');
     }



#3. 对 Visitor.php 的修改#

找到 vendor/weboap/visitor/src/Visitor.php 

添加方法： 
    
    public function hasArticle($id,$ip) {
    
        return count(VisitorRegistry::where('article_id','=',$id)->where('ip','=',$ip)->get()) > 0;
    }

再在log()方法 修改if判断条件


      if ($this->has($ip) && $this->hasArticle($article_id,$ip) )

再在下面添加代码

     $visitor = VisitorRegistry::where('ip','=',$ip)->where('article_id','=',$article_id)->first();
            $visitor->update(['clicks'=>$visitor->clicks + 1]);
            
              return true;

再在data里面添加 'article_id' => $article_id, 
    
     $data = [
                'ip'         => $ip,
                'country'    => $country,
                'clicks'     => 1,
                'updated_at' => c::now(),
                'created_at' => c::now(), 
                'article_id' => $article_id,              
            ];

#在控制器的使用#

  参考代码 1.(将访问数目存入Content表里面，这样可以不必关联两张表，个人认为这样能在一张表看到信息方便管理)
    
    use Visitor;


   
    public function dataShow(Request $request){
        
        $id=$request->route('id'); 
        //调用访问统计
        $visitors=Visitor::log($id);
        $info=contentModel::where('content_id',$id)->first();
        $contentId=$info['content_id'];
       
        //将访问数存入content表
        $info->content_visitors=$visitors; $info->save();
    
    }

  参考代码2.直接使用模型方法

     public function dataShow(Request $request){
   
     $id=$request->route('id'); 
     //调用访问统计
     $visitors=Visitor::log($id); 
     //实例化model 并且调用方法visitors()
     $model=new contentModel(); 
     $v=$model->visitors();
 
     //封装返回详情页数据
  
     return view('index/preview')->with('visitors',$v);

    }

最后配合 highCharts 能够很好的展现 近几天的访问量，效果图如下：

![](http://i.imgur.com/gPa2IZn.png)
