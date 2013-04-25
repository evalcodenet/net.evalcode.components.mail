<?php


namespace Components;


  /**
   * Mail_Part
   *
   * @package net.evalcode.components
   * @subpackage mail
   *
   * @author evalcode.net
   *
   * @property string name
   * @property string content
   * @property string contentId
   * @property string contentMd5
   * @property string contentDisposition
   * @property string boundary
   * @property string encoding
   * @property Io_MimeType mimeType
   */
  class Mail_Part
  {
    // PREDEFINED PROPERTIES
    const CONTENT_DISPOSITION_INLINE='inline';
    const CONTENT_DISPOSITION_ATTACHMENT='attachment';
    const CONTENT_ENCODING_BASE64='base64';
    const CONTENT_ENCODING_QUOTED_PRINTABLE='quoted-printable';
    //--------------------------------------------------------------------------


    // CONSTRUCTION
    public function __construct($content_, $contentId_, Io_MimeType $mimeType_)
    {
      if(null!==$content_)
      {
        $this->content=$content_;
        $this->contentMd5=md5($content_);
      }

      if(null===$contentId_)
        $contentId_=md5(uniqid());

      $this->contentId=$contentId_;
      $this->contentDisposition=self::CONTENT_DISPOSITION_INLINE;
      $this->boundary=md5("{$contentId_}-boundary");
      $this->mimeType=$mimeType_;
    }
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @return Mail_Part
     */
    public static function alternative()
    {
      return new self(null, md5(uniqid()), Io_MimeType::MULTIPART_ALTERNATIVE());
    }

    /**
     * @return Mail_Part
     */
    public static function mixed()
    {
      return new self(null, md5(uniqid()), Io_MimeType::MULTIPART_MIXED());
    }

    /**
     * @return Mail_Part
     */
    public static function related()
    {
      return new self(null, md5(uniqid()), Io_MimeType::MULTIPART_RELATED());
    }
    //--------------------------------------------------------------------------


    // ACCESSORS/MUTATORS
    /**
     * @param Mail_Part $part_
     *
     * @return Mail|Mail_Part
     */
    public function add(Mail_Part $part_)
    {
      array_push($this->m_parts, $part_);

      return $this;
    }

    /**
     * @param Mail_Part $part_
     *
     * @return Mail|Mail_Part
     */
    public function attach(Mail_Part $part_)
    {
      $part_->contentDisposition=self::CONTENT_DISPOSITION_ATTACHMENT;
      array_push($this->m_parts, $part_);

      return $this;
    }

    /**
     * @param string $header_
     * @param string $value_
     *
     * @return string|Mail
     */
    public function header($header_, $value_=null)
    {
      $this->m_headers[$header_]=$value_;

      return $this;
    }

    public function headers()
    {
      return $this->m_headers;
    }

    public function headersAsString()
    {
      return $this->compile()->m_compiledHeaders;
    }

    public function message()
    {
      return $this->compile()->m_compiledMessage;
    }

    public function source()
    {
      return $this->compile()->m_compiledSource;
    }

    public function contentEncoded()
    {
      if(Mail_Part::CONTENT_ENCODING_QUOTED_PRINTABLE===$this->encoding)
        return quoted_printable_encode($this->content);
      if(Mail_Part::CONTENT_ENCODING_BASE64===$this->encoding)
        return chunk_split(base64_encode($this->content), 76);

      return $this->content;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    public function __get($name_)
    {
      if(array_key_exists($name_, $this->m_properties))
        return $this->m_properties[$name_];

      return null;
    }

    public function __set($name_, $value_)
    {
      $this->m_properties[$name_]=$value_;
      $this->m_compiled=false;

      return $this;
    }

    public function __isset($name_)
    {
      return array_key_exists($name_, $this->m_properties);
    }

    public function __unset($name_)
    {
      if(array_key_exists($name_, $this->m_properties))
      {
        unset($this->m_properties[$name_]);
        $this->m_compiled=false;
      }

      return $this;
    }

    public function __call($name_, array $args_=array())
    {
      if(0===count($args_))
      {
        if(array_key_exists($name_, $this->m_properties))
          return $this->m_properties[$name_];

        return null;
      }

      if(1===count($args_))
      {
        $this->m_properties[$name_]=$args_[0];
        $this->m_compiled=false;

        return $this;
      }

      if(2===count($args_))
      {
        $this->m_properties[$name_][$args_[0]]=$args_[1];
        $this->m_compiled=false;
      }

      return $this;
    }

    public function __toString()
    {
      return $this->source();
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private $m_headers=array();
    private $m_parts=array();
    private $m_properties=array();

    private $m_compiled=false;

    private $m_compiledHeaders='';
    private $m_compiledMessage='';
    private $m_compiledSource='';
    //-----


    protected function compileHeaders()
    {
      if(__CLASS__===get_class($this) || $this instanceof Mail)
      {
        $this->header('Content-Type', sprintf('%1$s; boundary=%2$s', $this->mimeType, $this->boundary));
      }
      else
      {
        $this->header('Content-Type', sprintf('%1$s; charset=%2$s', $this->mimeType->name(), $this->mimeType->charset()->name()));

        if($this instanceof Mail_Part_File)
          $this->header('Content-Disposition', $this->contentDisposition);

        $this->header('Content-Transfer-Encoding', $this->encoding);
        $this->header('Content-MD5', $this->contentMd5);
      }
    }

    protected function compileMessage()
    {
      $message=array();

      if(count($this->m_parts))
      {
        foreach($this->m_parts as $part)
        {
          if(__CLASS__===get_class($this) || $this instanceof Mail)
            $message[]=sprintf('--%1$s', $this->boundary);

          $message[]=$part->source();
        }

        if(__CLASS__===get_class($this) || $this instanceof Mail)
          $message[]=sprintf('--%1$s--', $this->boundary);
      }
      else
      {
        $message[]=$this->contentEncoded();
      }

      return implode("\n", $message);
    }

    /**
     * @return Mail_Part
     */
    private function compile()
    {
      if($this->m_compiled)
        return $this;

      $this->compileHeaders();

      $headers=array();
      foreach($this->headers() as $header=>$value)
        $headers[]="$header: $value";

      $this->m_compiledHeaders=implode("\n", $headers);
      $this->m_compiledMessage=$this->compileMessage();
      $this->m_compiledSource=$this->m_compiledHeaders."\n\n".$this->m_compiledMessage;

      $this->m_compiled=true;

      return $this;
    }
    //--------------------------------------------------------------------------
  }
?>
