<?php
/**
 * Created by PhpStorm.
 * User: qianqian
 * Date: 2020/2/2
 * Time: 14:29
 */
class OpenApi
{
    /**
     * 请求网关
     * @var string
     */
    private $gateway='https://shq-api.51fubei.com/gateway/agent';
    /**
     * 密钥
     * @var string
     */
    private $appSecret='';


    /**
     * 生成提交结果参数
     * @param $commonData 公共参数
     * @param array $bizContent 业务参数
     * @return bool|string
     * @throws Exception
     */
    public function actionApi($commonData,$bizContent=[]){
        $commonData['biz_content']=json_encode($bizContent);
        $commonData['sign'] = $this->getSign($commonData);
        try{
            $result = $this->curlPostContents($this->gateway,$commonData);
            return $result;
        }catch (Exception $e){
            throw new Exception('交易异常'.$e->getMessage());
        }
    }

    /**
     * 字典排序
     * @param $data
     * @return array
     */
    public function arrSort($data){
        //数组键名小写
        $arr = array_change_key_case($data);
        //先进行键升序排列
        ksort($arr);
        return $arr;
    }

    /**
     * 生成签名
     * @param $data
     * @return string
     */
    public function getSign($data){

        $arr = $this->arrSort($data);
        //全部小写合并字符串
        $str = '';
        foreach ($arr as $key => $value) {
            $str .= strtolower($key).'='.$value.'&';
        }
        $str =trim($str,'&').$this->appSecret;
        //获取待加密字符串
        return strtoupper(md5($str));
    }

    /**
     * 提交提交结果
     * @param $url 网关地址
     * @param array $data 请求参数
     * @param int $timeout 超时时间
     * @return bool|string
     * @throws Exception
     */
    public function curlPostContents($url, $data = array(), $timeout=10){
        $data_string = json_encode($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_string);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_TIMEOUT,$timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string))
        );
        $result = curl_exec($ch);
        if ($no = curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            if(in_array(intval($no), [7, 28], true)) {
                throw new Exception('连接或请求超时' . $error, $no);
            }
        }
        curl_close($ch);
        return $result;
    }

}


//使用案例
$openApi=new OpenApi();
//公共参数
$commonData=[
    'vendor_sn'=>'',
    'method'=>'openapi.agent.merchant.querystatus',
    'nonce'=>'12312',
];
//业务参数
$bizContent=[
    'merchant_code'=>'测试198'
];
var_dump($openApi->actionApi($commonData,$bizContent));