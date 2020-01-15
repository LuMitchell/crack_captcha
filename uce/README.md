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

-观察验证码
尺寸（115x50），单字符尺寸（18x30）
背景为（255,255,255），存在干扰线（5,5,5），验证码rgb值范围广，但我们只要排除**背景**和**干扰线**，剩下的就是我们需要的验证码了。


## 识别

