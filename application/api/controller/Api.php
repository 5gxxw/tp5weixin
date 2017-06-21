<?php
namespace app\api\controller;
use think\Db;

class Api{
	private $weObj;
	public function index(){
		$options = [
			'token'=>config('token'),
			'encodingaeskey'=>config('encodingaeskey'),
			'appid'=>config('appid'),
			'appsecret'=>config('appsecret')
		];
		$this->weObj = new \com\TpWechat($options);
		//微信服务器验证
		$this->weObj->valid();

		//获取微信服务器发来的信息->获取接收消息的类型
		$type = $this->weObj->getRev()->getRevType();
		switch ($type) {
		    //text类型
			case \com\TpWechat::MSGTYPE_TEXT:
			    //获取接收消息内容正文
				$text = $this->weObj->getRev()->getRevContent();
			    //关键词
				$this->keyword($text);
				exit;
				break;

			default:
				# code...
				break;
		}

		//获取接收事件推送
		$event = $this->weObj->getRev()->getRevEvent();
		switch ($event['event']) {
			// 订阅
			case \com\TpWechat::EVENT_SUBSCRIBE:
				$areplyData = cache('areply');
				if(!$areplyData){
					$areplyData = Db::name('areply')->find();
					cache('areply', $areplyData, 24*3600);
				}
				
				if($areplyData['keyword']){
					$this->keyword($areplyData['keyword']);
				}else{
					$this->weObj->text('感谢您的关注，本程序采用tp5开发，QQ：296720094')->reply();
				}
				exit;
				break;

			case \com\TpWechat::EVENT_MENU_CLICK:
				$this->keyword($event['key']);
				exit;
				break;
			
			default:
				# code...
				break;
		}
	}

	//关键词
	private function keyword($keyword){
		$type = 1;
		$data = Db::name('keyword')->where('keyword',$keyword)->where('type',$type)->find();
		if(!$data){
			$type = 2;
			$data = Db::name('keyword')->where('keyword','like','%'.$keyword.'%')->where('type',$type)->order('id desc')->limit(9)->select();
		}

		if($data){
            if($type==1){
                $module = $data['module'];
            }else{
                $module = 'img';
            }

			switch ($module) {
				case 'text':
					$info = Db::name($module)->find($data['pid']);
					$this->weObj->text($info['text'])->reply();
					exit;
					break;

				case 'img':
					if($type == 1){
						// 精确匹配
						$info = Db::name($module)->find($data['pid']);
						$content[] = [
							'Title'=>$info['title'],
							'Description'=>$info['text'],
							'PicUrl'=>$this->getImgUrl($info['pic']),
							'Url'=>$this->getUrl($info)
						];
						$this->weObj->news($content)->reply();
						exit;
					}else{
						// 模糊匹配
						$ids = [];
						foreach ($data as $key => $value) {
							$ids[] = $value['pid'];
						}
						$info = Db::name('img')->where('id','in',$ids)->order('id desc')->select();
						$content = [];
						foreach ($info as $k => $v) {
							$content[] = [
								'Title'=>$v['title'],
								'Description'=>$v['text'],
								'PicUrl'=>$this->getImgUrl($v['pic']),
								'Url'=>$this->getUrl($v)
							];
						}
						$this->weObj->news($content)->reply();
						exit;
						
					}
					break;			
				default:
					# code...
					break;
			}
		}else{
			// 如果没有匹配到关键词
			$otherData = Db::name('other')->find();
			if(!$otherData){
				$this->weObj->text('感谢您的关注，技术QQ：296720094')->reply();
			}else{
				$data = Db::name('keyword')->where('keyword',$otherData['keyword'])->find();
				if($data){
					$this->keyword($otherData['keyword']);
				}else{
					$this->weObj->text('感谢您的关注，技术QQ：296720094')->reply();
				}
			}
		}

	}

	/**
	 * 图文链接转换
	 * @author ning
	 * @DateTime 2016-06-11T22:59:17+0800
	 * @param    [type]                   $info [description]
	 * @return   [type]                         [description]
	 */
    private function getUrl($info){
        if($info['url']){
            $url = str_replace(['{token}','{wecha_id}'], [config('token'), $this->weObj->getRev()->getRevFrom()], $info['url']);
        }else{
            //如果没有写外链，跳转到微官网详情页
            $url = config('site_url').URL('/index/img/index?id='.$info['id']);
        }
        
        return $url;
    }

/**
 * 获取缩略图的地址
 * @param  [type] $v [description]
 * @return [type]    [description]
 */
    private function getImgUrl($v){
        //如果是外部的网址
        if(strpos($v, 'http') === false){
            return config('img_server').$v;            
        }else{
            return $v;
        }
    }	

}