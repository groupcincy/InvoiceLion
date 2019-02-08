<?php
if (empty($_SESSION['user']['tenant_id'])) {
    if (explode('/',Router::getView()?:'/')[1]!='auth') {
        $url = urlencode(trim(Router::getRequest(),'/'));
        Router::redirect('auth/login?url='.$url);
    }
}
