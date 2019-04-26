<ul class="tabs">
    <li <?php if(substr(Router::getRequest(),0,17)==='/settings/general') e('class=active'); ?>><a href="/settings/general/view">General <span>settings</span></a></li>
    <li <?php if(substr(Router::getRequest(),0,17)==='/settings/company') e('class=active'); ?>><a href="/settings/company/view">Company <span>settings</span></a></li>
    <li <?php if(substr(Router::getRequest(),0,17)==='/settings/invoice') e('class=active'); ?>><a href="/settings/invoice/view">Invoice <span>settings</span></a></li>
</ul>

