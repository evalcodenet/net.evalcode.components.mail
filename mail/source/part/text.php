<?php


namespace Components;


  /**
   * Mail_Part_Text
   *
   * @package net.evalcode.components
   * @subpackage mail.part
   *
   * @author evalcode.net
   */
  class Mail_Part_Text extends Mail_Part
  {
    // CONSTRUCTION
    public function __construct($content_, Io_Charset $charset_)
    {
      parent::__construct($content_, md5($content_), Io_MimeType::TEXT_PLAIN($charset_));

      $this->encoding=Mail::CONTENT_ENCODING_QUOTED_PRINTABLE;
    }
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @return Mail_Part_Text
     */
    public static function ascii($content_)
    {
      return new self($content_, Io_Charset::US_ASCII());
    }

    /**
     * @return Mail_Part_Text
     */
    public static function latin($content_)
    {
      return new self($content_, Io_Charset::ISO_8859_15());
    }

    /**
     * @return Mail_Part_Text
     */
    public static function utf8($content_)
    {
      return new self($content_, Io_Charset::UTF_8());
    }
    //--------------------------------------------------------------------------
  }
?>
