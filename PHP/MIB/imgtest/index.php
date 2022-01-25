<?php
session_start();

echo "Attempting to save http://m.c.lnkd.licdn.com/mpr/mprx/0_M3jGd3EYNfy8qQSXJG2gd_I1NosSN82XU_dgdCo3wumgwXRkzFDabGYfsFVtcbDHZG0p6LNTs6I8";
copy("http://m.c.lnkd.licdn.com/mpr/mprx/0_M3jGd3EYNfy8qQSXJG2gd_I1NosSN82XU_dgdCo3wumgwXRkzFDabGYfsFVtcbDHZG0p6LNTs6I8", "imgs/test.png");
?>