<?php


  /**
   * Text_Html
   *
   * @package net.evalcode.components
   * @subpackage text
   *
   * @author evalcode.net
   */
  class Text_Html
  {
    // PROPERTIES
    public $width=40;
    public $linebreakCharacter="\n";
    public $indentationCharacter=" ";
    public $indentationSize=2;
    public $indentationDefault=1;
    //--------------------------------------------------------------------------


    // CONSTRUCTION
    public function __construct($html_, Io_Charset $charset_)
    {
      $this->m_html=$html_;
      $this->m_charset=$charset_;
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    /**
     * @return string
     */
    public function toPlainText(Io_Charset $outputCharset_=null)
    {
      if(null===$outputCharset_)
        $outputCharset_=$this->m_charset;

      $html=html_entity_decode($this->m_html, ENT_QUOTES, $this->m_charset->name());

      $tidy=new tidy();
      $tidy->parseString($html, array(
        'char-encoding'=>$this->m_charset->name(),
        'input-encoding'=>$this->m_charset->name(),
        'output-encoding'=>$outputCharset_->name()
      ));

      $string='';
      $this->nodeToPlainText($tidy->body(), $string);

      return $string;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    protected $m_htmlNodeConverters=array(
      ''=>'nodeTextToPlainText',
      'a'=>'nodeAnchorToPlainText',
      'br'=>'nodeLinebreakToPlainText',
      'h1'=>'nodeHeadline1ToPlainText',
      'h2'=>'nodeHeadline2ToPlainText',
      'h3'=>'nodeHeadline3ToPlainText',
      'h4'=>'nodeHeadline3ToPlainText',
      'h5'=>'nodeHeadline3ToPlainText',
      'p'=>'nodeParagraphToPlainText',
      'table'=>'nodeTableToPlainText',
      'tr'=>'nodeTableRowToPlainText'
    );

    /**
     * @var string
     */
    protected $m_html;
    /**
     * @var Io_Charset
     */
    protected $m_charset;
    //-----


    protected function nodeToPlainText(tidyNode $node_, &$output_)
    {
      if($node_->isComment())
        return;

      if(isset($this->m_htmlNodeConverters[$node_->name]))
      {
        $this->{$this->m_htmlNodeConverters[$node_->name]}($node_, $output_);
      }
      else if($node_->hasChildren())
      {
        foreach($node_->child as $node)
          $this->nodeToPlainText($node, $output_);
      }
    }

    protected function nodeAnchorToPlainText(tidyNode $node_, &$output_)
    {
      if(isset($node_->child) && is_array($node_->child))
      {
        $inner='';
        foreach($node_->child as $node)
          $this->nodeToPlainText($node, $inner);

        $href=$node_->attribute['href'];

        $output_.="$inner [$href]";
      }
    }

    protected function nodeHeadline1ToPlainText(tidyNode $node_, &$output_)
    {
      if(isset($node_->child) && is_array($node_->child))
      {
        $inner='';
        foreach($node_->child as $node)
          $this->nodeToPlainText($node, $inner);

        $output_.=$this->linebreakCharacter.$this->linebreakCharacter.$inner.$this->linebreakCharacter.str_repeat('=', $this->width).$this->linebreakCharacter;
      }
    }

    protected function nodeHeadline2ToPlainText(tidyNode $node_, &$output_)
    {
      if(isset($node_->child) && is_array($node_->child))
      {
        $inner='';
        foreach($node_->child as $node)
          $this->nodeToPlainText($node, $inner);

        $output_.=$this->linebreakCharacter.$inner.$this->linebreakCharacter.str_repeat('_', mb_strlen($inner)).$this->linebreakCharacter;
      }
    }

    protected function nodeHeadline3ToPlainText(tidyNode $node_, &$output_)
    {
      if(isset($node_->child) && is_array($node_->child))
      {
        $inner='';
        foreach($node_->child as $node)
          $this->nodeToPlainText($node, $inner);

        $output_.=$this->linebreakCharacter.$this->linebreakCharacter.$inner.$this->linebreakCharacter;
      }
    }

    protected function nodeLinebreakToPlainText(tidyNode $node_, &$output_)
    {
      $output_.=$this->linebreakCharacter;
    }

    protected function nodeParagraphToPlainText(tidyNode $node_, &$output_)
    {
      if(isset($node_->child) && is_array($node_->child))
      {
        $inner='';
        foreach($node_->child as $node)
          $this->nodeToPlainText($node, $inner);

        $output_.=$this->linebreakCharacter.$inner.$this->linebreakCharacter;
      }
    }

    protected function nodeTextToPlainText(tidyNode $node_, &$output_)
    {
      $output_.=$node_->value;
    }

    protected function nodeTableToPlainText(tidyNode $node_, &$output_)
    {
      $output_.=$this->linebreakCharacter;

      if(isset($node_->child) && is_array($node_->child))
      {
        foreach($node_->child as $node)
          $this->nodeToPlainText($node, $output_);
      }
    }

    protected function nodeTableRowToPlainText(tidyNode $node_, &$output_)
    {
      $columns=count($node_->child);
      $cellWidth=round($this->width/$columns, 0);

      $cells=array();
      if(isset($node_->child) && is_array($node_->child))
      {
        foreach($node_->child as $cell)
        {
          $inner='';
          $this->nodeToPlainText($cell, $inner);

          if(mb_ereg_replace('\s*', '', $inner))
            $cells[]=trim($inner);
        }
      }

      if(count($cells))
      {
        $output_.=$this->linebreakCharacter.
          vsprintf(str_repeat('%-'.$cellWidth.'s', count($cells)), $cells).
          $this->linebreakCharacter;
      }
    }
    //--------------------------------------------------------------------------
  }
?>
