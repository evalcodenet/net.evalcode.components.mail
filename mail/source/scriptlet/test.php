<?php


  class Mail_Scriptlet_Test extends Http_Scriptlet
  {
    public function post()
    {
      $mail=Mail::utf8();
      $mail->subject='容易使用的内容管理系统，并根据您行业特征而定制的';
      $mail->from('noreply@evalcode.net', 'noreply@evalcode.net');

      $message=Mail_Part::related();
      $image=Mail_Part_Image::forFilePath('/tmp/image.png');

      $text=Mail_Part::alternative();
      $text->add(Mail_Part_Text::utf8('容易使用的内容管理系统，并根据您行业特征而定制的'));
      $text->add(Mail_Part_Html::utf8(@file_get_contents('/tmp/content.html')));

      $message->add($text);
      $message->add($image);

      $mail->add($message);
      $mail->attach(Mail_Part_File::forFile(Io::file('/tmp/archive.zip')));

      return;

      $transport=new Mail_Transport_Phpmail();
      $transport->send('carsten.schipke@evalcode.net', $mail);
      $transport->send('carsten.schipke@gmail.com', $mail);
      $transport->send('carstenschipke@163.com', $mail);
    }

    public function get()
    {
      return $this->post();
    }
  }
?>
