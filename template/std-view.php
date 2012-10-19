<?php

if ($login_user===NULL and $page!='login') gotoP('login');

Outputer::links();
Outputer::blocks();
Outputer::menu();


?>

++PAGE++
<?php
//Diese divs werden irgendwo im template.html geöffnet und werden hier geschlossen
?>
</div></div></div></div>

<!-- Dörfer -->
<?php Outputer::dorfer(); ?>

</div>

<?php Outputer::lager(); ?>