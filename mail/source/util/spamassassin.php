<?php


namespace Components;


  /**
   * Mail_Util_Spamassassin
   *
   * @package net.evalcode.components
   * @subpackage mail.util
   *
   * @author evalcode.net
   */
  class Mail_Util_Spamassassin
  {
    // PREDEFINED PROPERTIES
    // FIXME Externalize configuration.
    const CMD_BIN='spamc';
    const CMD_ARG='-R';
    //--------------------------------------------------------------------------


    // CONSTUCTION
    public function __construct(Mail $mail_)
    {
      $this->m_mail=$mail_;
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    // FIXME Error handling.
    public function analyze()
    {
      $report=null;
      $mail=(string)$this->m_mail;

      $desc=array(
        0=>array('pipe', 'r'),
        1=>array('pipe', 'w+'),
        2=>array('pipe', 'w+'),
      );

      $pipes=array();
      $pid=@proc_open(self::CMD_BIN.' '.escapeshellarg(self::CMD_ARG), $desc, $pipes);

      if(false!==$pid)
      {
        @fwrite($pipes[0], $mail);
        @fclose($pipes[0]);

        $report=stream_get_contents($pipes[1]);

        @proc_close($pid);
      }

      if(null===$report)
        return;

      $lines=explode("\n", $report);
      $lines=array_reverse($lines);

      $score=$lines[count($lines)-1];
      $chunks=explode('/', $score);

      $this->m_score=(float)reset($chunks);
      $this->m_scoreThreshold=(float)end($chunks);

      $this->m_tests=array();
      foreach($lines as $line)
      {
        if(String::startsWith($line, '---'))
          break;

        $matches=array();
        preg_match('/([-+\d.\d]+)\s+(\w*)\s+([\s\S]*)/', $line, $matches);

        if(isset($matches[3]))
          $this->m_tests[$matches[2]]=array((float)$matches[1], $matches[3]);
      }
    }

    public function getTests()
    {
      return $this->m_tests;
    }

    public function getScore()
    {
      return $this->m_score;
    }

    public function getScoreThreshold()
    {
      return $this->m_scoreThreshold;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private $m_tests=array();
    /**
     * @var Mail
     */
    private $m_mail;
    private $m_score;
    private $m_scoreThreshold;
    //--------------------------------------------------------------------------
  }
?>
