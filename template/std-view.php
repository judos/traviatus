<?php

if ($login_user===NULL and $page!='login') gotoP('login');

Outputer::links();
Outputer::blocks();
Outputer::menu();


?>

++PAGE++
<?php
//Diese divs werden irgendwo im template.html ge�ffnet und werden hier geschlossen
?>
</div></div></div></div>

<!-- D�rfer -->
<?php Outputer::dorfer(); ?>

</div>

<?php Outputer::lager(); ?>