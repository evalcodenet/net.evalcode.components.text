<?php


namespace Components;


  /**
   * Text_Html_Tidy
   *
   * @package net.evalcode.components
   * @subpackage text.html
   *
   * @author evalcode.net
   */
  class Text_Html_Tidy
  {
    // PROPERTIES
    public $config=array(
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
    );
    //--------------------------------------------------------------------------


    // CONSTRUCTION
    public function __construct($html_, Io_Charset $charset_)
    {
      $this->m_tidy=new \tidy();

      //$this->m_html=$html_;
      $this->m_html=file_get_contents(__DIR__.'/test.html');
      $this->m_charset=$charset_;
    }
    //--------------------------------------------------------------------------


    // ACCESSORS/MUTATORS
    public function parse()
    {
      $charset=strtolower(str_replace(array('-', '_'), null, $this->m_charset->name()));

      $config=$this->config;
      $config['char-encoding']=$charset;

      $this->m_tidy->parseString($this->m_html, $config, $charset);
    }

    public function getErrors()
    {
      $this->m_tidy->diagnose();
      $errors=$this->m_tidy->errorBuffer;

      $matches=array();
      preg_match_all('/^(?:line (\d+) column (\d+) - )?(\S+): (?:\[((?:\d+\.?){4})]:)?(.*?)$/m', $errors, $matches, PREG_SET_ORDER);

      var_dump($matches);
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var string
     */
    private $m_html;
    /**
     * @var Io_Charset
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
   * @package net.evalcode.components
   * @subpackage text.html
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


    // ACCESSORS/MUTATORS
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
     * @var Text_Html_Tidy
     */
    private $m_tidy;
    /**
     * @var \tidyNode
     */
    private $m_node;
    //--------------------------------------------------------------------------
  }
?>
