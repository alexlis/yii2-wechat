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
class WechatCard extends Wechat
{

    /**
     * 获取卡券事件推送 - 卡卷审核是否通过
     * 当Event为 card_pass_check(审核通过) 或 card_not_pass_check(未通过)
     * @return string|boolean  返回卡券ID
     */
    public function getRevCardPass(){
        if (isset($this->_receive['CardId']))
            return $this->_receive['CardId'];
        else
            return false;
    }
    /**
     * 获取卡券事件推送 - 领取卡券
     * 当Event为 user_get_card(用户领取卡券)
     * @return array|boolean
     */
    public function getRevCardGet(){
        if (isset($this->_receive['CardId']))     //卡券 ID
            $array['CardId'] = $this->_receive['CardId'];
        if (isset($this->_receive['IsGiveByFriend']))    //是否为转赠，1 代表是，0 代表否。
            $array['IsGiveByFriend'] = $this->_receive['IsGiveByFriend'];
        if (isset($this->_receive['UserCardCode']) && !empty($this->_receive['UserCardCode'])) //code 序列号。自定义 code 及非自定义 code的卡券被领取后都支持事件推送。
            $array['UserCardCode'] = $this->_receive['UserCardCode'];
        if (isset($array) && count($array) > 0) {
            return $array;
        } else {
            return false;
        }
    }
    /**
     * 获取卡券事件推送 - 删除卡券
     * 当Event为 user_del_card(用户删除卡券)
     * @return array|boolean
     */
    public function getRevCardDel(){
        if (isset($this->_receive['CardId']))     //卡券 ID
            $array['CardId'] = $this->_receive['CardId'];
        if (isset($this->_receive['UserCardCode']) && !empty($this->_receive['UserCardCode'])) //code 序列号。自定义 code 及非自定义 code的卡券被领取后都支持事件推送。
            $array['UserCardCode'] = $this->_receive['UserCardCode'];
        if (isset($array) && count($array) > 0) {
            return $array;
        } else {
            return false;
        }
    }


    /**
     * 创建卡券
     * @param Array $data      卡券数据
     * @return array|boolean 返回数组中card_id为卡券ID
     */
    public function createCard($data) {
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::CARD_CREATE . 'access_token=' . $this->access_token, self::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }
    /**
     * 更改卡券信息
     * 调用该接口更新信息后会重新送审，卡券状态变更为待审核。已被用户领取的卡券会实时更新票面信息。
     * @param string $data
     * @return boolean
     */
    public function updateCard($data) {
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::CARD_UPDATE . 'access_token=' . $this->access_token, self::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return true;
        }
        return false;
    }
    /**
     * 删除卡券
     * 允许商户删除任意一类卡券。删除卡券后，该卡券对应已生成的领取用二维码、添加到卡包 JS API 均会失效。
     * 注意：删除卡券不能删除已被用户领取，保存在微信客户端中的卡券，已领取的卡券依旧有效。
     * @param string $card_id 卡券ID
     * @return boolean
     */
    public function delCard($card_id) {
        $data = array(
            'card_id' => $card_id,
        );
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::CARD_DELETE . 'access_token=' . $this->access_token, self::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return true;
        }
        return false;
    }
    /**
     * 查询卡券详情
     * @param string $card_id
     * @return boolean|array    返回数组信息比较复杂，请参看卡券接口文档
     */
    public function getCardInfo($card_id) {
        $data = array(
            'card_id' => $card_id,
        );
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::CARD_GET . 'access_token=' . $this->access_token, self::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }
    /**
     * 获取颜色列表
     * 获得卡券的最新颜色列表，用于创建卡券
     * @return boolean|array   返回数组请参看 微信卡券接口文档 的json格式
     */
    public function getCardColors() {
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_get(self::API_BASE_URL_PREFIX . self::CARD_GETCOLORS . 'access_token=' . $this->access_token);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }
    /**
     * 拉取门店列表
     * 获取在公众平台上申请创建的门店列表
     * @param int $offset  开始拉取的偏移，默认为0从头开始
     * @param int $count   拉取的数量，默认为0拉取全部
     * @return boolean|array   返回数组请参看 微信卡券接口文档 的json格式
     */
    public function getCardLocations($offset=0,$count=0) {
        $data=array(
            'offset'=>$offset,
            'count'=>$count
        );
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::CARD_LOCATION_BATCHGET . 'access_token=' . $this->access_token, self::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }
    /**
     * 批量导入门店信息
     * @tutorial 返回插入的门店id列表，以逗号分隔。如果有插入失败的，则为-1，请自行核查是哪个插入失败
     * @param array $data    数组形式的json数据，由于内容较多，具体内容格式请查看 微信卡券接口文档
     * @return boolean|string 成功返回插入的门店id列表
     */
    public function addCardLocations($data) {
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::CARD_LOCATION_BATCHADD . 'access_token=' . $this->access_token, self::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }
    /**
     * 生成卡券二维码
     * 成功则直接返回ticket值，可以用 getQRUrl($ticket) 换取二维码url
     *
     * @param string $cardid 卡券ID 必须
     * @param string $code 指定卡券 code 码，只能被领一次。use_custom_code 字段为 true 的卡券必须填写，非自定义 code 不必填写。
     * @param string $openid 指定领取者的 openid，只有该用户能领取。bind_openid 字段为 true 的卡券必须填写，非自定义 openid 不必填写。
     * @param int $expire_seconds 指定二维码的有效时间，范围是 60 ~ 1800 秒。不填默认为永久有效。
     * @param boolean $is_unique_code 指定下发二维码，生成的二维码随机分配一个 code，领取后不可再次扫描。填写 true 或 false。默认 false。
     * @param string $balance 红包余额，以分为单位。红包类型必填（LUCKY_MONEY），其他卡券类型不填。
     * @return boolean|string
     */
    public function createCardQrcode($card_id,$code='',$openid='',$expire_seconds=0,$is_unique_code=false,$balance='') {
        $card = array(
                'card_id' => $card_id
        );
        if ($code)
            $card['code'] = $code;
        if ($openid)
            $card['openid'] = $openid;
        if ($expire_seconds)
            $card['expire_seconds'] = $expire_seconds;
        if ($is_unique_code)
            $card['is_unique_code'] = $is_unique_code;
        if ($balance)
            $card['balance'] = $balance;
        $data = array(
            'action_name' => "QR_CARD",
            'action_info' => array('card' => $card)
        );
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::CARD_QRCODE_CREATE . 'access_token=' . $this->access_token, self::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }
    /**
     * 消耗 code
     * 自定义 code（use_custom_code 为 true）的优惠券，在 code 被核销时，必须调用此接口。
     *
     * @param string $code 要消耗的序列号
     * @param string $card_id 要消耗序列号所述的 card_id，创建卡券时use_custom_code 填写 true 时必填。
     * @return boolean|array
     * {
     *  "errcode":0,
     *  "errmsg":"ok",
     *  "card":{"card_id":"pFS7Fjg8kV1IdDz01r4SQwMkuCKc"},
     *  "openid":"oFS7Fjl0WsZ9AMZqrI80nbIq8xrA"
     * }
     */
    public function consumeCardCode($code,$card_id='') {
        $data = array('code' => $code);
        if ($card_id)
            $data['card_id'] = $card_id;
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::CARD_CODE_CONSUME . 'access_token=' . $this->access_token, self::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }
    /**
     * code 解码
     * @param string $encrypt_code 通过 choose_card_info 获取的加密字符串
     * @return boolean|array
     * {
     *  "errcode":0,
     *  "errmsg":"ok",
     *  "code":"751234212312"
     *  }
     */
    public function decryptCardCode($encrypt_code) {
        $data = array(
            'encrypt_code' => $encrypt_code,
        );
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::CARD_CODE_DECRYPT . 'access_token=' . $this->access_token, self::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }
    /**
     * 查询 code 的有效性（非自定义 code）
     * @param string $code
     * @return boolean|array
     * {
     *  "errcode":0,
     *  "errmsg":"ok",
     *  "openid":"oFS7Fjl0WsZ9AMZqrI80nbIq8xrA",    //用户 openid
     *  "card":{
     *      "card_id":"pFS7Fjg8kV1IdDz01r4SQwMkuCKc",
     *      "begin_time": 1404205036,               //起始使用时间
     *      "end_time": 1404205036,                 //结束时间
     *  }
     * }
     */
    public function checkCardCode($code) {
        $data = array(
            'code' => $code,
        );
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::CARD_CODE_GET . 'access_token=' . $this->access_token, self::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }
    /**
     * 批量查询卡列表
     * @param $offset  开始拉取的偏移，默认为0从头开始
     * @param $count   需要查询的卡片的数量（数量最大50,默认50）
     * @return boolean|array
     * {
     *  "errcode":0,
     *  "errmsg":"ok",
     *  "card_id_list":["ph_gmt7cUVrlRk8swPwx7aDyF-pg"],    //卡 id 列表
     *  "total_num":1                                       //该商户名下 card_id 总数
     * }
     */
    public function getCardIdList($offset=0,$count=50) {
        if ($count>50)
            $count = 50;
        $data = array(
            'offset' => $offset,
            'count'  => $count,
        );
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::CARD_BATCHGET . 'access_token=' . $this->access_token, self::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }
    /**
     * 更改 code
     * 为确保转赠后的安全性，微信允许自定义code的商户对已下发的code进行更改。
     * 注：为避免用户疑惑，建议仅在发生转赠行为后（发生转赠后，微信会通过事件推送的方式告知商户被转赠的卡券code）对用户的code进行更改。
     * @param string $code      卡券的 code 编码
     * @param string $card_id   卡券 ID
     * @param string $new_code  新的卡券 code 编码
     * @return boolean
     */
    public function updateCardCode($code,$card_id,$new_code) {
        $data = array(
            'code' => $code,
            'card_id' => $card_id,
            'new_code' => $new_code,
        );
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::CARD_CODE_UPDATE . 'access_token=' . $this->access_token, self::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return true;
        }
        return false;
    }
    /**
     * 设置卡券失效
     * 设置卡券失效的操作不可逆
     * @param string $code 需要设置为失效的 code
     * @param string $card_id 自定义 code 的卡券必填。非自定义 code 的卡券不填。
     * @return boolean
     */
    public function unavailableCardCode($code,$card_id='') {
        $data = array(
            'code' => $code,
        );
        if ($card_id)
            $data['card_id'] = $card_id;
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::CARD_CODE_UNAVAILABLE . 'access_token=' . $this->access_token, self::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return true;
        }
        return false;
    }
    /**
     * 库存修改
     * @param string $data
     * @return boolean
     */
    public function modifyCardStock($data) {
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::CARD_MODIFY_STOCK . 'access_token=' . $this->access_token, self::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return true;
        }
        return false;
    }
    /**
     * 激活/绑定会员卡
     * @param string $data 具体结构请参看卡券开发文档(6.1.1 激活/绑定会员卡)章节
     * @return boolean
     */
    public function activateMemberCard($data) {
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::CARD_MEMBERCARD_ACTIVATE . 'access_token=' . $this->access_token, self::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return true;
        }
        return false;
    }
    /**
     * 会员卡交易
     * 会员卡交易后每次积分及余额变更需通过接口通知微信，便于后续消息通知及其他扩展功能。
     * @param string $data 具体结构请参看卡券开发文档(6.1.2 会员卡交易)章节
     * @return boolean|array
     */
    public function updateMemberCard($data) {
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::CARD_MEMBERCARD_UPDATEUSER . 'access_token=' . $this->access_token, self::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }
    /**
     * 更新红包金额
     * @param string $code      红包的序列号
     * @param $balance          红包余额
     * @param string $card_id   自定义 code 的卡券必填。非自定义 code 可不填。
     * @return boolean|array
     */
    public function updateLuckyMoney($code,$balance,$card_id='') {
        $data = array(
                'code' => $code,
                'balance' => $balance
        );
        if ($card_id)
            $data['card_id'] = $card_id;
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::CARD_LUCKYMONEY_UPDATE . 'access_token=' . $this->access_token, self::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return true;
        }
        return false;
    }
    /**
     * 设置卡券测试白名单
     * @param string $openid    测试的 openid 列表
     * @param string $user      测试的微信号列表
     * @return boolean
     */
    public function setCardTestWhiteList($openid=array(),$user=array()) {
        $data = array();
        if (count($openid) > 0)
            $data['openid'] = $openid;
        if (count($user) > 0)
            $data['username'] = $user;
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::CARD_TESTWHILELIST_SET . 'access_token=' . $this->access_token, self::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return true;
        }
        return false;
    }
}
