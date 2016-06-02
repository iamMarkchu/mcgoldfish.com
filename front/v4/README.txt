安装前需要提供的信息：
1．	Tracking 数据库配置，host, port, user, password, dbname
2．	用于存放促销展示日志的目录 （会同步到stats 服务）
3．	获取促销信息的方法，和文件
get_promo($id)
返回:　array('merchant_id'=>, 'aff_url'=>, 'dest_url'=>); 
4．	获取商家信息的方法，和文件
get_store($id)
返回：array('deep_url_template'=>联盟出站模板, 'dest_url'=>商家出站链接, 'affiliate_default_url'=>商家出站联盟链接, 'url'=>域名)
5．	获取社区登录用户ID的方法，和文件。


Tracking 需要安装如下位置
1．	所有用户可以访问到的页面，记录incoming, pagevisit
Include ‘tracking/incoming.php’

2．	用户搜索行为记录：
set_searchs(关键词, 耗时, 搜索类型, 当前页数, 总结果数, 是否缓存0:1, 是否用suggest 0:1) 

3．	促销展示记录
a.	单条：set_impressions($coupnid=0, $merid=0, $page_type='', $page_value='', $pvid=0, $block_rank='')
b.	批量：set_batch_impression(array(0 => array($couponid, $merid, $page_type, $page_value, $pvid, $block_rank))); 

