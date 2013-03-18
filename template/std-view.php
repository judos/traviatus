<?php

if ($page!='login')
	needed_login();

add_javascript('keyhit');

Outputer::links();

Outputer::blocks();
Outputer::menu();
add_link('phpMyAdmin','/phpmyadmin/',array('target'=>'_blank'));

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