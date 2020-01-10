# ChinaPost

## 人工操作

打开查询网站，找到一个邮政单号，点击查询，出现滑动验证码，滑动滑块到指定位置。通过验证，显示结果。

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

## 破解




