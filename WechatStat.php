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
class WechatStat extends BaseWechat
{
    /**
     * 获取统计数据
     * @param string $type  数据分类(user|article|upstreammsg|interface)分别为(用户分析|图文分析|消息分析|接口分析)
     * @param string $subtype   数据子分类，参考 DATACUBE_URL_ARR 常量定义部分 或者README.md说明文档
     * @param string $begin_date 开始时间
     * @param string $end_date   结束时间
     * @return boolean|array 成功返回查询结果数组，其定义请看官方文档
     */
    public function getDatacube($type,$subtype,$begin_date,$end_date=''){
        if (!$this->access_token && !$this->checkAuth()) return false;
        if (!isset(self::$DATACUBE_URL_ARR[$type]) || !isset(self::$DATACUBE_URL_ARR[$type][$subtype]))
            return false;
        $data = array(
            'begin_date'=>$begin_date,
            'end_date'=>$end_date?$end_date:$begin_date
        );
        $result = $this->http_post(self::API_BASE_URL_PREFIX.self::$DATACUBE_URL_ARR[$type][$subtype].'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return isset($json['list'])?$json['list']:$json;
        }
        return false;
    }

}

