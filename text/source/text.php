<?php


namespace Components;


  /**
   * Misc_Text
   *
   * @package tncMiscPlugin
   * @subpackage lib
   *
   * @author evalcode.net
   */
  final class Misc_Text
  {
    // STATIC ACCESSORS
    /**
     * Removes leading & trailing non-visible characters as e.g. spaces and line breaks.
     *
     * @param string $string_
     *
     * @return string
     */
    public static function trim($string_)
    {
      $words=array();
      foreach(mb_split('\s', $string_) as $word)
      {
        if(trim((string)$word))
          $words[]=$word;
      }

      return implode(' ', $words);
    }

    /**
     * Returns substring of given start position and/or length.
     *
     * @param string $string_
     * @param int $start_
     * @param int $length_
     *
     * @return string
     */
    public static function sub($string_, $start_=null, $length_=null)
    {
      $start=null===$start_?0:(int)$start_;
      $length=null===$length_?self::len($string_):min(self::len($string_), (int)$length_);

      return mb_substr($string_, $start, $length);
    }

    /**
     * Extends given string to given length with padstring.
     *
     * @param string $string_
     * @param int $length_
     * @param string $padString_
     *
     * @return string
     */
    public static function pad($string_, $length_, $padString_='...')
    {
      if(1>((int)$length_-self::len($string_)))
        return $string_;

      return $string_.self::sub(str_repeat($padString_, $length_-self::len($string_)), 0, $length_-self::len($string_));
    }

    /**
     * Returns string length.
     *
     * @param string $string_
     *
     * @return int
     */
    public static function len($string_)
    {
      return mb_strlen($string_);
    }

    /**
     * Returns first position of match string inside of string.
     *
     * @param string $string_
     * @param string $match_
     *
     * @return int
     */
    public static function pos($string_, $match_)
    {
      return mb_strpos($string_, $match_);
    }

    /**
     * Returns last position of match string inside of string.
     *
     * @param string $string_
     * @param string $match_
     *
     * @return int
     */
    public static function posr($string_, $match_)
    {
      return mb_strrpos($string_, $match_);
    }

    /**
     * Returns first position of match string inside of string (case insensitive).
     *
     * @param string $string_
     * @param string $match_
     *
     * @return int
     */
    public static function posci($string_, $match_)
    {
      return mb_stripos($string_, $match_);
    }

    /**
     * Returns last position of match string inside of string (case insensitive).
     *
     * @param string $string_
     * @param string $match_
     *
     * @return int
     */
    public static function posrci($string_, $match_)
    {
      return mb_strripos($string_, $match_);
    }

    /**
     * Uppercase first letter.
     *
     * @param string $string_
     *
     * @return string
     */
    public static function ucfirst($string_)
    {
      return self::uppercase(self::sub($string_, 0, 1)).self::sub($string_, 1);
    }

    /**
     * Lowercase first letter.
     *
     * @param string $string_
     *
     * @return string
     */
    public static function lcfirst($string_)
    {
      return self::lowercase(self::sub($string_, 0, 1)).self::sub($string_, 1);
    }

    /**
     * Uppercase string.
     *
     * @param string $string_
     *
     * @return string
     */
    public static function uppercase($string_)
    {
      return mb_strtoupper($string_);
    }

    /**
     * Lowercase string.
     *
     * @param string $string_
     *
     * @return string
     */
    public static function lowercase($string_)
    {
      return mb_strtolower($string_);
    }


    /**
     * Wraps string best possible for given line length/width.
     *
     * @param string $string_
     * @param int $width_
     * @param string $break_
     * @param bool $cut_
     *
     * @return array
     */
    public static function wrap($string_, $lineLength_=80, $lineBreak_="\n", $linePrefix_='')
    {
      return implode($lineBreak_, self::splitLines($string_, $lineLength_, $lineBreak_, $linePrefix_));
    }

    /**
     * Splits string into lines of given length and returns them as an array.
     *
     * @param string $string_
     * @param int $width_
     * @param string $break_
     * @param bool $cut_
     *
     * @return array
     */
    public static function splitLines($string_, $lineLength_=80, $lineBreak_="\n", $linePrefix_='')
    {
      $lines=array();

      $length=mb_strlen($linePrefix_);
      $line=array();

      foreach(explode($lineBreak_, $string_) as $string)
      {
        $words=mb_split('\s', $string);

        foreach($words as $key=>$word)
        {
          $length+=mb_strlen($word)+1;
          $line[]=$word;

          if($length>=$lineLength_
          || (isset($words[$key+1]) && (($length+mb_strlen($words[$key+1]))>=$lineLength_)))
          {
            $lines[]=$linePrefix_.implode(' ', $line);
            $line=array();
            $length=mb_strlen($linePrefix_);
          }
        }

        $lines[]=$linePrefix_.implode(' ', $line);
        $line=array();
        $length=mb_strlen($linePrefix_);
      }

      return $lines;
    }

    /**
     * Returns 'true' if given strings are equal.
     *
     * @param string $stringA_
     * @param string $stringB_
     *
     * @return bool
     */
    public static function equals($stringA_, $stringB_)
    {
      return 0===strnatcmp($stringA_, $stringB_);
    }

    /**
     * Returns 0 if given strings are equal, otherwise a negative/positive number
     * if the first string is smaller/greater than the second one.
     *
     * @param string $stringA_
     * @param string $stringB_
     *
     * @return int
     */
    public static function compare($stringA_, $stringB_)
    {
      return strnatcmp($stringA_, $stringB_);
    }

    /**
     * Returns 0 if given strings are equal, otherwise a negative/positive number
     * if the first string is smaller/greater than the second one.
     *
     * @param string $stringA_
     * @param string $stringB_
     *
     * @return bool
     */
    public static function compareci($stringA_, $stringB_)
    {
      static $stringTable=array('ä'=>'a', 'ö'=>'o', 'ü'=>'u', 'ß'=>'s');
      $stringA=strtr(self::lowercase($stringA_, 'UTF-8'), $stringTable);
      $stringB=strtr(self::lowercase($stringB_, 'UTF-8'), $stringTable);

      return self::compare($stringA, $stringB);
    }

    /**
     * Replace given pattern in string.
     *
     * If the optional limit parameter is passed, only as much occurences
     * will be replaced.
     *
     * @param string $string_
     * @param string $match_
     * @param string $replace_
     * @param int $limit_
     *
     * @return string
     */
    public static function replace($string_, $match_, $replace_, $limit_=0)
    {
      if(!$limit_)
        return mb_ereg_replace($match_, $replace_, $string_);

      $result='';
      $tail=$string_;
      $searchStrLen=mb_strlen($match_, 'UTF-8');

      $i=0;
      while($limit_>$i && false!==($part=mb_strstr($tail, $match_, true, 'UTF-8')))
      {
        $result.=$part.$replace_;
        $tail=mb_substr($tail, mb_strlen($part)+$searchStrLen);
        $i++;
      }

      return $result.$tail;
    }


    /**
     * Replace all given patterns in string.
     *
     * @param string $string_
     * @param array $match_
     * @param array $replace_
     *
     * @return string
     */
    public static function replaceAll($string_, array $match_, array $replace_)
    {
      $count=min(count($match_), count($replace_))-1;
      for($i=0; $i<=$count; $i++)
        $string_=self::replace($string_, $match_[$i], $replace_[$i]);

      return $string_;
    }

    /**
     * Converts string to ASCII.
     *
     * @param string $string_
     *
     * @return string
     */
    public static function toASCII($string_)
    {
      static $s_table=array(
        'ä'=>'ae', 'ö'=>'oe', 'ü'=>'ue', 'Ä'=>'Ae', 'Ö'=>'Oe', 'Ü'=>'Ue',
        'ß'=>'ss', 'æ'=>'ae', 'œ'=>'oe', 'Æ'=>'Ae', 'Œ'=>'Oe', 'à'=>'a',
        'á'=>'a', 'â'=>'a', 'å'=>'a', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e',
        'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ò'=>'o','ó'=>'o', 'ô'=>'o',
        'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ÿ'=>'y', 'ç'=>'c', 'š'=>'s', 'À'=>'A',
        'Á'=>'A', 'Â'=>'A', 'Å'=>'A', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'ë'=>'E',
        'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ò'=>'O','Ó'=>'O', 'Ô'=>'O', 'Ù'=>'U',
        'Ú'=>'U', 'Û'=>'U', 'Ç'=>'C', 'Š'=>'S'
      );
      return strtr($string_, $s_table);
    }

    /**
     * Checks for ASCII string.
     *
     * @param string $string_
     *
     * @return bool
     */
    public static function isASCII($string_)
    {
      return 1!==preg_match('/[^\x00-\x7F]/', $string_);
    }

    /**
     * Checks for LATIN-1 string.
     *
     * @param string $string_
     *
     * @return bool
     */
    public static function isLatin1($string_)
    {
      $len=mb_strlen($string_);

      for($i=0; $i<$len; ++$i)
      {
        $ord=ord($string_[$i]);

        // ASCII?
        if($ord>=0 && $ord<=127)
          continue;

        // 2 byte sequence?
        if($ord>=192 && $ord<=223)
        {
          $ord=($ord-192)*64+ord($string_[++$i])-128;

          // LATIN-1?
          if($ord<=0xFF)
            continue;
        }

        return false;
      }

      return true;
    }

    public static function isZero($string_)
    {
      return 1===preg_match('/^[0]+$/', (string)$string_);
    }

    public static function isInteger($string_)
    {
      return 1===preg_match('/^[+-]?[0-9]+$/', (string)$string_);
    }

    public static function isNullOrEmpty($string_)
    {
      return null===$string_ || (!trim($string_) && !self::isZero($string_));
    }


    /**
     * Converts underscore names to camelcase.
     *
     * @param string $string_
     *
     * @return string
     */
    public static function underscoreToCamelCase($string_)
    {
      $camelcase=ucwords(strtr(self::trim($string_), '_', ' '));
      $camelcase[0]=self::lowercase($camelcase[0]);

      return self::replace($camelcase, ' ', '');
    }

    /**
     * Converts underscore names to namespaces.
     *
     * @param string $string_
     *
     * @return string
     */
    public static function underscoreToNamespace($string_)
    {
      return self::lowercase(self::replace($string_, '_', '/'));
    }

    /**
     * Converts camelcase names to underscore.
     *
     * @param string $string_
     *
     * @return string
     */
    public static function camelCaseToUnderscore($string_)
    {
      static $stringTable=array(
        'A'=>'_a', 'B'=>'_b', 'C'=>'_c', 'D'=>'_d', 'E'=>'_e', 'F'=>'_f', 'G'=>'_g', 'H'=>'_h',
        'I'=>'_i', 'J'=>'_j', 'K'=>'_k', 'L'=>'_l', 'M'=>'_m', 'N'=>'_n', 'O'=>'_o', 'P'=>'_p',
        'Q'=>'_q', 'R'=>'_r', 'S'=>'_s', 'T'=>'_t', 'U'=>'_u', 'V'=>'_v', 'W'=>'_w', 'X'=>'_x',
        'Y'=>'_y', 'Z'=>'_z'
      );

      return strtr(self::trim($string_), $stringTable);
    }

    /**
     * Checks for camelcase name.
     *
     * @param string $string_
     *
     * @return bool
     */
    public static function isCamelCase($string_)
    {
      return 1==preg_match('/^[a-z][a-zA-Z0-9]*$/', $string_);
    }

    /**
     * Converts to lowercase url friendly string.
     *
     * @param string $string_
     *
     * @return string
     */
    public static function toLowercaseUrlIdentifier($string_, $preserveUnicode_=false)
    {
      $string_=strtolower(Text_UTF8::toASCII($string_));
      if($preserveUnicode_)
        $string_=mb_convert_encoding($string_, 'HTML-ENTITIES', 'UTF-8');

      $string_=preg_replace('/[^a-z0-9]/', '-', $string_);

      return preg_replace('/-+/', '-', $string_);
    }

    /**
     * Checks for lowercase url friendly string.
     *
     * @param string $string_
     *
     * @return bool
     */
    public static function isLowercaseIdentifier($string_)
    {
      return 1==preg_match('/^[a-z][a-z0-9_]*$/', $string_);
    }

    /**
     * Escapes string for use with javascript.
     *
     * @param string $string_
     *
     * @return string
     */
    public static function escapeJs($string_)
    {
      static $match=array("/\\\\/", "/\n/", "/\r/", "/\"/", "/\'/", "/&/", "/</", "/>/");
      static $replace=array("\\\\\\\\", "\\n", "\\r", "\\\"", "\\'", "\\x26", "\\x3C", "\\x3E");

      return self::replaceAll($string_, $match, $replace);
    }

    /**
     * Escapes string.
     *
     * @param string $string_
     *
     * @return string
     */
    public static function escapeHtml($string_)
    {
      return htmlspecialchars($string_, ENT_COMPAT, 'UTF-8');
    }

    /**
     * Encodes to base64.
     *
     * @param string $string_
     *
     * @return string
     */
    public static function toBase64($string_)
    {
      return base64_encode($string_);
    }

    /**
     * Decodes from base64.
     *
     * @param string $string_
     *
     * @return string
     */
    public static function fromBase64($string_)
    {
      return base64_decode($string_);
    }

    /**
     * Encodes to url-friendly base64.
     *
     * @param string $string_
     *
     * @return string
     */
    public static function toBase64Url($string_)
    {
      return self::replaceAll(self::toBase64($string_), array('+', '/'), array('-', '_'));
    }

    /**
     * Decodes from url-friendly base64.
     *
     * @param string $string_
     *
     * @return string
     */
    public static function fromBase64Url($string_)
    {
      return self::fromBase64(self::replaceAll($string_, array('-', '_'), array('+', '/')));
    }

    /**
     * Encodes to quoted printable.
     *
     * @param string $string_
     *
     * @return string
     */
    public static function toQuotedPrintable($string_)
    {
      return preg_replace_callback('/[^\x21-\x3C\x3E-\x7E\x09]/', array('Text', 'toQuotedPrintableImpl'), $string_);
    }

    /**
     * Decodes from quoted printable.
     *
     * @param string $string_
     *
     * @return string
     */
    public static function fromQuotedPrintable($string_)
    {
      return quoted_printable_decode($string_);
    }

    /**
     * Encodes to url-friendly string.
     *
     * @param string $string_
     * @param boolean $avoidDoubleEncoding_ Only encodes if given string is not already encoded.
     *
     * @return string
     */
    public static function urlEncode($string_, $avoidDoubleEncoding_=true)
    {
      if(false===$avoidDoubleEncoding_ || false===self::isUrlEncoded($string_))
        return rawurlencode($string_);

      return $string_;
    }

    /**
     * Decodes back from url-friendly string.
     *
     * @param string $string_
     * @param boolean $avoidDoubleDecoding_ Only decodes if given string is url encoded.
     *
     * @return string
     */
    public static function urlDecode($string_, $avoidDoubleDecoding_=true)
    {
      if(false===$avoidDoubleDecoding_ || self::isUrlEncoded($string_))
        return rawurldecode($string_);

      return $string_;
    }

    /**
     * Checks if given string is encoded by rawurlencode.
     *
     * @param string $string_
     *
     * @return boolean
     */
    public static function isUrlEncoded($string_)
    {
      static $m_urlEncoded=array(
        '%20', '%21', '%2A', '%27',
        '%28', '%29', '%3B', '%3A',
        '%40', '%26', '%3D', '%2B',
        '%24', '%2C', '%2F', '%3F',
        '%25', '%23', '%5B', '%5D'
      );
      static $m_urlDecoded=array(
        ' ', '!', '*', "'",
        "(", ")", ";", ":",
        "@", "&", "=", "+",
        "$", ",", "/", "?",
        "%", "#", "[", "]"
      );

      $count=0;
      str_replace($m_urlEncoded, $m_urlDecoded, $string_, $count);

      return 0<$count;
    }

    public static function generatePassword($length_=8, $complexity_=3)
    {
      if($length_>25 || $length_<6)
      {
        throw new Core_Exception_NotSupported('misc/text',
          'Password of a length less than 6 or more than 25 are not supported.'
        );
      }

      static $maxValues=array(1=>60, 2=>80, 3=>90, 4=>100);

      $password='';
      for($i=0; $i<$length_; $i++)
      {
        $value=rand(1, $maxValues[$complexity_]);
        // alphanumeric lowercase
        if($value>0 && $value<=$maxValues[1])
          $password.=chr(rand(97, 122));
        // alphanumeric uppercase
        if($value>$maxValues[1] && $value<=$maxValues[2])
          $password.=chr(rand(65, 90));
        // numeric
        if($value>$maxValues[2] && $value<=$maxValues[3])
          $password.=chr(rand(48, 57));
        // peculiar
        if($value>$maxValues[3] && $value<=$maxValues[4])
          $password.=chr(rand(35, 38));
      }

      return $password;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * Translates given character to quoted printable.
     *
     * @param string $match_
     *
     * @return string
     *
     * @internal
     * @see toQuotedPrintable
     */
    /*private*/ static function toQuotedPrintableImpl($string_)
    {
      return sprintf('=%02X', ord($string_[0]));
    }
    //--------------------------------------------------------------------------
  }
?>