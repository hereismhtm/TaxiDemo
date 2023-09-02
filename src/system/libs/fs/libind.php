<?php

final class FS
{
    /**
     * Save uploaded file from $_FILES[] array to specific location.
     * @param string $postedName
     * @param string $localPath
     * @param string $as
     * @return array [true/false, file http link]
     */
    public static function upfile_saveAs($postedName, $localPath, $as=null)
    {
        if (!isset($as)) {
            $as = basename($_FILES[$postedName]['name']);
        }

        if (strpos($as, '.') === false) {
            $e = explode('.', basename($_FILES[$postedName]['name']) );
            $ext = end($e);
            $as = $as. '.'. $ext;
        }
        $as = strtolower($as);

        if (strpos($localPath, '~') === 0) {
            $webPath = APP_URL. SEP. RES_DIR. SEP. substr($localPath, 1). SEP. $as;
            $localPath = ROOT. RES_DIR. SEP. substr($localPath, 1). SEP. $as;
        } else {
            $webPath = 'N/A';
            $localPath .= SEP. $as;
        }

        $status[0] = move_uploaded_file($_FILES[$postedName]['tmp_name'], $localPath);
        $status[1] = $webPath;
        return $status;
    }

    /**
     * Save base64 reperesentation as file to specific location.
     * @param string $base64
     * @param string $localPath
     * @param string $as
     * @return array [true/false, file http link]
     */
    public static function base64_saveAs($base64, $localPath, $as)
    {
        if (strpos($localPath, '~') === 0) {
            $webPath = APP_URL. SEP. RES_DIR. SEP. substr($localPath, 1). SEP. $as;
            $localPath = ROOT. RES_DIR. SEP. substr($localPath, 1). SEP. $as;
        } else {
            $webPath = 'N/A';
            $localPath .= SEP. $as;
        }

        $status[0] = (file_put_contents($localPath, base64_decode($base64)) !== false);
        $status[1] = $webPath;
        return $status;
    }
}
