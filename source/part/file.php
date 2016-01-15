<?php


namespace Components;


  /**
   * Mail_Part_File
   *
   * @api
   * @package net.evalcode.components.mail
   * @subpackage part
   *
   * @author evalcode.net
   *
   * @property string filename
   */
  class Mail_Part_File extends Mail_Part
  {
    // CONSTRUCTION
    public function __construct($filename_, $content_, Io_Mimetype $mimeType_)
    {
      parent::__construct($content_, md5($filename_), $mimeType_);

      $this->encoding=Mail_Part::CONTENT_ENCODING_BASE64;
      $this->filename=$filename_;
    }
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @param string $filepath_
     * @param \Components\Io_Mimetype $mimeType_
     *
     * @throws \Components\Runtime_Exception
     *
     * @return \Components\Mail_Part_Image
     */
    public static function forFile(Io_File $file_)
    {
      if(false===$file_->exists())
      {
        throw new Io_Exception('mail/part/file', sprintf(
          'File does not exist [file: %1$s].', $file_
        ));
      }

      $mimeType=$file_->getMimetype();

      if(null===$mimeType)
      {
        throw new Io_Exception('mail/part/file', sprintf(
          'Unable to resolve mimetype for given file [file: %1$s].', $file_
        ));
      }

      return new static($file_->getName(), $file_->getContent(), $mimeType);
    }

    /**
     * @param string $filepath_
     * @param \Components\Io_Mimetype $mimeType_
     *
     * @throws \Components\Runtime_Exception
     *
     * @return \Components\Mail_Part_Image
     */
    public static function forFilePath($filepath_, Io_Mimetype $mimeType_=null)
    {
      if(false===@is_file($filepath_))
      {
        throw new Io_Exception('mail/part/file', sprintf(
          'Unable to resolve file for given path [filepath: %1$s].', $filepath_
        ));
      }

      if(null===$mimeType_)
        $mimeType_=Io_Mimetype::forFilePath($filepath_);

      if(null===$mimeType_)
      {
        throw new Io_Exception('mail/part/file', sprintf(
          'Unable to resolve mimetype for given path [filepath: %1$s].', $filepath_
        ));
      }

      return new static(@basename($filepath_), @file_get_contents($filepath_), $mimeType_);
    }

    /**
     * @param string $filename_
     * @param string $content_
     * @param \Components\Io_Mimetype $mimeType_
     *
     * @throws \Components\Runtime_Exception
     *
     * @return \Components\Mail_Part_Image
     */
    public static function forFileContents($filename_, $content_, Io_Mimetype $mimeType_=null)
    {
      if(null===$mimeType_)
        $mimeType_=Io_Mimetype::forFileName($filename_);

      if(null===$mimeType_)
      {
        throw new Io_Exception('mail/part/file', sprintf(
          'Unable to resolve mimetype for given filename [filename: %1$s].', $filename_
        ));
      }

      return new static($filename_, $content_, $mimeType_);
    }

    /**
     * @param string $filename_
     * @param string $content_
     * @param \Components\Io_Mimetype $mimeType_
     *
     * @throws \Components\Runtime_Exception
     *
     * @return \Components\Mail_Part_Image
     */
    public static function forFileContentsEncoded($filename_, $content_, Io_Mimetype $mimeType_=null)
    {
      $instance=static::forFileContents($filename_, $content_, $mimeType_);
      $instance->encoded=true;

      return $instance;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    public function contentEncoded()
    {
      if(isset($this->encoded))
        return $this->content;

      return parent::contentEncoded();
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    protected function compileHeaders()
    {
      $this->header('Content-Type', sprintf('%1$s; name="%2$s"', $this->mimeType->name(), $this->filename));
      $this->header('Content-Disposition', sprintf('%1$s; filename="%2$s"', $this->contentDisposition, $this->filename));
      $this->header('Content-Transfer-Encoding', $this->encoding);
      $this->header('Content-MD5', $this->contentMd5);
      $this->header('Content-ID', "<{$this->contentId}>");
      // TODO Check if google really requires it to embed images ...
      $this->header('X-Attachment-Id', $this->contentId);
    }
    //--------------------------------------------------------------------------
  }
?>
