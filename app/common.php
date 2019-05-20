<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
function genToken( $len = 32, $md5 = true ) {
       # Seed random number generator
          # Only needed for PHP versions prior to 4.2
          mt_srand( (double)microtime()*1000000 );
          # Array of characters, adjust as desired
          $chars = array(
              'Q', '@', '8', 'y', '%', '^', '5', 'Z', '(', 'G', '_', 'O', '`',
              'S', '-', 'N', '<', 'D', '{', '}', '[', ']', 'h', ';', 'W', '.',
              '/', '|', ':', '1', 'E', 'L', '4', '&', '6', '7', '#', '9', 'a',
              'A', 'b', 'B', '~', 'C', 'd', '>', 'e', '2', 'f', 'P', 'g', ')',
              '?', 'H', 'i', 'X', 'U', 'J', 'k', 'r', 'l', '3', 't', 'M', 'n',
              '=', 'o', '+', 'p', 'F', 'q', '!', 'K', 'R', 's', 'c', 'm', 'T',
              'v', 'j', 'u', 'V', 'w', ',', 'x', 'I', '$', 'Y', 'z', '*'
          );
          # Array indice friendly number of chars;
          $numChars = count($chars) - 1; $token = '';
          # Create random token at the specified length
          for ( $i=0; $i<$len; $i++ )
              $token .= $chars[ mt_rand(0, $numChars) ];
          # Should token be run through md5?
          if ( $md5 ) {
              # Number of 32 char chunks
              $chunks = ceil( strlen($token) / 32 ); $md5token = '';
              # Run each chunk through md5
              for ( $i=1; $i<=$chunks; $i++ )
                  $md5token .= md5( substr($token, $i * 32 - 32, 32) );
              # Trim the token
              $token = substr($md5token, 0, $len);
          }

          return $token;
      }

/**
     * 获取随机字符串
     * @param number $length 长度
     * @param string $type 类型
     * @param number $convert 转换大小写
     * @return string 随机字符串
     */
    function random($length = 6, $type = 'string', $convert = 0)
    {
        $config = array(
            'number' => '1234567890',
            'letter' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'string' => 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789',
            'all' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'
        );

        if (!isset($config[$type]))
            $type = 'string';
        $string = $config[$type];

        $code = '';
        $strlen = strlen($string) - 1;
        for ($i = 0; $i < $length; $i++) {
            $code .= $string{mt_rand(0, $strlen)};
        }
        if (!empty($convert)) {
            $code = ($convert > 0) ? strtoupper($code) : strtolower($code);
        }
        return $code;
    }
