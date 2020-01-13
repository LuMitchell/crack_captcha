# ChinaPost

## 人工操作

打开[查询网站](http://yjcx.chinapost.com.cn/qps/yjcx)，找到一个邮政单号，点击查询，出现滑动验证码，滑动滑块到指定位置。通过验证，显示结果。

## 观察

在查询页面打开F12开发者工具，切换到Network栏。

我们不需要其他的信息，直接清空。

再点击查询按钮，我们看到有三条请求，分别是：

1. 请求验证码
2. 背景验证码图片
3. 小块验证码图片

![1](https://github.com/LuMitchell/crack_captcha/blob/master/chinapost/images/61826.png)


第一个请求返回的是个json，分别是：
1. YYPng_base64 背景图片的base64
2. CutPng_base64 小块图片的base64
3. uuid 标志的id（后面请求结果的时候要用到）

![2](https://github.com/LuMitchell/crack_captcha/blob/master/chinapost/images/62111.png)

继续操作滑块，滑到正确位置。

这时候多了一条请求**VerifyCheck**，我们需要的信息就在里面了。

![3](https://github.com/LuMitchell/crack_captcha/blob/master/chinapost/images/62317.png)

![4](https://github.com/LuMitchell/crack_captcha/blob/master/chinapost/images/62338.png)

观察这条请求的url、请求头和参数

![5](https://github.com/LuMitchell/crack_captcha/blob/master/chinapost/images/62401.png)

![6](https://github.com/LuMitchell/crack_captcha/blob/master/chinapost/images/62452.png)

请求头中初步观察并没有token类的校验参数，所有我们暂时不用管。

看提交的表单信息

**uuid** 就是请求验证码返回给我们的标志，

**moveEnd_X** 看意思是移动滑块的x坐标

**text[]** 这是我们查询的单号

**selectType** 选择类型（这个不用管，跟着填 1 就好了）

![7](https://github.com/LuMitchell/crack_captcha/blob/master/chinapost/images/62549.png)

到这来看我们需要的就只有 **moveEnd_X** 这个参数是变化的，需要我们来识别。

算是基础的滑动验证码。

## 识别

观察缺失的背景图片，使用取色工具取出中间缺失的rgb值为（255,255,255）。使用量取工具测定验证码图片左边到缺失处左边的距离为（184），与我们提交的**moveEnd_X**对应。可以确定这个参数就是这两边的距离。

![8](https://github.com/LuMitchell/crack_captcha/blob/master/chinapost/images/62807.png)

这时候只要找到开始缺失白边的坐标，我们从左到右遍历每一个像素点，当像素点rgb为（255,255,255）连续出现4次时判定为缺失处的最左边，也就是我们需要的距离。

这里使用PHP代码

```
//验证码图片大小为 300x180
define('IMAGE_WIDTH', 300);
define('IMAGE_HEIGHT', 180);

function getPos($image)
{
    $res = imagecreatefromstring($image);
    $canvas = imagecreatetruecolor(IMAGE_WIDTH, IMAGE_HEIGHT);
    imagecopy($canvas, $res, 0, 0, 0, 0, IMAGE_WIDTH, IMAGE_HEIGHT);
    $white = imagecolorallocate($canvas, 255, 255, 255);
    imagefill($canvas, 0, 0, $white);
    imagetruecolortopalette($canvas, false, 256);

    for($i = 10; $i < IMAGE_WIDTH; $i++)//有时候会产生白边干扰，我们偏移一点从10开始
    {
        $white = 0;
        for($j = 10; $j < IMAGE_HEIGHT; $j++)
        {
            $rgb = imagecolorat($canvas, $i, $j);
            $rgbarray = imagecolorsforindex($canvas, $rgb);

            //这里代码获取的rgb （252,255,250）与浏览器取出的rgb（255,255,255）有所差异
            if($rgbarray['red'] >= 250 &&  $rgbarray['green'] >= 250 && $rgbarray['blue'] >= 250)
            {
                $white++;
            }
            if($white > 4)//当为white连续4次以上再进行一次检查，检查横向右边第x位（这里取4）是否也是为white（缺块内）
            {
                $rgb = imagecolorat($canvas, $i+4, $j);
                $rgbarray = imagecolorsforindex($canvas, $rgb);
                if($rgbarray['red'] >= 250 &&  $rgbarray['green'] >= 250 && $rgbarray['blue'] >= 250)
                {
                    return $i;//得到距离
                }
            }
        }
    }
    return 0;//这里识别失败
}
```

至此我们得到了**moveEnd_X**的值

```
$jsonarr = json_decode($image_result, true);

$image64 = $jsonarr['YYPng_base64'];
$uuid = $jsonarr['uuid'];
echo '<img src="data:image/jpeg;base64,'.$image64.'">';

$image = base64_decode($image64);
$moveEnd_X = getPos($image);
```

带上所有参数请求

```
$url = 'http://yjcx.chinapost.com.cn/qps/showPicture/verify/slideVerifyCheck';
$cfg['post'] = ['uuid'=>$uuid, 'moveEnd_X'=>$moveEnd_X, 'text[]'=>$number, 'selectType'=>1];
```

最后拿到正确的数据

![9](https://github.com/LuMitchell/crack_captcha/blob/master/chinapost/images/63115.png)

