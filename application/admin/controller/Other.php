<?php
namespace app\admin\controller;

use app\admin\model\Other as OtherModel;
use think\Db;

/**
* 默认回复
*/
class Other extends Base
{
	/**
	 * 默认回复设置
	 * @author ning
	 * @DateTime 2016-09-25T15:14:13+0800
	 * @return   [type]                   [description]
	 */
	public function index()
	{
		$data = Db::table('other')->find();
		$this->assign('data', $data);
		return view('index');
	}

	/**
	 * 设置默认回复
	 * @author ning
	 * @DateTime 2016-07-30T10:43:25+0800
	 */
	public function set(){
		$data = Db::table('other')->find();
		if(request()->isPost() && input('post.')){
			$keyword = input('?post.keyword') ? input('post.keyword') : '';

			$otherModel = new OtherModel;

			if($data){
				if($otherModel->save(['keyword'=>$keyword],['id'=>$data['id']])){
					return $this->success('设置成功');
				}else{
					return $this->error('设置失败');
				}
			}else{
				if($otherModel->save(['keyword'=>$keyword])){
					return $this->success('设置成功');
				}else{
					return $this->error('设置失败');
				}
			}
		}
	}	
}