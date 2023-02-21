<?php
/**
 * Redirects user to specified url.
 * 
 * @param string $url
 * @param int $status_code
 */
function redirect($url, $status_code=303)
{
    switch($status_code)
    {
        case 404:{

        } break;
        case 500:{

        } break;
        default: {
            header('Location: ' . $url, true, $status_code);
        } break;
    }
    die();
}
?>