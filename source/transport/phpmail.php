<?php


namespace Components;


  /**
   * Mail_Transport_Phpmail
   *
   * @package net.evalcode.components
   * @subpackage mail.transport
   *
   * @author evalcode.net
   */
  class Mail_Transport_Phpmail implements Mail_Transport
  {
    // OVERRIDES
    public function send($to_, Mail $mail_)
    {
      return @mail($to_, $mail_->subjectEncoded(), $mail_->message(), $mail_->headersAsString());
    }

    public function sendRaw($to_, $subject_, $source_, array $headers_)
    {
      return @mail($to_, $subject_, $source_);
    }
    //--------------------------------------------------------------------------
  }
?>
