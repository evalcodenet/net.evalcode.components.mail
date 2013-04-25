<?php


namespace Components;


  /**
   * Mail_Transport
   *
   * @package net.evalcode.components
   * @subpackage mail
   *
   * @author evalcode.net
   */
  interface Mail_Transport
  {
    // ACCESSORS/MUTATORS
    function send($to_, Mail $mail_);
    function sendRaw($to_, $subject_, $source_, array $headers_);
    //--------------------------------------------------------------------------
  }
?>
