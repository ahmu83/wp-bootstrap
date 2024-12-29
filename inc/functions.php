<?php
/**
 * Define a constant if it is not already defined.
 *
 * @param  string $constant Constant name.
 * @param  mixed  $value    Constant value.
 * @return void
 */
function wpb_maybe_define($constant, $value) {

  defined($constant) || define($constant, $value);

}

