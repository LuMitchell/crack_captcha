
require_once 'bkcaptcha.php' ;

$cfg['returnheader'] = true;
$cfg['useragent'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.142 Safari/537.36';

//$cfg['ssl'] = true;
$imgurl = 'http://www.uce.cn/kaptcha/getKaptchaImage.do?type=trk&50';
$image = curlOpen($imgurl, $cfg);
$img_data = explode("\r\n\r\n", $image, 2);
$img_header = $img_data[0];
$image = $img_data[1];

file_put_contents('img/img3/express'.mt_rand(0, 999).'.jpg', $image);

preg_match_all('#Set-Cookie\: (.*?);#is', $img_header, $cookies);

foreach ($cookies[1] as $cookie)
{
    if(stripos($cookie, 'captchaIdTrk') !== false)
    {
        $trk = str_replace('captchaIdTrk=', '', $cookie);
    }
}

$a = new bkcaptcha();
$a->SetImageStr($image);
@$code = $a->GetResult();
var_dump($code);
