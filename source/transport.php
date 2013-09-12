<?php


namespace Components;


  /**
   * Mail_Transport
   *
   * @package net.evalcode.components.mail
   *
   * @author evalcode.net
   *
   * @api
   */
  interface Mail_Transport
  {
    // ACCESSORS
    function send($to_, Mail $mail_);
    function sendRaw($to_, $subject_, $source_, array $headers_);
    //--------------------------------------------------------------------------
  }
?>
