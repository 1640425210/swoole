<?php
/**
 * Created by PhpStorm.
 * User: youan03
 * Date: 2019-09-26
 * Time: 10:37
 */
namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\Controller;

class Express extends Controller
{
    public function index()
    {
        // TODO: Implement index() method.
    }

    public function sfSend()
    {
        $file = EASYSWOOLE_ROOT."/storage/sf_order/requestXml/order.txt";
        $dom = simplexml_load_file($file);
        $body = $dom->xpath("Body/Order");
        $arr = [
            'orderid' => "TEST". time(),
            'express_type' => '1',
            'j_province' => '广东省',
            'j_city' => '深圳市',
            'j_county' => '福田区',
            'j_company' => '顺丰速运',
            'j_contact' => '小丰',
            'j_tel' => '95338',
            'j_address' => '新洲十一街万基商务大厦',
            'd_province' => '广东省',
            'd_city' => '深圳市',
            'd_county' => '南山区',
            'd_company' => '顺丰科技',
            'd_contact' => '小顺',
            'd_tel' => '4008111111',
            'd_address' => '学府路软件产业基地1栋B座',
            'parcel_quantity' => '1',
            'cargo_total_weight' => '1',
            'custid' => '7551234567',
            'sendstarttime' => '2012-7-30 09:30:00',
            'pay_method' => '1',
            'routelabelService' => '1',
            'is_docall' => '1'
        ];
        foreach ($arr as $k => $v){
            $dom->Body->Order[$k] = $v;
        }
        $dom->asXML($file);
        $checkword="qoLOxjkfDsfIXGRefpxoF5e8RE3dBCzP";
        $xmlContent = file_get_contents($file);
        $verifyCode = base64_encode(md5(($xmlContent . $checkword), TRUE));
        $post_data = array(
            'xml' => $xmlContent,
            'verifyCode' => $verifyCode
        );
        $resultCont = $this->send_post('http://bsp-oisp.sf-express.com/bsp-oisp/sfexpressService', $post_data);
        var_dump($resultCont);
    }

    function send_post($url, $post_data)
    {

        $postdata = http_build_query($post_data);
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type:application/x-www-form-urlencoded;charset=utf-8',
                'content' => $postdata,
                'timeout' => 15 * 60
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return $result;
    }
}