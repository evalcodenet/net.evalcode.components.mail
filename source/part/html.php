<?php


namespace Components;


  /**
   * Mail_Part_Html
   *
   * @api
   * @package net.evalcode.components.mail
   * @subpackage part
   *
   * @author evalcode.net
   */
  class Mail_Part_Html extends Mail_Part
  {
    // CONSTRUCTION
    public function __construct($content_, Io_Charset $charset_)
    {
      parent::__construct($content_, md5($content_), Io_Mimetype::TEXT_HTML($charset_));

      $this->encoding=Mail_Part::CONTENT_ENCODING_BASE64;
    }
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @return \Components\Mail_Part_Html
     */
    public static function ascii($content_)
    {
      return new self($content_, Io_Charset::US_ASCII());
    }

    /**
     * @return \Components\Mail_Part_Html
     */
    public static function latin($content_)
    {
      return new self($content_, Io_Charset::ISO_8859_15());
    }

    /**
     * @return \Components\Mail_Part_Html
     */
    public static function utf8($content_)
    {
      return new self($content_, Io_Charset::UTF_8());
    }
    //--------------------------------------------------------------------------
  }
?>
