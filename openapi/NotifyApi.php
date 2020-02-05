<?php
/**
 * Created by PhpStorm.
 * User: shaojian
 * Date: 2020/2/2
 * Time: 16:31
 */

class NotifyApi
{
    /**
     * 密钥
     * @var string
     */
    private $appSecret='';

    /**
     * 回调验证验证
     * @param array $data
     */
    public function notify($data){
        $signData=$data['sign'];
        unset($data['sign']);
        $sign=$this->getSign($data,$this->appSecret);
        if($signData===$sign){
            echo 'success';
        }else{
            echo 'fail';
        }
    }


    /**
     * 字典排序
     * @param $data
     * @return array
     */
    private function arrSort($data){
        //数组键名小写
        $arr = array_change_key_case($data);
        //先进行键升序排列
        ksort($arr);
        return $arr;
    }

    /**
     * 生成签名
     * @param $data 数据
     * @param $appSecret 秘钥
     * @return string
     */
    private function getSign($data,$appSecret){

        $arr = $this->arrSort($data);
        //全部小写合并字符串
        $str = '';
        foreach ($arr as $key => $value) {
            $str .= strtolower($key).'='.$value.'&';
        }
        $str =trim($str,'&').$appSecret;
        //获取待加密字符串
        return strtoupper(md5($str));
    }
}