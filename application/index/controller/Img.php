<?php
namespace app\index\controller;

use think\Db;

/**
* 图文回复
*/
class Img extends Base
{	
	/**
	 * 图文回复页面
	 * @author 296720094@qq.com
	 * @DateTime 2016-09-25T15:46:24+0800
	 * @return   [type]                   [description]
	 */
	public function index()
	{
		$id = input('param.id');
		$data = cache('img_' . $id);
		if(!$data){
			$data = Db::table('img')->where('id',$id)->find();
			cache('img_' . $id, $data, 24*3600);
		}
		$click = cache('img_click_' . $id);
		if(!$click){
			$click = $data['click'];
		}
		$click ++;
		if($click % 10 == 0){
			Db::table('img')->where('id', $id)->update(['click'=>$click]);
		}
		cache('img_click_' . $id, $click);

		$this->assign('click', $click);
		$this->assign('data', $data);
		return view('index');
	}
}