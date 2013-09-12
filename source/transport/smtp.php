<?php


namespace Components;


  /**
   * Mail_Transport_Smtp
   *
   * @api
   * @package net.evalcode.components.mail
   * @subpackage transport
   *
   * @author evalcode.net
   */
  class Mail_Transport_Smtp implements Mail_Transport
  {
    // OVERRIDES
    public function send($to_, Mail $mail_)
    {
      // TODO Implement
    }

    public function sendRaw($to_, $subject_, $source_, array $headers_)
    {
      // TODO Implement
    }
    //--------------------------------------------------------------------------
  }
?>
