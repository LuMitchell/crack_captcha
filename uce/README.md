# 优速快递查询 uce.cn

## 人工操作

打开[查询网站](http://www.uce.cn/service/expressTracking.html)，找到一个优速单号，输入图形验证码。点击查询，通过验证，显示结果。

## 观察

- 老规矩，打开F12，点击图形验证码更换验证码，拿到验证码请求路径。（这里就不再详细说明了，参照之前的chinapost）
> 验证码请求url：http://www.uce.cn/kaptcha/getKaptchaImage.do?type=trk&50

- 从验证码请求中我们需要拿到两条信息：
1. Set-Cookie: captchaIdTrk=trk_70D1C500A02F2806C8DA000003004001;（**captchaIdTrk** 验证码的标志码）（响应头）
2. 就是返回的验证码图片（body）
> 如果响应头中没有看到 **Set-Cookie: captchaIdTrk=...** 请清除该网站的cookie再去请求就能看到

- 直接拿到验证码[请求url](http://www.uce.cn/kaptcha/getKaptchaImage.do?type=trk&50)到浏览器打开，正常显示验证码，则判断不需要额外的请求参数即可拿到验证码。

- 观察验证码
尺寸（115x50），单字符尺寸（18x30）,
背景为（255,255,255），存在干扰线（5,5,5），验证码rgb值范围广，但我们只要排除**背景**和**干扰线**，剩下的就是我们需要的验证码了。

![1](https://github.com/LuMitchell/crack_captcha/blob/master/images/captcha.jpg)


## 识别

这里使用PHP

### 确定常规参数
```
var $ImageWidth = 100;
var $ImageHeight = 40;
var $WordWidth = 18;
var $WordHeight = 30;
var $WordSpacing = 1;//平均
```

### 二值化，转数组
```
$res = imagecreatefromstring($this->ImageStr);//获取image
$canvas = imagecreatetruecolor($this->ImageWidth, $this->ImageHeight);//创建画布
imagecopy($canvas, $res, 0, 0, 10, 0, $this->ImageWidth, $this->ImageHeight);//渲染画布，这里根据实际情况进行偏移，前面留白的直接截取了
$white = imagecolorallocate($canvas, 255, 255, 255);
imagefill($canvas, 0, 0, $white);
imagetruecolortopalette($canvas, false, 256);
```

进行二值化并转为data数组，把背景和干扰线设为0，颜色设为（255,255,255）,其他（也就是验证码的值）设为1，颜色设为（0,0,0）
```
$data = [];
//遍历每个像素点
for($i=0; $i < $this->ImageWidth; $i++)//从左到右
{
    for($j=0; $j < $this->ImageHeight; $j++)//从上到下
    {
        $rgb = imagecolorat($canvas,$i,$j);
        $rgbarray = imagecolorsforindex($canvas, $rgb);//获取像素点的rgb值

        if($rgbarray['red'] >= 240 && $rgbarray['green'] >= 240 && $rgbarray['blue'] >= 240)//判断为背景设为0
        {
            $data[$i][$j] = 0;
            imagecolorset($canvas, $rgb, 255, 255, 255);
        }
        else//其他设为1
        {
            $data[$i][$j] = 1;
            imagecolorset($canvas, $rgb, 0, 0, 0);
        }
    }
}
```

将二值化后的图片保存，方便查看，进行调整。

`imagejpeg($canvas,'img/test'.mt_rand(0,999).'.jpg');`

我们看下效果：
![2](https://github.com/LuMitchell/crack_captcha/blob/master/images/test300.jpg)

大概区分出来了，但是还不太好，再提高一点判断为背景的rgb值

`if($rgbarray['red'] >= 235 && $rgbarray['green'] >= 235 && $rgbarray['blue'] >= 235)`

再看下效果：
![3](https://github.com/LuMitchell/crack_captcha/blob/master/images/test909.jpg)

还不太理想，再扩大

`if($rgbarray['red'] >= 230 && $rgbarray['green'] >= 230 && $rgbarray['blue'] >= 230)`

效果：
![4](https://github.com/LuMitchell/crack_captcha/blob/master/images/test959.jpg)

还是差点，再扩大，顺便把周围多余的白边截取了，提高效率。

```
var $ImageWidth = 85;
var $ImageHeight = 32;
var $WordWidth = 18;
var $WordHeight = 30;
var $WordSpacing = 1;//平均
```

`imagecopy($canvas, $res, 0, 0, 15, 2, $this->ImageWidth, $this->ImageHeight);`

`if($rgbarray['red'] >= 225 && $rgbarray['green'] >= 225 && $rgbarray['blue'] >= 225)`

最后效果：
![5](https://github.com/LuMitchell/crack_captcha/blob/master/images/test422.jpg)

> 具体根据实际情况来调节相应的参数，边调边改

