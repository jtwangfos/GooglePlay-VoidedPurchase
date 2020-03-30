<?php
/**
 * Created by PhpStorm.
 * User: wangjiatong
 * Date: 2020/3/3
 * Time: 14:27
 */

class Handler {
    protected $packageName;
    protected $jsonSecret;
    protected $jsonSecretPath;

    protected $googleClient;
    protected $googleService;

    public function __construct($package_name, $json_secret, $json_secret_path) {
        $this->packageName = $package_name;
        $this->jsonSecretPath = $json_secret_path;
        $this->jsonSecret = $this->getJsonSecret($json_secret);

        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $this->jsonSecret);
        $this->googleClient = new Google_Client();
        $this->googleClient->useApplicationDefaultCredentials();
        $this->googleClient->addScope(Google_Service_AndroidPublisher::ANDROIDPUBLISHER);

        $this->googleService = new Google_Service_AndroidPublisher($this->googleClient);
    }

    public function process($optParams = [], callable $callback) {
        $ops = [];
        if (isset($optParams['endTime'])) {
            $ops['endTime'] = $optParams['endTime'];
        }
        if (isset($optParams['maxResults'])) {
            $ops['maxResults'] = $optParams['maxResults'];
        }
        if (isset($optParams['startIndex'])) {
            $ops['startIndex'] = $optParams['startIndex'];
        }
        if (isset($optParams['startTime'])) {
            $ops['startTime'] = $optParams['startTime'];
        }
        if (isset($optParams['token'])) {
            $ops['token'] = $optParams['token'];
        }
        if (isset($optParams['type'])) {
            $ops['type'] = $optParams['type'];
        }

        $list = $this->googleService->purchases_voidedpurchases->listPurchasesVoidedpurchases($this->packageName, $ops);
        $voidedPurchases = $list->getVoidedPurchases();
        if (empty($voidedPurchases)) {
            return false;
        }

        foreach ($voidedPurchases as $voidedPurchase) {
            $callback($voidedPurchase);
        }

        if ($pagination = $list->getTokenPagination()) {
            $ops['token'] = $pagination->getNextPageToken();
            $this->process($ops, $callback);
        }

        return true;
    }

    protected function getJsonSecret($json_secret) {
        $ext = pathinfo($json_secret)['extension'];
        if ($ext !== 'json') {
            throw new Exception(__CLASS__ . '::$jsonSecret must be json file');
        }

        if (substr($this->jsonSecretPath, -1) !== '/') {
            $this->jsonSecretPath .= '/';
        }

        return $this->jsonSecretPath . $json_secret;
    }

}