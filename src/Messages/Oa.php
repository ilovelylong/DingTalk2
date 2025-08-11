<?php

/*
 * @Author: sunkaiyuan 
 * @Date: 2021-11-17 13:46:12 
 * @Last Modified by: sunkaiyuan
 * @Last Modified time: 2021-11-17 17:09:27
 */

namespace EasyDingTalk\Messages;

class Oa extends Message
{
    protected $type = 'oa';



    public function setMessage_url($value)
    {
        $this->attributes['message_url'] = $value;
        return $this;
    }

    public function setPc_message_url($value)
    {
        $this->attributes['pc_message_url'] = $value;
        return $this;
    }
    public function setHead($value)
    {
        $this->attributes['head'] = $value;
        return $this;
    }
    public function setHeadBgcolor($value)
    {
        $this->attributes['head']['bgcolor'] = $value;
        return $this;
    }
    public function setHeadText($value)
    {
        $this->attributes['head']['text'] = $value;
        return $this;
    }
    public function setStatusBar($value)
    {
        $this->attributes['status_bar'] = $value;
        return $this;
    }
    public function setStatusBarStatusValue($value)
    {
        $this->attributes['status_bar']['status_value'] = $value;
        return $this;
    }
    public function setStatusBarStatusBg($value)
    {
        $this->attributes['status_bar']['status_bg'] = $value;
        return $this;
    }
    public function setBody($value)
    {
        $this->attributes['body'] = $value;
        return $this;
    }

    public function setBodyTitle($value)
    {
        $this->attributes['body']['title'] = $value;
        return $this;
    }
    public function setBodyForm($value)
    {
        $this->attributes['body']['form'] = $value;
        return $this;
    }
    public function setBodyFormKey($value)
    {
        $this->attributes['body']['form']['key'] = $value;
        return $this;
    }
    public function setBodyFormValue($value)
    {
        $this->attributes['body']['form']['value'] = $value;
        return $this;
    }
    public function setBodyRich($value)
    {
        $this->attributes['body']['rich'] = $value;
        return $this;
    }
    public function setBodyRichNum($value)
    {
        $this->attributes['body']['rich']['num'] = $value;
        return $this;
    }
    public function setBodyRichUnit($value)
    {
        $this->attributes['body']['rich']['unit'] = $value;
        return $this;
    }
    public function setBodyContent($value)
    {
        $this->attributes['body']['content'] = $value;
        return $this;
    }
    public function setBodyImage($value)
    {
        $this->attributes['body']['image'] = $value;
        return $this;
    }
    public function setBodyFileCount($value)
    {
        $this->attributes['body']['file_count'] = $value;
        return $this;
    }
    public function setBodyFileAuthor($value)
    {
        $this->attributes['body']['author'] = $value;
        return $this;
    }
}
