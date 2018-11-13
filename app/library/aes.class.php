<?php

/**
 * 说明:
 *
 * User: 胡熔
 * Date: 2018/11/5
 * Time: 15:43
 */
class Aes
{
    /**
     * var string $method 加解密方法，可通过openssl_get_cipher_methods()获得,可选值如下：
     *
     * Array
     * (
     * [0] => AES-128-CBC
     * [1] => AES-128-CBC-HMAC-SHA1
     * [2] => AES-128-CBC-HMAC-SHA256
     * [3] => AES-128-CFB
     * [4] => AES-128-CFB1
     * [5] => AES-128-CFB8
     * [6] => AES-128-CTR
     * [7] => AES-128-ECB
     * [8] => AES-128-OCB
     * [9] => AES-128-OFB
     * [10] => AES-128-XTS
     * [11] => AES-192-CBC
     * [12] => AES-192-CFB
     * [13] => AES-192-CFB1
     * [14] => AES-192-CFB8
     * [15] => AES-192-CTR
     * [16] => AES-192-ECB
     * [17] => AES-192-OCB
     * [18] => AES-192-OFB
     * [19] => AES-256-CBC
     * [20] => AES-256-CBC-HMAC-SHA1
     * [21] => AES-256-CBC-HMAC-SHA256
     * [22] => AES-256-CFB
     * [23] => AES-256-CFB1
     * [24] => AES-256-CFB8
     * [25] => AES-256-CTR
     * [26] => AES-256-ECB
     * [27] => AES-256-OCB
     * [28] => AES-256-OFB
     * [29] => AES-256-XTS
     * [30] => BF-CBC
     * [31] => BF-CFB
     * [32] => BF-ECB
     * [33] => BF-OFB
     * [34] => CAMELLIA-128-CBC
     * [35] => CAMELLIA-128-CFB
     * [36] => CAMELLIA-128-CFB1
     * [37] => CAMELLIA-128-CFB8
     * [38] => CAMELLIA-128-CTR
     * [39] => CAMELLIA-128-ECB
     * [40] => CAMELLIA-128-OFB
     * [41] => CAMELLIA-192-CBC
     * [42] => CAMELLIA-192-CFB
     * [43] => CAMELLIA-192-CFB1
     * [44] => CAMELLIA-192-CFB8
     * [45] => CAMELLIA-192-CTR
     * [46] => CAMELLIA-192-ECB
     * [47] => CAMELLIA-192-OFB
     * [48] => CAMELLIA-256-CBC
     * [49] => CAMELLIA-256-CFB
     * [50] => CAMELLIA-256-CFB1
     * [51] => CAMELLIA-256-CFB8
     * [52] => CAMELLIA-256-CTR
     * [53] => CAMELLIA-256-ECB
     * [54] => CAMELLIA-256-OFB
     * [55] => CAST5-CBC
     * [56] => CAST5-CFB
     * [57] => CAST5-ECB
     * [58] => CAST5-OFB
     * [59] => ChaCha20
     * [60] => ChaCha20-Poly1305
     * [61] => DES-CBC
     * [62] => DES-CFB
     * [63] => DES-CFB1
     * [64] => DES-CFB8
     * [65] => DES-ECB
     * [66] => DES-EDE
     * [67] => DES-EDE-CBC
     * [68] => DES-EDE-CFB
     * [69] => DES-EDE-OFB
     * [70] => DES-EDE3
     * [71] => DES-EDE3-CBC
     * [72] => DES-EDE3-CFB
     * [73] => DES-EDE3-CFB1
     * [74] => DES-EDE3-CFB8
     * [75] => DES-EDE3-OFB
     * [76] => DES-OFB
     * [77] => DESX-CBC
     * [78] => IDEA-CBC
     * [79] => IDEA-CFB
     * [80] => IDEA-ECB
     * [81] => IDEA-OFB
     * [82] => RC2-40-CBC
     * [83] => RC2-64-CBC
     * [84] => RC2-CBC
     * [85] => RC2-CFB
     * [86] => RC2-ECB
     * [87] => RC2-OFB
     * [88] => RC4
     * [89] => RC4-40
     * [90] => RC4-HMAC-MD5
     * [91] => SEED-CBC
     * [92] => SEED-CFB
     * [93] => SEED-ECB
     * [94] => SEED-OFB
     * [95] => aes-128-cbc
     * [96] => aes-128-cbc-hmac-sha1
     * [97] => aes-128-cbc-hmac-sha256
     * [98] => aes-128-ccm
     * [99] => aes-128-cfb
     * [100] => aes-128-cfb1
     * [101] => aes-128-cfb8
     * [102] => aes-128-ctr
     * [103] => aes-128-ecb
     * [104] => aes-128-gcm
     * [105] => aes-128-ocb
     * [106] => aes-128-ofb
     * [107] => aes-128-xts
     * [108] => aes-192-cbc
     * [109] => aes-192-ccm
     * [110] => aes-192-cfb
     * [111] => aes-192-cfb1
     * [112] => aes-192-cfb8
     * [113] => aes-192-ctr
     * [114] => aes-192-ecb
     * [115] => aes-192-gcm
     * [116] => aes-192-ocb
     * [117] => aes-192-ofb
     * [118] => aes-256-cbc
     * [119] => aes-256-cbc-hmac-sha1
     * [120] => aes-256-cbc-hmac-sha256
     * [121] => aes-256-ccm
     * [122] => aes-256-cfb
     * [123] => aes-256-cfb1
     * [124] => aes-256-cfb8
     * [125] => aes-256-ctr
     * [126] => aes-256-ecb
     * [127] => aes-256-gcm
     * [128] => aes-256-ocb
     * [129] => aes-256-ofb
     * [130] => aes-256-xts
     * [131] => bf-cbc
     * [132] => bf-cfb
     * [133] => bf-ecb
     * [134] => bf-ofb
     * [135] => camellia-128-cbc
     * [136] => camellia-128-cfb
     * [137] => camellia-128-cfb1
     * [138] => camellia-128-cfb8
     * [139] => camellia-128-ctr
     * [140] => camellia-128-ecb
     * [141] => camellia-128-ofb
     * [142] => camellia-192-cbc
     * [143] => camellia-192-cfb
     * [144] => camellia-192-cfb1
     * [145] => camellia-192-cfb8
     * [146] => camellia-192-ctr
     * [147] => camellia-192-ecb
     * [148] => camellia-192-ofb
     * [149] => camellia-256-cbc
     * [150] => camellia-256-cfb
     * [151] => camellia-256-cfb1
     * [152] => camellia-256-cfb8
     * [153] => camellia-256-ctr
     * [154] => camellia-256-ecb
     * [155] => camellia-256-ofb
     * [156] => cast5-cbc
     * [157] => cast5-cfb
     * [158] => cast5-ecb
     * [159] => cast5-ofb
     * [160] => chacha20
     * [161] => chacha20-poly1305
     * [162] => des-cbc
     * [163] => des-cfb
     * [164] => des-cfb1
     * [165] => des-cfb8
     * [166] => des-ecb
     * [167] => des-ede
     * [168] => des-ede-cbc
     * [169] => des-ede-cfb
     * [170] => des-ede-ofb
     * [171] => des-ede3
     * [172] => des-ede3-cbc
     * [173] => des-ede3-cfb
     * [174] => des-ede3-cfb1
     * [175] => des-ede3-cfb8
     * [176] => des-ede3-ofb
     * [177] => des-ofb
     * [178] => desx-cbc
     * [179] => id-aes128-CCM
     * [180] => id-aes128-GCM
     * [181] => id-aes128-wrap
     * [182] => id-aes128-wrap-pad
     * [183] => id-aes192-CCM
     * [184] => id-aes192-GCM
     * [185] => id-aes192-wrap
     * [186] => id-aes192-wrap-pad
     * [187] => id-aes256-CCM
     * [188] => id-aes256-GCM
     * [189] => id-aes256-wrap
     * [190] => id-aes256-wrap-pad
     * [191] => id-smime-alg-CMS3DESwrap
     * [192] => idea-cbc
     * [193] => idea-cfb
     * [194] => idea-ecb
     * [195] => idea-ofb
     * [196] => rc2-40-cbc
     * [197] => rc2-64-cbc
     * [198] => rc2-cbc
     * [199] => rc2-cfb
     * [200] => rc2-ecb
     * [201] => rc2-ofb
     * [202] => rc4
     * [203] => rc4-40
     * [204] => rc4-hmac-md5
     * [205] => seed-cbc
     * [206] => seed-cfb
     * [207] => seed-ecb
     * [208] => seed-ofb
     * )
     */
    protected $method;

    /**
     * var string $secret_key 加解密的密钥
     */
    protected $secret_key;

    /**
     * var string $iv 加解密的向量，有些方法需要设置比如CBC
     */
    protected $iv;

    /**
     * 构造函数
     *
     * @param string $key 密钥
     * @param string $iv iv非 NULL 的初始化向量
     * @param string $method 加密方式
     * @param mixed $options 文档上解释是：options 是以下标记的按位或： OPENSSL_RAW_DATA 、 OPENSSL_ZERO_PADDING。默认为0
     *
     */
    public function __construct($key, $iv = '', $method = 'AES-128-ECB', $options = 0)
    {
        // key是必须要设置的
        $this->secret_key = isset($key) ? $key : 'htTp://www.yunInDex.com@4806';

        $this->method = $method;

        $this->iv = $iv;

        $this->options = $options;
    }

    /**
     * var string $options （不知道怎么解释，目前设置为0没什么问题）
     */
    protected $options;

    /**
     * 加密方法，对数据进行加密，返回加密后的数据
     *
     * @param string $data 要加密的数据
     *
     * @return string
     *
     */
    public function encrypt($data)
    {
        return openssl_encrypt($data, $this->method, $this->secret_key, $this->options, $this->iv);
    }

    /**
     * 解密方法，对数据进行解密，返回解密后的数据
     *
     * @param string $data 要解密的数据
     *
     * @return string
     *
     */
    public function decrypt($data)
    {
        return openssl_decrypt($data, $this->method, $this->secret_key, $this->options, $this->iv);
    }
}