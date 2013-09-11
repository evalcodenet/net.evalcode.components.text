<?php


namespace Components;


  /**
   * Text_Json
   *
   * @package net.evalcode.components
   * @subpackage text
   *
   * @author evalcode.net
   */
  class Text_Json implements Object, Value_String
  {
    // PREDEFINED PROPERTIES
    const JSON_DEFAULT=0;
    const JSON_FORMAT=2;
    const JSON_DECODE_UNICODE=4;
    //--------------------------------------------------------------------------


    // PROPERTIES
    public $linebreakCharacter="\n";
    public $indentationCharacter=" ";
    public $indentationSize=2;
    public $indentationDefault=1;
    //--------------------------------------------------------------------------


    // CONSTRUCTION
    public function __construct($value_, Io_Charset $charset_=null)
    {
      if(null===$charset_)
        $charset_=Io_Charset::UTF_8();

      $this->m_value=$value_;
      $this->m_charset=$charset_;
    }
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @param string $json_
     *
     * @return \Components\Text_Json
     */
    public static function valueOf($json_)
    {
      return new self($json_);
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    /**
     * @param \Components\Io_Charset $outputCharset_
     *
     * @return string
     */
    public function get($options_=self::JSON_DEFAULT, Io_Charset $outputCharset_=null)
    {
      $value=$this->m_value;

      if(null===$outputCharset_)
        $outputCharset_=$this->m_charset;
      else if(false===$this->m_charset->equals($outputCharset_))
        $value=$this->m_charset->convert($this->m_value, $outputCharset_);

      if(self::JSON_DEFAULT===$options_)
        return $value;

      if(0<($options_&self::JSON_DECODE_UNICODE))
        $value=$outputCharset_->unicodeDecode($value);

      if(0<($options_&self::JSON_FORMAT))
        return $this->formatImpl($value);

      return $value;
    }

    public function decode()
    {
      return json_decode($this->m_value);
    }

    public function format()
    {
      return $this->formatImpl($this->m_value);
    }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    /**     * @see \Components\Value_String::value() \Components\Value_String::value()
     */
    public function value()
    {
      return $this->m_value;
    }

    /**     * @see \Components\Object::hashCode() \Components\Object::hashCode()
     */
    public function hashCode()
    {
      return object_hash($this);
    }

    /**     * @see \Components\Object::equals() \Components\Object::equals()
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return String::equal($this->m_value, $object_->m_value);

      return false;
    }

    /**     * @see \Components\Object::__toString() \Components\Object::__toString()
     */
    public function __toString()
    {
      return $this->m_value;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private static $m_tokenIndent=array(
      '{'=>0,
      '['=>0
    );
    private static $m_tokenOutdent=array(
      '}'=>0,
      ']'=>0
    );
    private static $m_tokenLinebreak=array(
      '{'=>0,
      '['=>0,
      ','=>0
    );
    private static $m_tokenWhitespace=array(
      ':'=>0,
      ','=>0
    );

    /**
     * @var \Components\Io_Charset
     */
    private $m_charset;
    /**
     * @var string
     */
    private $m_value;
    //-----


    protected function formatImpl($json_)
    {
      $string='';
      $indent='';

      $level=0;
      $length=strlen($json_);

      for($i=0; $i<$length; $i++)
      {
        if(isset(self::$m_tokenIndent[$json_[$i]]))
        {
          $string.="\n".str_repeat($this->indentationCharacter, $this->indentationSize*$level);
          $level++;
        }
        else if(isset(self::$m_tokenOutdent[$json_[$i]]))
        {
          $level--;
          $string.="\n".str_repeat($this->indentationCharacter, $this->indentationSize*$level);
        }

        $string.=$json_[$i];

        if(isset(self::$m_tokenLinebreak[$json_[$i]]))
          $string.="\n".str_repeat($this->indentationCharacter, $this->indentationSize*$level);
        else if(isset(self::$m_tokenWhitespace[$json_[$i]]))
          $string.=' ';
      }

      return trim($string);
    }
    //--------------------------------------------------------------------------
  }
?>
