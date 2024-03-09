<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class S3 {
    private $s3;
    private $bucket_name;

    public function __construct() {
        $this->CI =& get_instance();

        /** 
         * s3 config file load
         */
        $this->CI->load->config('s3');

        /**
         * Load Config Items Values 
         */
        $secretKey = $this->CI->config->item('bucket_secret_key');
        $accessKey = $this->CI->config->item('bucket_access_key');
        $endpoint  = $this->CI->config->item('bucket_endpoints');
        $this->bucket_name  = $this->CI->config->item('bucket_name');

        $this->s3 = new S3Client([
            'version'     => 'latest',
            'region'      => 'id-jkt-1',
            'credentials' => [
                'key'    => $accessKey,
                'secret' => $secretKey,
            ],
            'endpoint' => $endpoint,
        ]);
    }

    public function singleUpload($file_Path, $acl = 'public-read') {
        $key = basename($file_Path);
        $result = $this->s3->putObject([
            'Bucket'     => $this->bucket_name,
            'Key'        => $key,
            'SourceFile' => $file_Path,
			'ACL'        => $acl,
        ]);
        return $result['ObjectURL'];
    }

    public function multipleUpload($file_Path, $acl = 'public-read') {
        $urls = [];

        foreach ($filePaths as $file_Path) {
            $key = basename($file_Path);
            $result = $this->s3->putObject([
                'Bucket'     => $this->bucket_name,
                'Key'        => $key,
                'SourceFile' => $file_Path,
                'ACL'        => $acl,
            ]);
            $urls[] = $result['ObjectURL'];
        }

        return $urls;
    }

    public function bucketList() {
        $result = $this->s3->listBuckets();
        return $result;
    }
}
