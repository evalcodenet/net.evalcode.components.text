<?php


namespace Components;


  /**
   * Text_Html_Tidy
   *
   * @api
   * @package net.evalcode.components.text
   * @subpackage html
   *
   * @author evalcode.net
   */
  class Text_Html_Tidy implements Object, Runtime_Error_Handler
  {
    // PREDEFINED PROPERTIES
    const ERROR=Debug::ERROR;
    const WARN=Debug::WARN;
    const INFO=Debug::INFO;
    //--------------------------------------------------------------------------


    // PROPERTIES
    public $config=[
      'indent'=>true,
      'indent-spaces'=>2,
      'wrap'=>80,
      'uppercase-tags'=>false,
      'uppercase-attributes'=>false,
      'quote-ampersand'=>true,
      'break-before-br'=>true,
      'quote-marks'=>true,
      'quote-nbsp'=>true,
      'numeric-entities'=>false
    ];

    public $ignoreProprietaryAttributes=true;
    //--------------------------------------------------------------------------


    // CONSTRUCTION
    public function __construct($html_, Io_Charset $charset_)
    {
      $this->m_tidy=new \tidy();

      $this->m_html=$html_;
      $this->m_charset=$charset_;
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    /**
     * @return \Components\Text_Html_Tidy
     */
    public function parse()
    {
      $charset=strtolower(str_replace(['-', '_'], null, $this->m_charset->name()));

      $config=$this->config;
      $config['char-encoding']=$charset;
      $config['drop-proprietary-attributes']=!$this->ignoreProprietaryAttributes;

      Runtime::pushRuntimeErrorHandler($this);
      $this->m_tidy->parseString($this->m_html, $config, $charset);
      Runtime::popRuntimeErrorHandler($this);

      return $this;
    }

    /**
     * Returns array as severity > line > column > message.
     *
     * @return string[]
     */
    public function getErrors()
    {
      $this->m_tidy->diagnose();

      $matches=[];
      preg_match_all('/^(?:line (\d+) column (\d+) - )?(\S+): (?:\[((?:\d+\.?){4})]:)?(.*?)$/m',
        $this->m_tidy->errorBuffer,
        $matches,
        PREG_SET_ORDER
      );

      $errors=[];

      if(false===isset($matches[0]))
        return $errors;

      foreach($matches as $match)
      {
        $match[5]=\str\trim(\html\strip($match[5]));

        if($this->ignoreProprietaryAttributes && \str\startsWithIgnoreCase($match[5], 'proprietary attribute'))
          continue;

        $levelTidy=strtolower(trim($match[3]));

        $level=self::ERROR;
        if(isset(self::$m_severities[$levelTidy]))
          $level=self::$m_severities[$levelTidy];

        $errors[$level][(int)$match[1]][(int)$match[2]]=$match[5];
      }

      return $errors;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    /**
     * @see \Components\Runtime_Error_Handler::onError() onError
     */
    public function onError(\ErrorException $e_)
    {
      return true;
    }

    /**
     * @see \Components\Object::equals() equals
     */
    public function equals($object_)
    {
      if(null===$object_)
        return false;

      return $this===$object_;
    }

    /**
     * @see \Components\Object::hashCode() hashCode
     */
    public function hashCode()
    {
      return \math\hasho($this);
    }

    /**
     * @see \Components\Object::__toString() __toString
     */
    public function __toString()
    {
      return sprintf('%s@%s', __CLASS__, $this->hashCode());
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var string[]
     */
    private static $m_severities=[
      'info'=>self::INFO,
      'warning'=>self::WARN,
      'error'=>self::ERROR
    ];

    /**
     * @var string
     */
    private $m_html;
    /**
     * @var \Components\Io_Charset
     */
    private $m_charset;
    /**
     * @var \tidy
     */
    private $m_tidy;
    //--------------------------------------------------------------------------
  }


  /**
   * Text_Html_Tidy_Node
   *
   * @package net.evalcode.components.text
   * @subpackage html
   *
   * @author evalcode.net
   */
  class Text_Html_Tidy_Node
  {
    // CONSTRUCTION
    public function __construct(Text_Html_Tidy $tidy_, \tidyNode $node_)
    {
      $this->m_tidy=$tidy_;
      $this->m_node=$node_;
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    public function getTag()
    {
      return $this->m_node->name;
    }

    public function hasAttribute($name_)
    {
      return isset($this->m_node->attribute[$name_]);
    }

    public function getAttribute($name_)
    {
      return $this->m_node->attribute[$name_];
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var \Components\Text_Html_Tidy
     */
    private $m_tidy;
    /**
     * @var \tidyNode
     */
    private $m_node;
    //--------------------------------------------------------------------------
  }
?>
