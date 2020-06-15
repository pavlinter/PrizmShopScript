<?php

/**
 * Class PrizmApi
 */
class PrizmApi
{
    public $servlet_url;

    public $noda_url;

    public $sendkey;

    public $result;

    /**
     * PrizmApi constructor.
     * @param $servlet_url
     * @param $noda_url
     * @param null $sendkey
     */
    public function __construct($servlet_url, $noda_url, $sendkey = null)
    {
        $this->servlet_url = $servlet_url;
        $this->noda_url = $noda_url;
        $this->sendkey = $sendkey;
    }

    /**
     * @param int $last_id
     * @return array
     */
    public function servlet_history($last_id = 0)
    {
        if ($last_id) {
            $url = $this->servlet_url . '/history?fromid=' . $last_id;
        } else {
            $url = $this->servlet_url . '/history';
        }

        $page = '';
        $result = $this->request($url);

        if (!$this->hasError()) {
            $page = $result['content'];
        }

        $rows = [];
        $xcmorewrite = explode("\n", str_replace("\r", '', $page));

        foreach ($xcmorewrite as $value) {
            if ($value) {
                if ($value != "No transactions!") {
                    list($tr_id, $tr_date, $tr_timestamp, $tr_address, $tr_price, $tr_comment) = explode(";", $value);

                    if (!preg_match( '/^-?[0-9]+$/', $tr_id)) {

                        $error = trim(str_replace("\r", '', str_replace("\n", '', $result['content'])));

                        $continueList = ['Request timeout - PrizmCore is busy!'];
                        if (in_array($error, $continueList)) {
                            continue;
                        }

                        log_error('Transaction not number (' . $error . ')');
                        continue;
                    }

                    $data = [
                        'id' => $tr_id,
                        'date' => $tr_date, //Thu Apr 23 13:05:25 CEST 2020
                        'timestamp' => $tr_timestamp,
                        'from' => $tr_address,
                        'price' => $tr_price,
                        'comment' => $tr_comment,
                    ];
                    $rows[$tr_id] = $data;
                }
            }
        }
        return $rows;
    }

    /**
     * @return integer
     */
    public function getLastPzmHistory()
    {
        global $db;

        $stmt = $db->prepare('SELECT * FROM pzm_history ORDER BY id DESC LIMIT 1');
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return $row['tr_id'];
        }
        return 0;
    }

    /**
     * @param array|string $params
     * @return string
     */
    public function request_to_noda($params = [])
    {
        $baseUrl = $this->noda_url . '/prizm';
        $url = $baseUrl;
        if (is_array($params)) {
            $query = http_build_query($params);
            if ($params) {
                $url = $baseUrl . '?' . $query;
            }

        } else {
            $url = $baseUrl . $params;
        }

        return $this->request($url);
    }

    /**
     * @param $prizm_address
     * @return string
     * It’s only possible to get a public key from an activated wallet and only from an old wallet
     */
    public function getPublicKey($prizm_address)
    {
        $url = $this->servlet_url . '/publickey?destination=' . $prizm_address;
        $result = $this->request($url);
        if ($this->hasError()) {
            //$error = $result['errmsg'];
            return '';
        } else {
            $page = $result['content'];
            $haystack = "Public key absent";
            $haystack2 = "Send error!";
            $pos = strripos($page, $haystack);
            $pos2 = strripos($page, $haystack2);
            if ($pos === false AND $pos2 === false) {
                $xcmorewrite = explode(' ', $page);
                $page = trim($xcmorewrite[0]);
                return $page;
            } else {
                return '';
            }
        }
        return '';
    }


    /**
     * @param $amount (min 0.01 pzm)
     * @param $prizm_address
     * @param $public_key
     * @param $comment
     * @return bool ()
     */
    public function payPZM($amount, $prizm_address, $public_key, $comment)
    {

        $url = $this->servlet_url . '/send?sendkey=' . $this->sendkey . '&amount=' . $amount . '&comment=' . urlencode($comment) . '&destination=' . $prizm_address . '&publickey=' . $public_key;
        $transaction_id = '';
        $result = $this->request($url);

        if ($this->hasError()) {
            //$error = $result['errmsg'];
        } else {
            $transaction_id = $result['content'];
        }

        if (preg_match('/^\+?\d+$/', $transaction_id)) {
            return (int)$transaction_id;
        } else {
            return $result;
        }
    }

    /**
     * @param $prizm_address
     * @return float|int|null
     */
    public function getBalancePZM($prizm_address)
    {

        $result = $this->request_to_noda([
            'requestType' => 'getAccount',
            'account' => $prizm_address,
        ]);

        if ($this->hasError()) {
            return null;
        } else {
            $data = jsonDecode($result['content']);
            if ( isset($data['balanceNQT']) ) {
                return $data['balanceNQT'] / 100;
            }
        }
        return 0;
    }


    /**
     * @param $url
     * @return mixed
     */
    public function request($url)
    {
        $this->resetResult();

        $uagent = "Opera/9.80 (Windows NT 6.1; WOW64) Presto/2.12.388 Version/12.14";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_USERAGENT, $uagent);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 2);

        // Do not check the SSL certificates
        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $content = curl_exec($ch);
        $err = curl_errno($ch);
        $errmsg = curl_error($ch);
        $header = curl_getinfo($ch);
        curl_close($ch);

        $header['errno'] = $err;
        $header['errmsg'] = $errmsg;
        $header['content'] = $content;
        $header['has_error'] = false;

        if (($err != 0) || ($header['http_code'] != 200)) {
            $header['has_error'] = true;
        }

        $this->result = $header;

        return $header;
    }

    public function resetResult()
    {
        $this->result = null;
    }

    /**
     * @return bool
     */
    public function hasError()
    {
        if (isset($this->result['has_error'])) {
            return $this->result['has_error'];
        }
        return true;
    }
}
