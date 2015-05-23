<?php
/**
 * @link http://www.lubanr.com/
 * @copyright Copyright (c) 2015 Baochen Tech. Co. 
 * @license http://www.lubanr.com/license/
 */

namespace lubaogui\wechat;

/**
 * 微信接口 
 *
 * @author Baogui Lu (lbaogui@lubanr.com)
 * @version since 2.0
 */
class WechatSemantic extends BaseWechat
{
    /**
     * 语义理解接口
     * @param String $uid      用户唯一id（非开发者id），用户区分公众号下的不同用户（建议填入用户openid）
     * @param String $query    输入文本串
     * @param String $category 需要使用的服务类型，多个用“，”隔开，不能为空
     * @param Float $latitude  纬度坐标，与经度同时传入；与城市二选一传入
     * @param Float $longitude 经度坐标，与纬度同时传入；与城市二选一传入
     * @param String $city     城市名称，与经纬度二选一传入
     * @param String $region   区域名称，在城市存在的情况下可省略；与经纬度二选一传入
     * @return boolean|array
     */
    public function querySemantic($uid,$query,$category,$latitude=0,$longitude=0,$city="",$region=""){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $data=array(
                'query' => $query,
                'category' => $category,
                'appid' => $this->appid,
                'uid' => ''
        );
        //地理坐标或城市名称二选一
        if ($latitude) {
            $data['latitude'] = $latitude;
            $data['longitude'] = $longitude;
        } elseif ($city) {
            $data['city'] = $city;
        } elseif ($region) {
            $data['region'] = $region;
        }
        $result = $this->http_post(self::API_BASE_URL_PREFIX.self::SEMANTIC_API_URL.'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }


}

