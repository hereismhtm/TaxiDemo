<?php

// Framework Globals Functions

/**
 * Clean inputs.
 * @param string/array $data
 * @return string/array
 */
function ci($data)
{
    if (is_array( $data )) {
        $a = array();
        foreach ($data as $obj) {
            $a[] = ci($obj);
        }
        return $a;
    } else {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
}

/**
 * Generate URL link.
 * @param string $page
 * @param boolean/array $withvals
 * @return string
 */
function cd($page=null, $withvals=false)
{
    if (!isset($page)) {
        $page = APP_URL;
    } else {
        if (strpos($page, '~') === 0) {
            $page = APP_URL. SEP. RES_DIR. SEP. substr($page, 1);
        } else if ((strpos($_SERVER['REQUEST_URI'], '.php') !== false)) {
            $page = APP_URL. SEP. basename($_SERVER['SCRIPT_NAME']). SEP. $page;
        } else
            $page = APP_URL. SEP. '?'. ROUTE_VAR. '='. $page;
    }

    if ($withvals !== false) {
        if ($withvals === true) {
            $page = data::gluedUrl($page);
        }
        if (is_array($withvals)) {
            $page = data::gluedUrl($page, $withvals);
        }
    }

    return $page;
}

/**
 * Change direction to link.
 * @param string $page
 * @param boolean $withvals
 * @return void
 */
function dirto($page='./', $withvals=true)
{
    if ($withvals === true) {
        $page = data::gluedUrl($page);
    }
    if (is_array($withvals)) {
        $page = data::gluedUrl($page, $withvals);
    }

    load::$unit->eventlog();
    header('Location:'. $page);
    die();
}

/**
 * @deprecated
 */
function error($code, $show_view=false)
{
    httpsta($code, $show_view);
}

/**
 * Output HTTP status message.
 * @param string $number
 * @param boolean $show_view
 * @return string
 */
function httpsta($code, $show_view=false)
{
    if (!isset($code)) die();

    if (ob_get_length()) ob_clean();
    if ($show_view === true) {
        $show_view = ROOT. APP_DIR. '/views/_httpstatuses/'. $code. '.html';
        if (file_exists($show_view)) {
            include $show_view;
        } else {
            load::error('Error', 'No view file for this HTTP status code', $code);
        }
    } else {
        switch ($code) {
            case 100: $message = 'Continue'; break;
            case 101: $message = 'Switching Protocols'; break;
            case 102: $message = 'Processing'; break;

            case 200: $message = 'OK'; break;
            case 201: $message = 'Created'; break;
            case 202: $message = 'Accepted'; break;
            case 203: $message = 'Non-authoritative Information'; break;
            case 204: $message = 'No Content'; break;
            case 205: $message = 'Reset Content'; break;
            case 206: $message = 'Partial Content'; break;
            case 207: $message = 'Multi-Status'; break;
            case 208: $message = 'Already Reported'; break;
            case 226: $message = 'IM Used'; break;

            case 300: $message = 'Multiple Choices'; break;
            case 301: $message = 'Moved Permanently'; break;
            case 302: $message = 'Found'; break;
            case 303: $message = 'See Other'; break;
            case 304: $message = 'Not Modified'; break;
            case 305: $message = 'Use Proxy'; break;
            case 307: $message = 'Temporary Redirect'; break;
            case 308: $message = 'Permanent Redirect'; break;

            case 400: $message = 'Bad Request'; break;
            case 401: $message = 'Unauthorized'; break;
            case 402: $message = 'Payment Required'; break;
            case 403: $message = 'Forbidden'; break;
            case 404: $message = 'Not Found'; break;
            case 405: $message = 'Method Not Allowed'; break;
            case 406: $message = 'Not Acceptable'; break;
            case 407: $message = 'Proxy Authentication Required'; break;
            case 408: $message = 'Request Timeout'; break;
            case 409: $message = 'Conflict'; break;
            case 410: $message = 'Gone'; break;
            case 411: $message = 'Length Required'; break;
            case 412: $message = 'Precondition Failed'; break;
            case 413: $message = 'Payload Too Large'; break;
            case 414: $message = 'Request-URI Too Long'; break;
            case 415: $message = 'Unsupported Media Type'; break;
            case 416: $message = 'Requested Range Not Satisfiable'; break;
            case 417: $message = 'Expectation Failed'; break;
            case 418: $message = 'I\'m a teapot'; break;
            case 421: $message = 'Misdirected Request'; break;
            case 422: $message = 'Unprocessable Entity'; break;
            case 423: $message = 'Locked'; break;
            case 424: $message = 'Failed Dependency'; break;
            case 426: $message = 'Upgrade Required'; break;
            case 428: $message = 'Precondition Required'; break;
            case 429: $message = 'Too Many Requests'; break;
            case 431: $message = 'Request Header Fields Too Large'; break;
            case 444: $message = 'Connection Closed Without Response'; break;
            case 451: $message = 'Unavailable For Legal Reasons'; break;
            case 499: $message = 'Client Closed Request'; break;

            case 500: $message = 'Internal Server Error'; break;
            case 501: $message = 'Not Implemented'; break;
            case 502: $message = 'Bad Gateway'; break;
            case 503: $message = 'Service Unavailable'; break;
            case 504: $message = 'Gateway Timeout'; break;
            case 505: $message = 'HTTP Version Not Supported'; break;
            case 506: $message = 'Variant Also Negotiates'; break;
            case 507: $message = 'Insufficient Storage'; break;
            case 508: $message = 'Loop Detected'; break;
            case 510: $message = 'Not Extended'; break;
            case 511: $message = 'Network Authentication Required'; break;
            case 599: $message = 'Network Connect Timeout Error'; break;

            default: $message = 'N/A';
        }

        if ($message == 'N/A') {
            $status = 'Not Applicable!';
        } else {
            http_response_code($code);
            $status = substr($code, 0, 1);
            switch ($status) {
                case 1: $status = 'Informational'; break;
                case 2: $status = 'Success'; break;
                case 3: $status = 'Redirection'; break;
                case 4: $status = 'Client Error'; break;
                case 5: $status = 'Server Error'; break;
            }
        }
        echo '<h2>'. $status. '</h2>';
        echo '<p>HTTP status code <b>'. $code. '</b>: '. $message. '</p>';
        echo '<hr/><p><small>'. GENERATOR. '</small></p>';
    }
    die();
}
