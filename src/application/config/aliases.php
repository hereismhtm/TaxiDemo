<?php
/*
 Say you want page like:    ?cd=users/new
 "users" is a Staticy_Unit class and "new" is class's method,
 but PHP syntax dose not accept that because "new" is a reserved keyword,
 so you can change method name for example to "new_user" in that class
 and here put the following "if" statement:

if ($GLOBALS['_sfw_route']['unit'] == 'users' && $GLOBALS['_sfw_route']['action'] == 'new') {
    $GLOBALS['_sfw_route']['action'] = 'new_user';
}
*/
