<?php


  /**
   * Mail_Part_File
   *
   * @package net.evalcode.components
   * @subpackage mail.part
   *
   * @author evalcode.net
   *
   * @property string filename
   */
  class Mail_Part_File extends Mail_Part
  {
    // CONSTRUCTION
    public function __construct($filename_, $content_, Io_MimeType $mimeType_)
    {
      parent::__construct($content_, md5($filename_), $mimeType_);

      $this->encoding=Mail_Part::CONTENT_ENCODING_BASE64;
      $this->filename=$filename_;
    }
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @param string $filepath_
     * @param Io_MimeType $mimeType_
     *
     * @throws Runtime_Exception
     *
     * @return Mail_Part_Image
     */
    public static function forFile(Io_File $file_)
    {
      if(false===$file_->exists())
      {
        throw new Runtime_Exception('mail/part/image', sprintf(
          'File does not exist [file: %1$s].', $file_
        ));
      }

      $mimeType=$file_->getMimeType();

      if(null===$mimeType)
      {
        throw new Runtime_Exception('mail/part/image', sprintf(
          'Unable to resolve mimetype for given file [file: %1$s].', $file_
        ));
      }

      return new self($file_->getName(), $file_->getContent(), $mimeType);
    }

    /**
     * @param string $filepath_
     * @param Io_MimeType $mimeType_
     *
     * @throws Runtime_Exception
     *
     * @return Mail_Part_Image
     */
    public static function forFilePath($filepath_, Io_MimeType $mimeType_=null)
    {
      if(false===@is_file($filepath_))
      {
        throw new Runtime_Exception('mail/part/image', sprintf(
          'Unable to resolve file for given path [filepath: %1$s].', $filepath_
        ));
      }

      if(null===$mimeType_)
        $mimeType_=Io_MimeType::forFilePath($filepath_);

      if(null===$mimeType_)
      {
        throw new Runtime_Exception('mail/part/image', sprintf(
          'Unable to resolve mimetype for given path [filepath: %1$s].', $filepath_
        ));
      }

      return new self(@basename($filepath_), @file_get_contents($filepath_), $mimeType_);
    }

    /**
     * @param string $filename_
     * @param string $content_
     * @param Io_MimeType $mimeType_
     *
     * @throws Runtime_Exception
     *
     * @return Mail_Part_Image
     */
    public static function forFileContents($filename_, $content_, Io_MimeType $mimeType_=null)
    {
      if(null===$mimeType_)
        $mimeType_=Io_MimeType::forFileName($filename_);

      if(null===$mimeType_)
      {
        throw new Runtime_Exception('mail/part/image', sprintf(
          'Unable to resolve mimetype for given filename [filename: %1$s].', $filename_
        ));
      }

      return new self($filename_, $content_, $mimeType_);
    }

    /**
     * @param string $filename_
     * @param string $content_
     * @param Io_MimeType $mimeType_
     *
     * @throws Runtime_Exception
     *
     * @return Mail_Part_Image
     */
    public static function forFileContentsEncoded($filename_, $content_, Io_MimeType $mimeType_=null)
    {
      $instance=self::forFileContents($filename_, $content_, $mimeType_);
      $instance->encoded=true;

      return $instance;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
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
      $this->header('Content-Disposition', $this->contentDisposition);
      $this->header('Content-Transfer-Encoding', $this->encoding);
      $this->header('Content-MD5', $this->contentMd5);
      $this->header('Content-ID', "<{$this->contentId}>");
      // TODO Check if google really requires it to embed images ...
      $this->header('X-Attachment-Id', $this->contentId);
    }
    //--------------------------------------------------------------------------
  }
?>
