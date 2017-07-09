<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
function getJson($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    curl_close($ch);
    return json_decode($output, true);
}

function get_uri($url)
{
    $ch = curl_init() or die (curl_error());
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 360);
    $response = curl_exec($ch);
    curl_close($ch);
    //显示获得的数据
    return $response;
}

/**
 * 发送邮件
 * @param  array $address 需要发送的邮箱地址 发送给多个地址需要写成数组形式
 * @param  string $subject 标题
 * @param  string $content 内容
 * @return array  放回状态吗和提示信息
 */
function send_email($address, $subject, $content)
{
    $email_smtp = config('email.EMAIL_SMTP');
    $email_username = config('email.EMAIL_USERNAME');
    $email_password = config('email.EMAIL_PASSWORD');
    $email_from_name = config('email.EMAIL_FROM_NAME');
    if (empty($email_smtp) || empty($email_username) || empty($email_password) || empty($email_from_name)) {
        return ["error" => 1, "message" => '邮箱请求参数不全，请检测邮箱的合法性'];
    }
    $phpmailer = new PHPMailer();
    // 	设置PHPMailer使用SMTP服务器发送Email
    $phpmailer->IsSMTP();
    // 	设置为html格式
    $phpmailer->IsHTML(true);
    // 	设置邮件的字符编码'
    $phpmailer->CharSet = 'UTF-8';
    // 设置SMTP服务器。
    $phpmailer->Host = $email_smtp;
    // 设置为"需要验证"
    $phpmailer->SMTPAuth = true;
    // 设置用户名
    $phpmailer->Username = $email_username;
    // 设置密码
    $phpmailer->Password = $email_password;
    // 设置邮件头的From字段。
    $phpmailer->From = $email_username;
    // 设置发件人名字
    $phpmailer->FromName = $email_from_name;
    // 添加收件人地址，可以多次使用来添加多个收件人
    if (is_array($address)) {
        foreach ($address as $addressv) {
            //验证邮件地址,非邮箱地址返回为false
            if (false === filter_var($address, FILTER_VALIDATE_EMAIL)) {
                return ["error" => 1, "message" => '邮箱格式错误'];
            }
            $phpmailer->AddAddress($addressv);
        }
    } else {
        //验证邮件地址,非邮箱地址返回为false
        if (false === filter_var($address, FILTER_VALIDATE_EMAIL)) {
            return ["error" => 1, "message" => '邮箱格式错误'];
        }
        $phpmailer->AddAddress($address);
    }
    // 设置邮件标题
    $phpmailer->Subject = $subject;
    // 设置邮件正文,这里最好修改为这个，不是boby
    $phpmailer->MsgHTML($content);
    // 发送邮件。
    if (!$phpmailer->Send()) {
        return ["error" => 1, "message" => $phpmailer->ErrorInfo];
    }
    return ["error" => 0];
}
