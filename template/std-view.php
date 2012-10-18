<?php

if ($login_user===NULL and $page!='login') gotoP('login');

Outputer::links();
Outputer::blocks();
Outputer::menu();

?>
++PAGE++

</div></div></div></div>

<!-- Dörfer -->
<?php Outputer::dorfer(); ?>

</div>

<?php Outputer::lager(); ?>