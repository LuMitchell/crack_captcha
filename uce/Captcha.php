<?php if(!defined('PATH_INC')) exit('Request Error!');

class Captcha
{
    var $ImagePath;
    var $ImageStr;
    var $ImageRes;
    var $DataArr;
    var $Data;
    var $Chars;
    
    public function __construct($imagestr = '', $imageres = '', $imagepath = '')
    {
          $this->Chars = [];
          $this->ImageStr = $imagestr;
          $this->ImageRes = $imageres;
          $this->ImagePath = $imagepath;
    }
    
    public function SetImageStr($imagestr, $imagepath='')
    {
        $this->ImageStr = $imagestr;
        $this->ImagePath = $imagepath;
    }
    
    public function SetImageRes($imageres, $imagepath='')
    {
        $this->ImageRes = $imageres;
        $this->ImagePath = $imagepath;
    }
    
    //二值化，转为数组
    public function getDataArr()
    {
  
    }
    
    function GetNumStr()
    {		
    }
    
    function GetResult()
    {
        $this->getDataArr();
        $this->GetNumStr();       
        return $this->Data;
    }
    
    //输出字符
    function PrintChar($data)
    {
        foreach($data as $i => $char)
        {
                $key = key($char);		  
                foreach($char[$key] as $k => $v)//$char每个字符
                {
                    foreach($char as $vv)
                    {
                        if($vv[$k] == 1) echo '<span style="display:inline-block;width:6px;height:6px;background-color:#00f;"></span>';
                        else echo '<span style="display:inline-block;width:6px;height:6px;background-color:#fff;"></span>';
                    }
                    echo '<br />';
                }
                echo $i.'<br /><br />';					
        }
    }
}
