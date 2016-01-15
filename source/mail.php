<?php


namespace Components;


  /**
   * Mail
   *
   * @api
   * @package net.evalcode.components.mail
   *
   * @author evalcode.net
   *
   * @property string date
   * @property string subject
   * @property string version
   * @property string messageId
   *
   * @method \Components\Mail to
   * @method \Components\Mail cc
   * @method \Components\Mail bcc
   * @method \Components\Mail from
   * @method \Components\Mail replyTo
   */
  class Mail extends Mail_Part
  {
    // PREDEFINED PROPERTIES
    const DATE_RFC_2822='r'; // Conform with RFC-2822 / Internet Message Format
    const MIME_VERSION='1.0 (Generated by: net.evalcode.mail)';
    //--------------------------------------------------------------------------


    // CONSTRUCTION
    public function __construct(Io_Charset $charset_)
    {
      parent::__construct(null, md5(uniqid()), Io_Mimetype::MULTIPART_MIXED($charset_));

      $this->date=new \DateTime();
      $this->version=self::MIME_VERSION;
      $this->messageId=md5(uniqid());
    }
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @return \Components\Mail
     */
    public static function ascii()
    {
      return new self(Io_Charset::US_ASCII());
    }

    /**
     * @return \Components\Mail
     */
    public static function latin()
    {
      return new self(Io_Charset::ISO_8859_15());
    }

    /**
     * @return \Components\Mail
     */
    public static function utf8()
    {
      return new self(Io_Charset::UTF_8());
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    public function subjectEncoded()
    {
      return mb_encode_mimeheader(
        $this->subject,
        $this->mimeType->charset()->name(),
        'Q',
        "\n"
      );
    }

    public function messageId()
    {
      $from=$this->from();
      $sender=reset($from);
      $domain=substr($sender, strpos($sender, '@')+1);

      return sprintf('<%1$s@%2$s>', $this->messageId, $domain);
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    protected function compileHeaders()
    {
      $this->header('MIME-Version', $this->version);
      $this->header('Date', $this->date->format(self::DATE_RFC_2822));
      $this->header('Subject', $this->subjectEncoded());

      foreach(['to', 'cc', 'bcc', 'from', 'reply-To'] as $addressType)
      {
        if(!($addresses=$this->{str_replace('-', '', $addressType)}()) || false===is_array($addresses))
          continue;

        $addressStrings=[];
        foreach($addresses as $email=>$name)
        {
          if(null===$name || 1>mb_strlen($name))
            $addressStrings[]=$email;
          else
            $addressStrings[]="\"$name\" <$email>";
        }

        $this->header(ucfirst($addressType), implode(', ', $addressStrings));
        $this->header('Message-Id', $this->messageId());

        parent::compileHeaders();
      }
    }
    //--------------------------------------------------------------------------
  }
?>
